<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Search\Response\QueryResponse;

class ResponseFactory
{
    /**
     * @param ObjectManagerInterface $objectManager
     * @param DocumentFactory $documentFactory
     * @param AggregationFactory $aggregationFactory
     */
    public function __construct(
        private ObjectManagerInterface $objectManager,
        private DocumentFactory $documentFactory,
        private AggregationFactory $aggregationFactory
    ) { }

    /**
     * @param array $response
     * @return QueryResponse
     */
    public function create(array $response): QueryResponse
    {
        $documents = [];
        foreach ($response['documents']['hits'] as $rawDocument) {
            $documents[] = $this->documentFactory->create($rawDocument);
        }

        $aggregations = $this->aggregationFactory->create($response['aggregations']);

        return $this->objectManager->create(
            QueryResponse::class,
            [
                'documents' => $documents,
                'aggregations' => $aggregations,
                'total' => $response['total']
            ]
        );
    }
}
