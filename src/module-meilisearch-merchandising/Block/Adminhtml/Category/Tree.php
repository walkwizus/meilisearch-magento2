<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Block\Adminhtml\Category;

use Magento\Backend\Block\Template;
use Magento\Catalog\Api\CategoryManagementInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Tree extends Template
{
    /**
     * @var CategoryManagementInterface
     */
    protected CategoryManagementInterface $categoryManagement;

    /**
     * @param Template\Context $context
     * @param CategoryManagementInterface $categoryManagement
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        Template\Context $context,
        CategoryManagementInterface $categoryManagement,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        $this->categoryManagement = $categoryManagement;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public function getCategoryTree(): bool|string
    {
        $rootCategoryId = 2;
        try {
            $rootCategory = $this->categoryManagement->getTree($rootCategoryId);
            $jsTreeData = $this->convertToJsTreeFormat($rootCategory);
            return json_encode([$jsTreeData]);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @param $category
     * @return array
     */
    protected function convertToJsTreeFormat($category): array
    {
        $jsTreeFormat = [
            'text' => $category['name'],
            'id' => $category['id']
        ];

        if (!empty($category['children_data'])) {
            $jsTreeFormat['children'] = [];
            foreach ($category['children_data'] as $child) {
                $jsTreeFormat['children'][] = $this->convertToJsTreeFormat($child);
            }
        }

        return $jsTreeFormat;
    }

    /**
     * @return string
     */
    public function getAjaxUrl(): string
    {
        return $this->getUrl('meilisearch_merchandising/category/ajax_getrule');
    }
}
