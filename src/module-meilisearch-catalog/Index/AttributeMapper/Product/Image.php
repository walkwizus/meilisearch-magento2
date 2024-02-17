<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchCatalog\Index\AttributeMapper\Product;

use Walkwizus\MeilisearchBase\Api\Index\AttributeMapperInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ResourceConnection;
use Magento\Eav\Model\Config as EavConfig;

class Image implements AttributeMapperInterface
{
    const IMAGE_ATTRIBUTE = 'image';

    /**
     * @var ResourceConnection
     */
    protected ResourceConnection $resourceConnection;

    /**
     * @var EavConfig
     */
    protected EavConfig $eavConfig;

    /**
     * @param ResourceConnection $resourceConnection
     * @param EavConfig $eavConfig
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        EavConfig $eavConfig
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param array $documentData
     * @param $storeId
     * @return array
     * @throws LocalizedException
     */
    public function map(array $documentData, $storeId): array
    {
        $documents = [];
        foreach ($documentData as $id => $indexData) {
            $documents[$id]['image'] = $this->getProductImage($id);
        }

        return $documents;
    }

    /**
     * @param $productId
     * @return string
     * @throws LocalizedException
     */
    protected function getProductImage($productId): string
    {
        $connection = $this->resourceConnection->getConnection();

        $entityTypeId = $this->eavConfig
            ->getEntityType(ProductAttributeInterface::ENTITY_TYPE_CODE)
            ->getEntityTypeId();

        $select = $connection
            ->select()
            ->from(['main_table' => $connection->getTableName('catalog_product_entity_varchar')], ['main_table.value'])
            ->join(
                ['eav_attribute' => $connection->getTableName('eav_attribute')],
                'main_table.attribute_id = eav_attribute.attribute_id',
                []
            )
            ->where('entity_id = ?', $productId)
            ->where('eav_attribute.entity_type_id = ?', $entityTypeId)
            ->where('eav_attribute.attribute_code = "' . self::IMAGE_ATTRIBUTE . '"');

        return $connection->fetchOne($select);
    }
}
