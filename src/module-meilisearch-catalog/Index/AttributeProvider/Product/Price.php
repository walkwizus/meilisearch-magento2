<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchCatalog\Index\AttributeProvider\Product;

use Walkwizus\MeilisearchBase\Api\Index\AttributeProviderInterface;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;

class Price implements AttributeProviderInterface
{
    /**
     * @var GroupCollectionFactory
     */
    protected GroupCollectionFactory $groupCollectionFactory;

    /**
     * @param GroupCollectionFactory $groupCollectionFactory
     */
    public function __construct(GroupCollectionFactory $groupCollectionFactory)
    {
        $this->groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * @return array
     */
    public function getFilterableAttributes(): array
    {
        $prices = [];
        $groups = $this->groupCollectionFactory
            ->create()
            ->toArray();

        foreach ($groups['items'] as $group) {
            $prices[] = 'price_' . $group['customer_group_id'];
        }

        return $prices;
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
        $prices = [];
        $groups = $this->groupCollectionFactory
            ->create()
            ->toArray();

        foreach ($groups['items'] as $group) {
            $prices[] = 'price_' . $group['customer_group_id'];
        }

        return $prices;
    }
}
