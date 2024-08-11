<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Model;

use Walkwizus\MeilisearchBase\Index\AttributeProvider;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Catalog\Api\Data\ProductAttributeInterface;

class AttributeRuleProvider
{
    /**
     * @var array
     */
    private array $excludedAttributes = [
        'id',
        'type_id',
        'stock',
    ];

    private array $additionalAttributes = [
        'price',
    ];

    /**
     * @var AttributeProvider
     */
    private AttributeProvider $attributeProvider;

    /**
     * @var AttributeCollectionFactory
     */
    private AttributeCollectionFactory $attributeCollectionFactory;

    /**
     * @param AttributeProvider $attributeProvider
     * @param AttributeCollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        AttributeProvider $attributeProvider,
        AttributeCollectionFactory $attributeCollectionFactory
    ) {
        $this->attributeProvider = $attributeProvider;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        $filterableAttributes = $this->attributeProvider->getFilterableAttributes('catalog_product');

        $filteredAttributes = array_filter($filterableAttributes, function ($k) {
            return !str_contains($k, "_value") &&
                !str_starts_with($k, "price_") &&
                !in_array($k, $this->excludedAttributes, true);
        });

        $attributeCollection = $this->attributeCollectionFactory
            ->create()
            ->addFieldToFilter('attribute_code', ['in' => array_merge(array_values($filteredAttributes), $this->additionalAttributes)]);

        $attributes = [];
        /** @var ProductAttributeInterface $attribute */
        foreach ($attributeCollection as $attribute) {
            $attributes[] = [
                'code' => $attribute->getAttributeCode(),
                'type' => $attribute->getFrontendInput(),
                'label' => $attribute->getDefaultFrontendLabel()
            ];
        }

        return $attributes;
    }
}
