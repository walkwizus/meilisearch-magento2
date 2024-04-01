<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchInstantSearch\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Walkwizus\MeilisearchBase\Helper\ServerSettings;
use Walkwizus\MeilisearchBase\Helper\Data as MeilisearchHelper;
use Walkwizus\MeilisearchBase\SearchAdapter\SearchIndexNameResolver;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Locale\Format;
use Magento\Customer\Model\Session;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Swatches\Helper\Data;
use Walkwizus\MeilisearchMerchandising\Api\CategoryRepositoryInterface;
use Walkwizus\MeilisearchMerchandising\Api\FacetRepositoryInterface;
use Walkwizus\MeilisearchMerchandising\Model\ResourceModel\FacetAttribute\CollectionFactory as FacetAttributeCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Walkwizus\MeilisearchMerchandising\Service\QueryBuilderService;

class Config implements ArgumentInterface
{
    /**
     * @param ServerSettings $serverSettings
     * @param MeilisearchHelper $meilisearchHelper
     * @param SearchIndexNameResolver $searchIndexNameResolver
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Format $localeFormat
     * @param Session $customerSession
     * @param AttributeCollectionFactory $attributeCollectionFactory
     * @param Data $swatchesHelper
     * @param FacetRepositoryInterface $facetRepository
     * @param FacetAttributeCollectionFactory $facetAttributeCollectionFactory
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        private ServerSettings $serverSettings,
        private MeilisearchHelper $meilisearchHelper,
        private SearchIndexNameResolver $searchIndexNameResolver,
        private ScopeConfigInterface $scopeConfig,
        private StoreManagerInterface $storeManager,
        private Format $localeFormat,
        private Session $customerSession,
        private AttributeCollectionFactory $attributeCollectionFactory,
        private Data $swatchesHelper,
        private FacetRepositoryInterface $facetRepository,
        private FacetAttributeCollectionFactory $facetAttributeCollectionFactory,
        private CategoryRepositoryInterface $categoryRepository,
        private QueryBuilderService $queryBuilderService
    ) { }

    /**
     * @return MeilisearchHelper
     */
    public function getHelper(): MeilisearchHelper
    {
        return $this->meilisearchHelper;
    }

    /**
     * @param $categoryId
     * @return false|string
     */
    public function isMerchandisingCategory($categoryId): bool|string
    {
        try {
            $categoryMerchandising = $this->categoryRepository->getByCategoryId($categoryId);
        } catch (NoSuchEntityException $noSuchEntityException) {
            return false;
        }

        return $this->queryBuilderService->convertRulesToMeilisearchQuery(json_decode($categoryMerchandising->getQuery(), true));
    }

    /**
     * @return ServerSettings
     */
    public function getServerSettingsHelper(): ServerSettings
    {
        return $this->serverSettings;
    }

    /**
     * @param $indexerId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getIndexName($indexerId): string
    {
        $storeId = $this->storeManager->getStore()->getId();
        return $this->searchIndexNameResolver->getIndexName($storeId, $indexerId);
    }

    /**
     * @return array
     */
    public function getRefinementList(): array
    {
        try {
            $facet = $this->facetRepository->getByIndex('catalog_product');
            $facetAttributeCollection = $this->facetAttributeCollectionFactory
                ->create()
                ->addFieldToFilter('facet_id', $facet->getId())
                ->setOrder('position', 'ASC');
            if ($facetAttributeCollection->getSize() > 0) {
                return $facetAttributeCollection->toArray()['items'];
            }
        } catch (\Exception $e) {

        }

        return [];
    }

    /**
     * @return array
     */
    public function getSwatches(): array
    {
        $swatches = [];

        $attributeCodes = array_map(function($attributeCode) {
            return str_replace('_value', '', $attributeCode);
        }, $this->getRefinementList());

        $attributes = $this->getAttributeByCodes($attributeCodes);
        foreach ($attributes as $attribute) {
            if ($this->swatchesHelper->isSwatchAttribute($attribute)) {
                foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                    $swatch = $this->swatchesHelper->getSwatchesByOptionsId([$option['value']]);
                    $swatches[$attribute->getAttributeCode()][$option['label']] = $swatch[$option['value']] ?? false;
                }
            }
        }

        return $swatches;
    }

    /**
     * @return string
     */
    public function getProductMediaUrl(): string
    {
        try {
            return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'catalog/product';
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * @return mixed|string
     */
    public function getProductUrlSuffix(): mixed
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
            return $this->meilisearchHelper->getProductUrlSuffix($storeId);
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);
    }

    /**
     * @return array
     */
    public function getPriceFormat(): array
    {
        return $this->localeFormat->getPriceFormat();
    }

    /**
     * @return int
     */
    public function getCustomerGroupId(): int
    {
        return $this->customerSession->isLoggedIn()
            ? $this->customerSession->getCustomer()->getGroupId()
            : GroupInterface::NOT_LOGGED_IN_ID;
    }

    /**
     * @param string $viewMode
     * @return int
     */
    public function getHitPerPage(string $viewMode = 'grid'): int
    {
        $perPageConfigPath = 'catalog/frontend/' . $viewMode . '_per_page';
        return (int)$this->scopeConfig->getValue($perPageConfigPath, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param array $attributeCodes
     * @return AttributeCollection
     */
    protected function getAttributeByCodes(array $attributeCodes): AttributeCollection
    {
        return $this->attributeCollectionFactory
            ->create()
            ->addFieldToFilter('attribute_code', ['in' => $attributeCodes]);
    }
}
