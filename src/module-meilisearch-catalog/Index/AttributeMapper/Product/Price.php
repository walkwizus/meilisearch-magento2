<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchCatalog\Index\AttributeMapper\Product;

use Walkwizus\MeilisearchBase\Api\Index\AttributeMapperInterface;
use Magento\AdvancedSearch\Model\ResourceModel\Index;
use Walkwizus\MeilisearchCatalog\Index\AttributeMapper\Product\Price\PriceDataInterface;

class Price implements AttributeMapperInterface
{
    /**
     * @var Index
     */
    protected Index $index;

    /**
     * @var PriceDataInterface[]
     */
    protected array $priceReaderPool;

    /**
     * @param Index $index
     * @param array $priceReaderPool
     */
    public function __construct(
        Index $index,
        array $priceReaderPool = []
    ) {
        $this->index = $index;
        $this->priceReaderPool = $priceReaderPool;
    }

    /***
     * @param array $documentData
     * @param $storeId
     * @return array
     */
    public function map(array $documentData, $storeId): array
    {
        $productIds = array_keys($documentData);
        $priceIndexData = $this->index->getPriceIndexData($productIds, $storeId);
        $data = [];

        foreach ($productIds as $productId) {
            $data[$productId] = $this->getProductPriceData($productId, $priceIndexData);
        }

        return $data;
    }

    /**
     * @param $productId
     * @param array $priceIndexData
     * @return array
     */
    protected function getProductPriceData($productId, array $priceIndexData): array
    {
        $result = [];
        if (array_key_exists($productId, $priceIndexData)) {
            $productPriceIndexData = $priceIndexData[$productId];
            foreach ($productPriceIndexData as $customerGroupId => $price) {
                $result['price_' . $customerGroupId] = (float)sprintf('%F', $price);
            }
        }

        return $result;
    }
}
