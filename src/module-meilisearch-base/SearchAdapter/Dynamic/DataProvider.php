<?php

namespace Walkwizus\MeilisearchBase\SearchAdapter\Dynamic;

use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Magento\Elasticsearch\SearchAdapter\QueryAwareInterface;
use Magento\Catalog\Model\Layer\Filter\Price\Range;
use Magento\Framework\Search\Dynamic\IntervalFactory;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Search\Dynamic\EntityStorage;
use Magento\Framework\Search\Dynamic\IntervalInterface;
use Magento\Framework\Search\Request\BucketInterface;

class DataProvider implements DataProviderInterface, QueryAwareInterface
{
    /**
     * @var Range
     */
    protected Range $range;

    /**
     * @var IntervalFactory
     */
    protected IntervalFactory $intervalFactory;

    /**
     * @var ScopeResolverInterface
     */
    protected ScopeResolverInterface $scopeResolver;

    /**
     * @param Range $range
     * @param IntervalFactory $intervalFactory
     * @param ScopeResolverInterface $scopeResolver
     */
    public function __construct(
        Range $range,
        IntervalFactory $intervalFactory,
        ScopeResolverInterface $scopeResolver
    ) {
        $this->range = $range;
        $this->intervalFactory = $intervalFactory;
        $this->scopeResolver = $scopeResolver;
    }

    /**
     * @return array
     */
    public function getRange()
    {
        return $this->range->getPriceRange();
    }

    /**
     * @param EntityStorage $entityStorage
     * @return array
     */
    public function getAggregations(EntityStorage $entityStorage): array
    {
        $source = $entityStorage->getSource();
        $count = $source['query_result']['estimatedTotalHits'];

        if ($count == 0) {
            return [
                'count' => 0,
                'max' => 0,
                'min' => 0,
                'std' => 0,
            ];
        }

        $priceDistribution = $source['query_result']['facetDistribution'][$source['price_field']];

        $aggregations = [
            'count' => $count,
            'min' => $source['query_result']['facetDistribution']['facetStats'][$source['price_field']]['min'],
            'max' => $source['query_result']['facetDistribution']['facetStats'][$source['price_field']]['max'],
        ];

        $sum = 0;
        foreach($priceDistribution as $key => $value) {
            $sum += (float)$key * (float)$value;
        }
        $avg = $sum / $count;

        $sumOfSquares = 0;
        foreach ($priceDistribution as $key => $value) {
            $diff = $key - $avg;
            $sumOfSquares += ($diff * $diff) * $value;
        }

        $aggregations['sum'] = $sum;
        $aggregations['avg'] = $avg;
        $aggregations['std'] = sqrt($sumOfSquares / $count);

        return $aggregations;
    }

    /**
     * @param BucketInterface $bucket
     * @param array $dimensions
     * @param EntityStorage $entityStorage
     * @return IntervalInterface
     */
    public function getInterval(BucketInterface $bucket, array $dimensions, EntityStorage $entityStorage)
    {
        $source = $entityStorage->getSource();
        $entityIds = $source['entity_ids'];
        $fieldName = $source['price_field'];
        $dimension = current($dimensions);
        $storeId = $this->scopeResolver->getScope($dimension->getValue())->getId();

        return $this->intervalFactory->create(
            [
                'entityIds' => $entityIds,
                'storeId' => $storeId,
                'fieldName' => $fieldName,
            ]
        );
    }

    /**
     * @param BucketInterface $bucket
     * @param array $dimensions
     * @param $range
     * @param EntityStorage $entityStorage
     * @return array
     */
    public function getAggregation(BucketInterface $bucket, array $dimensions, $range, EntityStorage $entityStorage)
    {
        $source = $entityStorage->getSource();
        $priceDistribution = $source['query_result']['facetDistribution'][$source['price_field']];
        $result = [];

        foreach ($priceDistribution as $price => $count) {
            $key = intval($price / $range + 1);
            if (!isset($result[$key])) {
                $result[$key] = 0;
            }
            $result[$key] += $count;
        }

        ksort($result);

        return $result;
    }

    /**
     * @param $range
     * @param array $dbRanges
     * @return array
     */
    public function prepareData($range, array $dbRanges)
    {
        $data = [];
        if (!empty($dbRanges)) {
            foreach ($dbRanges as $index => $count) {
                $fromPrice = $index == 1 ? 0 : ($index - 1) * $range;
                $toPrice = $index * $range;
                $data[] = [
                    'from' => $fromPrice,
                    'to' => $toPrice,
                    'count' => $count,
                ];
            }
        }

        return $data;
    }
}
