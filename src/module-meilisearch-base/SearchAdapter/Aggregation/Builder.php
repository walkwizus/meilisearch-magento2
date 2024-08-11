<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter\Aggregation;

use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Walkwizus\MeilisearchBase\SearchAdapter\Aggregation\Builder\BucketBuilderInterface;
use Magento\Framework\Search\RequestInterface;
use Walkwizus\MeilisearchBase\SearchAdapter\QueryContainer;

class Builder
{
    /**
     * @var QueryContainer|null
     */
    protected ?QueryContainer $query = null;

    /**
     * @var array|DataProviderInterface[]
     */
    private array $dataProviderContainer;

    /**
     * @var array|BucketBuilderInterface[]
     */
    private array $aggregationContainer;

    /**
     * @var DataProviderFactory
     */
    private DataProviderFactory $dataProviderFactory;

    /**
     * @param array $dataProviderContainer
     * @param array $aggregationContainer
     * @param DataProviderFactory $dataProviderFactory
     */
    public function __construct(
        array $dataProviderContainer,
        array $aggregationContainer,
        DataProviderFactory $dataProviderFactory
    ) {
        $this->dataProviderContainer = array_map(
            static function (DataProviderInterface $dataProvider) {
                return $dataProvider;
            },
            $dataProviderContainer
        );
        $this->aggregationContainer = array_map(
            static function (BucketBuilderInterface $bucketBuilder) {
                return $bucketBuilder;
            },
            $aggregationContainer
        );
        $this->dataProviderFactory = $dataProviderFactory;
    }

    /**
     * @param RequestInterface $request
     * @param array $queryResult
     * @return array
     */
    public function build(RequestInterface $request, array $queryResult): array
    {
        $aggregations = [];
        $buckets = $request->getAggregation();

        foreach ($buckets as $bucket) {
            $dataProvider = $this->dataProviderFactory->create(
                $this->dataProviderContainer[$request->getIndex()],
                $this->query,
                $bucket->getField()
            );
            $bucketAggregationBuilder = $this->aggregationContainer[$bucket->getType()];
            $aggregations[$bucket->getName()] = $bucketAggregationBuilder->build(
                $bucket,
                $request->getDimensions(),
                $queryResult,
                $dataProvider
            );
        }

        $this->query = null;

        return $aggregations;
    }

    /**
     * @param QueryContainer $query
     * @return $this
     */
    public function setQuery(QueryContainer $query): static
    {
        $this->query = $query;
        return $this;
    }
}
