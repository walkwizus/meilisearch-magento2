<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter;

use Magento\Framework\ObjectManagerInterface;

class AggregationFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected ObjectManagerInterface $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param array $rawAggregation
     * @return \Magento\Framework\Search\Response\Aggregation|mixed
     */
    public function create(array $rawAggregation)
    {
        $buckets = [];
        foreach ($rawAggregation as $rawBucketName => $rawBucket) {
            /** @var \Magento\Framework\Search\Response\Bucket[] $buckets */
            $buckets[$rawBucketName] = $this->objectManager->create(
                \Magento\Framework\Search\Response\Bucket::class,
                [
                    'name' => $rawBucketName,
                    'values' => $this->prepareValues($rawBucket)
                ]
            );
        }

        return $this->objectManager->create(
            \Magento\Framework\Search\Response\Aggregation::class,
            ['buckets' => $buckets]
        );
    }

    /**
     * Prepare values list
     *
     * @param array $values
     * @return \Magento\Framework\Search\Response\Aggregation\Value[]
     */
    private function prepareValues(array $values): array
    {
        $valuesObjects = [];
        foreach ($values as $name => $value) {
            $valuesObjects[] = $this->objectManager->create(
                \Magento\Framework\Search\Response\Aggregation\Value::class,
                [
                    'value' => $name,
                    'metrics' => $value,
                ]
            );
        }
        return $valuesObjects;
    }
}
