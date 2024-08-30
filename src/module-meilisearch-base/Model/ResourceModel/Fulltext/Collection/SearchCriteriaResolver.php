<?php

namespace Walkwizus\MeilisearchBase\Model\ResourceModel\Fulltext\Collection;

use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection\SearchCriteriaResolverInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchCriteria;

class SearchCriteriaResolver implements SearchCriteriaResolverInterface
{
    /**
     * SearchCriteriaResolver constructor.
     * @param SearchCriteriaBuilder $builder
     * @param string $searchRequestName
     * @param int $currentPage
     * @param int $size
     * @param array|null $orders
     */
    public function __construct(
        private SearchCriteriaBuilder $builder,
        private string $searchRequestName,
        private int $currentPage,
        private int $size,
        private ?array $orders
    ) { }

    /**
     * @inheritdoc
     */
    public function resolve(): SearchCriteria
    {
        $searchCriteria = $this->builder->create();
        $searchCriteria->setRequestName($this->searchRequestName);
        $searchCriteria->setSortOrders($this->orders);
        $searchCriteria->setCurrentPage($this->currentPage - 1);
        if ($this->size) {
            $searchCriteria->setPageSize($this->size);
        }

        return $searchCriteria;
    }
}
