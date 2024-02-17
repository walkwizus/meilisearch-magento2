<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchCatalog\Index\AttributeMapper\Product;

use Walkwizus\MeilisearchBase\Api\Index\AttributeMapperInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\Indexer\Category\Product\TableMaintainer;

class Category implements AttributeMapperInterface
{
    /**
     * @var ResourceConnection
     */
    protected ResourceConnection $resourceConnection;

    /**
     * @var TableMaintainer
     */
    protected TableMaintainer $tableMaintainer;

    /**
     * @param ResourceConnection $resourceConnection
     * @param TableMaintainer $tableMaintainer
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        TableMaintainer $tableMaintainer
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->tableMaintainer = $tableMaintainer;
    }

    /**
     * @param array $documentData
     * @param $storeId
     * @return array
     */
    public function map(array $documentData, $storeId): array
    {
        $documents = [];

        foreach ($documentData as $id => $indexData) {
            $categories = $this->getProductCategories($id, $storeId);
            foreach ($categories as $category) {
                $documents[$id]['category_ids'][] = $category['category_id'];
                $documents[$id]['position_category_' . $category['category_id']] = $category['position'] ?? 0;
            }
        }

        return $documents;
    }

    /**
     * @param $productId
     * @param $storeId
     * @return array
     */
    protected function getProductCategories($productId, $storeId): array
    {
        $select = $this->resourceConnection->getConnection()
            ->select()
            ->from(['cpi' => $this->getCategoryProductIndexTable($storeId)])
            ->where('cpi.store_id = ?', $storeId)
            ->where('cpi.product_id = ?', $productId);

        return $this->resourceConnection->getConnection()->fetchAll($select);
    }

    /**
     * @param $storeId
     * @return string
     */
    protected function getCategoryProductIndexTable($storeId): string
    {
        return $this->tableMaintainer->getMainTable((int)$storeId);
    }
}
