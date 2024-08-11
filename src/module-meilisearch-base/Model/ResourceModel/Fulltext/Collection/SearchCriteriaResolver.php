<?php

namespace Walkwizus\MeilisearchBase\Model\ResourceModel\Fulltext\Collection;

use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection\SearchCriteriaResolverInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchCriteria;

class SearchCriteriaResolver implements SearchCriteriaResolverInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $builder;

    /**
     * @var string
     */
    private string $searchRequestName;

    /**
     * @var int
     */
    private int $currentPage;

    /**
     * @var int
     */
    private int $size;

    /**
     * @var array|null
     */
    private ?array $orders;

    /**
     * @param SearchCriteriaBuilder $builder
     * @param string $searchRequestName
     * @param int $currentPage
     * @param int $size
     * @param array|null $orders
     */
    public function __construct(
        SearchCriteriaBuilder $builder,
        string $searchRequestName,
        int $currentPage,
        int $size,
        ?array $orders
    ) {
        $this->builder = $builder;
        $this->searchRequestName = $searchRequestName;
        $this->currentPage = $currentPage;
        $this->size = $size;
        $this->orders = $orders;
    }

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
