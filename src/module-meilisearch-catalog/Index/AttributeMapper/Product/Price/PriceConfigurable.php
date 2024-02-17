<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchCatalog\Index\AttributeMapper\Product\Price;

class PriceConfigurable implements PriceDataInterface
{
    /**
     * @param array $priceData
     * @return float
     */
    public function getPrice(array $priceData): float
    {
        return (float)$priceData['min_price'];
    }

    /**
     * @param array $priceData
     * @return float
     */
    public function getOriginalPrice(array $priceData): float
    {
        return (float)$priceData['max_price'];
    }
}
