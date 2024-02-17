<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Search\Response\QueryResponse;

class ResponseFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected ObjectManagerInterface $objectManager;

    /**
     * @var DocumentFactory
     */
    protected DocumentFactory $documentFactory;

    /**
     * @var AggregationFactory
     */
    protected AggregationFactory $aggregationFactory;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param DocumentFactory $documentFactory
     * @param AggregationFactory $aggregationFactory
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        DocumentFactory $documentFactory,
        AggregationFactory $aggregationFactory
    ) {
        $this->objectManager = $objectManager;
        $this->documentFactory = $documentFactory;
        $this->aggregationFactory = $aggregationFactory;
    }

    public function create($response)
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
