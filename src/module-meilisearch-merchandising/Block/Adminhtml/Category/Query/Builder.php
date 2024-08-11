<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Block\Adminhtml\Category\Query;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Walkwizus\MeilisearchMerchandising\Service\QueryBuilderService;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\UrlInterface;

class Builder extends Template
{
    /**
     * @var QueryBuilderService
     */
    private QueryBuilderService $queryBuilderService;

    /**
     * @param Context $context
     * @param QueryBuilderService $queryBuilderService
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        Template\Context $context,
        QueryBuilderService $queryBuilderService,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        $this->queryBuilderService = $queryBuilderService;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    /**
     * @return string
     */
    public function getSaveRuleUrl(): string
    {
        return $this->getUrl('meilisearch_merchandising/category/ajax_saverule');
    }

    /**
     * @return string
     */
    public function getDeleteRuleUrl(): string
    {
        return $this->getUrl('meilisearch_merchandising/category/ajax_deleterule');
    }

    /**
     * @return string
     */
    public function getPreviewUrl(): string
    {
        return $this->getUrl('meilisearch_merchandising/category/ajax_preview');
    }

    /**
     * @return string
     */
    public function getProductChooserUrl(): string
    {
        return $this->getUrl('meilisearch_merchandising/category_ajax/chooser_sku');
    }

    /**
     * @return string
     */
    public function getCategoryChooserUrl(): string
    {
        return $this->getUrl('meilisearch_merchandising/category_ajax/chooser_category');
    }

    /**
     * @return string
     */
    public function getPromoteProductUrl(): string
    {
        return $this->getUrl('meilisearch_merchandising/category_ajax/promote');
    }

    /**
     * @return mixed
     */
    public function getStoreId(): mixed
    {
        return $this->getRequest()->getParam('store', false);
    }

    /**
     * @return false|string
     * @throws NoSuchEntityException
     */
    public function getFilters(): bool|string
    {
        return json_encode($this->queryBuilderService->convertAttributesToRules());
    }

    /**
     * @return string
     */
    public function getProductMediaUrl(): string
    {
        try {
            return $this->_storeManager->getStore($this->getStoreId())->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'catalog/product';
        } catch (\Exception $e) {
            return '';
        }
    }
}
