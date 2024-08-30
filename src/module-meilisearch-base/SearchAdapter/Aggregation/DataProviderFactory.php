<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter\Aggregation;

use Magento\Elasticsearch\SearchAdapter\QueryAwareInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Walkwizus\MeilisearchBase\SearchAdapter\QueryContainer;

class DataProviderFactory
{
    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        private ObjectManagerInterface $objectManager
    ) { }

    public function create(
        DataProviderInterface $dataProvider,
        QueryContainer $query = null,
        ?string $aggregationFieldName = null
    ) {
        $result = $dataProvider;
        if ($dataProvider instanceof QueryAwareInterface) {
            if (null === $query) {
                throw new \LogicException(
                    'Instance of ' . QueryAwareInterface::class . ' must be configured with a search query,'
                    . ' but the query is empty'
                );
            }

            $className = get_class($dataProvider);
            $result = $this->objectManager->create(
                $className,
                [
                    'queryContainer' => $query,
                    'aggregationFieldName' => $aggregationFieldName
                ]
            );
        }

        return $result;
    }
}
