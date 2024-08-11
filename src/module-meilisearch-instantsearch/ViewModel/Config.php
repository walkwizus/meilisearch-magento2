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
     * @var ServerSettings
     */
    private ServerSettings $serverSettings;

    /**
     * @var MeilisearchHelper
     */
    private MeilisearchHelper $meilisearchHelper;

    /**
     * @var SearchIndexNameResolver
     */
    private SearchIndexNameResolver $searchIndexNameResolver;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var Format
     */
    private Format $localeFormat;

    /**
     * @var Session
     */
    private Session $customerSession;

    /**
     * @var AttributeCollectionFactory
     */
    private AttributeCollectionFactory $attributeCollectionFactory;

    /**
     * @var Data
     */
    private Data $swatchesHelper;

    /**
     * @var FacetRepositoryInterface
     */
    private FacetRepositoryInterface $facetRepository;

    /**
     * @var FacetAttributeCollectionFactory
     */
    private FacetAttributeCollectionFactory $facetAttributeCollectionFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @var QueryBuilderService
     */
    private QueryBuilderService $queryBuilderService;

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
     * @param QueryBuilderService $queryBuilderService
     */
    public function __construct(
        ServerSettings $serverSettings,
        MeilisearchHelper $meilisearchHelper,
        SearchIndexNameResolver $searchIndexNameResolver,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Format $localeFormat,
        Session $customerSession,
        AttributeCollectionFactory $attributeCollectionFactory,
        Data $swatchesHelper,
        FacetRepositoryInterface $facetRepository,
        FacetAttributeCollectionFactory $facetAttributeCollectionFactory,
        CategoryRepositoryInterface $categoryRepository,
        QueryBuilderService $queryBuilderService
    ) {
        $this->serverSettings = $serverSettings;
        $this->meilisearchHelper = $meilisearchHelper;
        $this->searchIndexNameResolver = $searchIndexNameResolver;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->localeFormat = $localeFormat;
        $this->customerSession = $customerSession;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->swatchesHelper = $swatchesHelper;
        $this->facetRepository = $facetRepository;
        $this->facetAttributeCollectionFactory = $facetAttributeCollectionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->queryBuilderService = $queryBuilderService;
    }

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
