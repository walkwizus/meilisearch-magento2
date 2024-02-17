<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchInstantSearch\ViewModel;

use Walkwizus\MeilisearchBase\Helper\ServerSettings;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\Format;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Swatches\Helper\Data;
use Walkwizus\MeilisearchBase\Helper\Data as MeilisearchHelper;
use Walkwizus\MeilisearchBase\SearchAdapter\SearchIndexNameResolver;
use Magento\Store\Model\StoreManagerInterface;
use Walkwizus\MeilisearchBase\Index\AttributeProvider;
use Walkwizus\MeilisearchCatalog\Service\GetCurrentCategoryService;

class Config implements ArgumentInterface
{
    /**
     * @var ServerSettings
     */
    protected ServerSettings $serverSettings;

    /**
     * @var MeilisearchHelper
     */
    protected MeilisearchHelper $meilisearchHelper;

    /**
     * @var SearchIndexNameResolver
     */
    protected SearchIndexNameResolver $searchIndexNameResolver;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var AttributeProvider
     */
    protected AttributeProvider $attributeProvider;

    /**
     * @var Format
     */
    protected Format $localeFormat;

    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var AttributeCollectionFactory
     */
    protected AttributeCollectionFactory $attributeCollectionFactory;

    /**
     * @var Data
     */
    protected Data $swatchesHelper;

    /**
     * @var GetCurrentCategoryService
     */
    protected GetCurrentCategoryService $getCurrentCategoryService;

    /**
     * @param ServerSettings $serverSettings
     * @param MeilisearchHelper $meilisearchHelper
     * @param SearchIndexNameResolver $searchIndexNameResolver
     * @param StoreManagerInterface $storeManager
     * @param AttributeProvider $attributeProvider
     * @param Format $localeFormat
     * @param Session $customerSession
     * @param AttributeCollectionFactory $attributeCollectionFactory
     * @param Data $swatchesHelper
     * @param GetCurrentCategoryService $getCurrentCategoryService
     */
    public function __construct(
        ServerSettings $serverSettings,
        MeilisearchHelper $meilisearchHelper,
        SearchIndexNameResolver $searchIndexNameResolver,
        StoreManagerInterface $storeManager,
        AttributeProvider $attributeProvider,
        Format $localeFormat,
        Session $customerSession,
        AttributeCollectionFactory $attributeCollectionFactory,
        Data $swatchesHelper,
        GetCurrentCategoryService $getCurrentCategoryService
    ) {
        $this->serverSettings = $serverSettings;
        $this->meilisearchHelper = $meilisearchHelper;
        $this->searchIndexNameResolver = $searchIndexNameResolver;
        $this->storeManager = $storeManager;
        $this->attributeProvider = $attributeProvider;
        $this->localeFormat = $localeFormat;
        $this->customerSession = $customerSession;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->swatchesHelper = $swatchesHelper;
        $this->getCurrentCategoryService = $getCurrentCategoryService;
    }

    /**
     * @return MeilisearchHelper
     */
    public function getHelper(): MeilisearchHelper
    {
        return $this->meilisearchHelper;
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
        $attributes = $this->attributeProvider->getFilterableAttributes('catalog_product');

        if (count($attributes) > 0) {
            return array_filter($attributes, function($el) {
                return strpos($el, '_value');
            });
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

        $attributes = $this->attributeCollectionFactory->create()
            ->addFieldToFilter('attribute_code', ['in' => $attributeCodes]);
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
            return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . '/catalog/product';
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
     * @return int|null
     */
    public function getCurrentCategoryId()
    {
        return $this->getCurrentCategoryService->getCategoryId();
    }
}
