<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchCatalog\Service;

use Walkwizus\MeilisearchBase\Index\AttributeProvider;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;
use Magento\Framework\App\State;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;

class GetRefinementListService
{
    /**
     * @param AttributeProvider $attributeProvider
     * @param AttributeCollectionFactory $attributeCollectionFactory
     * @param AttributeCollection $attributeCollection
     * @param State $state
     */
    public function __construct(
        private AttributeProvider $attributeProvider,
        private AttributeCollectionFactory $attributeCollectionFactory,
        private AttributeCollection $attributeCollection,
        private State $state
    ) { }

    /**
     * @param string $indexName
     * @return array
     * @throws LocalizedException
     */
    public function get(string $indexName): array
    {
        $filterableAttributes = $this->attributeProvider->getFilterableAttributes($indexName);

        if (count($filterableAttributes) == 0) {
            return [];
        }

        $attributesWithSuffix = array_filter($filterableAttributes, function($el) {
            return strpos($el, '_value');
        });

        $rawAttributes = array_map(function($attributeCode) {
            return str_replace('_value', '', $attributeCode);
        }, $attributesWithSuffix);

        $attributes = $this->getAttributeByCodes($rawAttributes);

        $refinementList = [];
        /** @var ProductAttributeInterface $attribute */
        foreach ($attributes as $attribute) {
            if ($this->state->getAreaCode() != Area::AREA_FRONTEND) {
                $refinementList[$attribute->getAttributeCode() . '_value'] = $attribute->getDefaultFrontendLabel();
            } else {
                $refinementList[$attribute->getAttributeCode() . '_value'] = $attribute->getStoreLabel();
            }
        }

        return $refinementList;
    }

    /**
     * @param array $attributeCodes
     * @return AttributeCollection
     */
    protected function getAttributeByCodes(array $attributeCodes): AttributeCollection
    {
        return $this->attributeCollectionFactory
            ->create()
            ->addFieldToFilter('attribute_code', ['in' => $attributeCodes]);
    }
}
