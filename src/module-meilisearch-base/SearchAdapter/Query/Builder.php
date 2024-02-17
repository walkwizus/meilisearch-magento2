<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter\Query;

use Walkwizus\MeilisearchBase\SearchAdapter\SearchIndexNameResolver;
use Walkwizus\MeilisearchBase\SearchAdapter\Query\Builder\Aggregation as AggregationBuilder;
use Walkwizus\MeilisearchBase\SearchAdapter\Query\Builder\Sort;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Search\RequestInterface;

class Builder
{
    /**
     * Default Page Size
     */
    private const MEILISEARCH_DEFAULT_PAGE_SIZE = 1000;

    /**
     * @var SearchIndexNameResolver
     */
    protected SearchIndexNameResolver $searchIndexNameResolver;

    /**
     * @var AggregationBuilder
     */
    protected AggregationBuilder $aggregationBuilder;

    /**
     * @var Sort
     */
    protected Sort $sort;

    /**
     * @var ScopeResolverInterface
     */
    protected ScopeResolverInterface $scopeResolver;

    /**
     * @param SearchIndexNameResolver $searchIndexNameResolver
     * @param AggregationBuilder $aggregationBuilder
     * @param ScopeResolverInterface $scopeResolver
     */
    public function __construct(
        SearchIndexNameResolver $searchIndexNameResolver,
        AggregationBuilder $aggregationBuilder,
        Sort $sort,
        ScopeResolverInterface $scopeResolver
    ) {
        $this->searchIndexNameResolver = $searchIndexNameResolver;
        $this->aggregationBuilder = $aggregationBuilder;
        $this->sort = $sort;
        $this->scopeResolver = $scopeResolver;
    }

    /**
     * Set initial settings for query
     *
     * @param RequestInterface $request
     * @return array
     */
    public function initQuery(RequestInterface $request): array
    {
        $dimension = current($request->getDimensions());
        $storeId = $this->scopeResolver->getScope($dimension->getValue())->getId();

        return [
            'index' => $this->searchIndexNameResolver->getIndexName($storeId, $request->getIndex()),
            'offset' => min(self::MEILISEARCH_DEFAULT_PAGE_SIZE, $request->getFrom()),
            'limit' => $request->getSize(),
            'sort' => $this->sort->getSort($request),
            'query' => [],
        ];
    }

    /**
     * Add aggregations settings to query
     *
     * @param RequestInterface $request
     * @param array $searchQuery
     * @return array
     */
    public function initAggregations(RequestInterface $request, array $searchQuery): array
    {
        return $this->aggregationBuilder->build($request, $searchQuery);
    }
}
