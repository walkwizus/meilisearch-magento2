<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchCatalog\Index\AttributeProvider\Product;

use Walkwizus\MeilisearchBase\Api\Index\AttributeProviderInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

class CategoryPosition implements AttributeProviderInterface
{
    /**
     * @var CategoryCollectionFactory
     */
    protected CategoryCollectionFactory $categoryCollectionFactory;

    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     */
    public function __construct(CategoryCollectionFactory $categoryCollectionFactory)
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @return array
     */
    public function getFilterableAttributes(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getSearchableAttributes(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getSortableAttributes(): array
    {
        $positionAttributes = [];

        $categories = $this->categoryCollectionFactory
            ->create()
            ->addFieldToSelect('entity_id');

        foreach ($categories as $category) {
            $positionAttributes[] = 'position_category_' . $category->getId();
        }

        return $positionAttributes;
    }
}
