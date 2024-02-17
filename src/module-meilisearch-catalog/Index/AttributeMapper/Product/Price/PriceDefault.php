<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchCatalog\Index\AttributeMapper\Product\Price;

class PriceDefault implements PriceDataInterface
{
    /**
     * @param array $priceData
     * @return float
     */
    public function getPrice(array $priceData): float
    {
        return (float)$priceData['final_price'] ?? $priceData['price'] ?? 0;
    }

    /**
     * @param array $priceData
     * @return float
     */
    public function getOriginalPrice(array $priceData): float
    {
        return (float)$priceData['price'] ?? 0;
    }
}
