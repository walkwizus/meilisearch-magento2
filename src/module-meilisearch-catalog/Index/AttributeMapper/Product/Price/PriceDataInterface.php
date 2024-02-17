<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchCatalog\Index\AttributeMapper\Product\Price;

interface PriceDataInterface
{
    /**
     * @param array $priceData
     * @return float
     */
    public function getPrice(array $priceData): float;

    /**
     * @param array $priceData
     * @return float
     */
    public function getOriginalPrice(array $priceData): float;
}
