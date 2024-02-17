<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute\Interceptor;

class SearchableAttributes implements OptionSourceInterface
{
    /**
     * @var AttributeCollectionFactory
     */
    protected AttributeCollectionFactory $attributeCollectionFactory;

    /**
     * @param AttributeCollectionFactory $attributeCollectionFactory
     */
    public function __construct(AttributeCollectionFactory $attributeCollectionFactory)
    {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $filterableAttributes = [];

        $attributes = $this->attributeCollectionFactory->create()
            ->addIsSearchableFilter()
            ->addFieldToSelect('attribute_code');

        $attributes->getSelect()->order('search_weight DESC');

        foreach ($attributes as $attribute) {
            /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
            $filterableAttributes[] = [
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getName() . ' (Search Weight: ' . $attribute->getSearchWeight() . ')',
            ];
        }

        return $filterableAttributes;
    }
}
