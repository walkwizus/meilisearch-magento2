<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter;

use Magento\Framework\Search\AdapterInterface;
use Walkwizus\MeilisearchBase\SearchAdapter\Aggregation\Builder as AggregationBuilder;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Response\QueryResponse;
use Psr\Log\LoggerInterface;

class Adapter implements AdapterInterface
{
    /**
     * @var ConnectionManager
     */
    protected ConnectionManager $connectionManager;

    /**
     * @var Mapper
     */
    protected Mapper $mapper;

    /**
     * @var ResponseFactory
     */
    protected ResponseFactory $responseFactory;

    /**
     * @var AggregationBuilder
     */
    protected AggregationBuilder $aggregationBuilder;

    /**
     * @var QueryContainerFactory
     */
    protected QueryContainerFactory $queryContainerFactory;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param ConnectionManager $connectionManager
     * @param Mapper $mapper
     * @param ResponseFactory $responseFactory
     * @param AggregationBuilder $aggregationBuilder
     * @param QueryContainerFactory $queryContainerFactory
     */
    public function __construct(
        ConnectionManager $connectionManager,
        Mapper $mapper,
        ResponseFactory $responseFactory,
        AggregationBuilder $aggregationBuilder,
        QueryContainerFactory $queryContainerFactory,
        LoggerInterface $logger
    ) {
        $this->connectionManager = $connectionManager;
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
        $client = $this->connectionManager->getConnection();
        $aggregationBuilder = $this->aggregationBuilder;
        $query = $this->mapper->buildQuery($request);
        $aggregationBuilder->setQuery($this->queryContainerFactory->create(['query' => $query]));

        $search = $query['query']['query']['query']['*']['query'] ?? '';

        $response = [];
        try {
            $searchResult = $client
                ->index($query['index'])
                ->search($search,
                    [
                        'offset' => (int)$query['offset'],
                        'limit' => (int)$query['limit'],
                        'facets' => ['*'],
                        'filter' => $query['filter'],
                        'sort' => $query['sort'],
                    ]
                );
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
