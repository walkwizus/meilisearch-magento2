<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter;

use Magento\Framework\Search\AdapterInterface;
use Walkwizus\MeilisearchBase\Model\Adapter\Meilisearch;
use Walkwizus\MeilisearchBase\SearchAdapter\Aggregation\Builder as AggregationBuilder;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Response\QueryResponse;
use Psr\Log\LoggerInterface;

class Adapter implements AdapterInterface
{
    /**
     * @param Meilisearch $client
     * @param Mapper $mapper
     * @param ResponseFactory $responseFactory
     * @param AggregationBuilder $aggregationBuilder
     * @param QueryContainerFactory $queryContainerFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        private Meilisearch $client,
        private Mapper $mapper,
        private ResponseFactory $responseFactory,
        private AggregationBuilder $aggregationBuilder,
        private QueryContainerFactory $queryContainerFactory,
        private LoggerInterface $logger
    ) { }

    /**
     * @param RequestInterface $request
     * @return QueryResponse|mixed|void
     */
    public function query(RequestInterface $request)
    {
        $aggregationBuilder = $this->aggregationBuilder;
        $query = $this->mapper->buildQuery($request);
        $aggregationBuilder->setQuery($this->queryContainerFactory->create(['query' => $query]));

        $search = $query['query']['query']['query']['*']['query'] ?? '';

        $response = [];
        try {
            $searchResult = $this->client->search($query['index'], $search, [
                'offset' => (int)$query['offset'],
                'limit' => (int)$query['limit'],
                'facets' => ['*'],
                'filter' => $query['filter'],
                'sort' => $query['sort'],
            ]);
            $response = $searchResult->toArray();
            if (isset($response['facetDistribution'])) {
                $response['facetDistribution']['facetStats'] = $searchResult->getFacetStats();
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $this->responseFactory->create([
            'documents' => $response,
            'aggregations' => $aggregationBuilder->build($request, $response),
            'total' => $response['estimatedTotalHits'] ?? 0
        ]);
    }
}
