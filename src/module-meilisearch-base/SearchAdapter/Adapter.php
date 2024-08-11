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
     * @var Meilisearch
     */
    private Meilisearch $client;

    /**
     * @var Mapper
     */
    private Mapper $mapper;

    /**
     * @var ResponseFactory
     */
    private ResponseFactory $responseFactory;

    /**
     * @var AggregationBuilder
     */
    private AggregationBuilder $aggregationBuilder;

    /**
     * @var QueryContainerFactory
     */
    private QueryContainerFactory $queryContainerFactory;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Meilisearch $client
     * @param Mapper $mapper
     * @param ResponseFactory $responseFactory
     * @param AggregationBuilder $aggregationBuilder
     * @param QueryContainerFactory $queryContainerFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Meilisearch $client,
        Mapper $mapper,
        ResponseFactory $responseFactory,
        AggregationBuilder $aggregationBuilder,
        QueryContainerFactory $queryContainerFactory,
        LoggerInterface $logger
    ) {
        $this->client = $client;
        $this->mapper = $mapper;
        $this->responseFactory = $responseFactory;
        $this->aggregationBuilder = $aggregationBuilder;
        $this->queryContainerFactory = $queryContainerFactory;
        $this->logger = $logger;
    }

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
