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
    private array $ajaxUrls = [
        'loadRule' => 'meilisearch_merchandising/category/ajax_getrule',
        'saveRule' => 'meilisearch_merchandising/category/ajax_saverule',
        'deleteRule' => 'meilisearch_merchandising/category/ajax_deleterule',
        'preview' => 'meilisearch_merchandising/category/ajax_preview',
        'promoteProduct' => 'meilisearch_merchandising/category_ajax/promote'
    ];

    /**
     * @param Context $context
     * @param QueryBuilderService $queryBuilderService
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        Template\Context $context,
        private QueryBuilderService $queryBuilderService,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getJsLayout()
    {
        $this->jsLayout['components']['categoryMerchandisingQueryBuilder']['config']['filters'] = $this->getFilters();
        $this->jsLayout['components']['categoryMerchandisingQueryBuilder']['config']['storeId'] = $this->getStoreId();
        $this->jsLayout['components']['categoryMerchandisingQueryBuilder']['config']['productMediaUrl'] = $this->getProductMediaUrl();

        foreach ($this->ajaxUrls as $key => $value) {
            $this->jsLayout['components']['categoryMerchandisingQueryBuilder']['config']['ajaxUrl'][$key] = $this->getUrl($value);
        }

        return parent::getJsLayout();
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
