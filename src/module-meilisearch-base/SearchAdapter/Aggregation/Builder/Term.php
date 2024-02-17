<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter\Aggregation\Builder;

use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;
use Magento\Framework\Search\Request\Dimension;

class Term implements BucketBuilderInterface
{
    /**
     * @param RequestBucketInterface $bucket
     * @param array $dimensions
     * @param array $queryResult
     * @param DataProviderInterface $dataProvider
     * @return array
     */
    public function build(
        RequestBucketInterface $bucket,
        array $dimensions,
        array $queryResult,
        DataProviderInterface $dataProvider
    ): array {
        $fieldName = $bucket->getField();
        $buckets = $queryResult['facetDistribution'][$fieldName] ?? [];
        $values = [];

        foreach ($buckets as $key => $value) {
            $values[$key] = [
                'value' => $key,
                'count' => $value,
            ];
        }

        return $values;
    }
}
