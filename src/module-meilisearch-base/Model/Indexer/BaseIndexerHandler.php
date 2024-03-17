<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Model\Indexer;

use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Walkwizus\MeilisearchBase\Model\Adapter\Meilisearch as MeilisearchAdapter;
use Walkwizus\MeilisearchBase\SearchAdapter\SearchIndexNameResolver;
use Walkwizus\MeilisearchBase\Api\Index\SettingsInterface;
use Walkwizus\MeilisearchBase\Index\AttributeMapper;
use Magento\Framework\Indexer\SaveHandler\Batch;

class BaseIndexerHandler implements IndexerInterface
{
    /**
     * @param MeilisearchAdapter $meilisearchAdapter
     * @param SearchIndexNameResolver $searchIndexNameResolver
     * @param Batch $batch
     * @param AttributeMapper $attributeMapper
     * @param string $indexName
     * @param SettingsInterface $settings
     * @param int $batchSize
     * @param string $indexPrimaryKey
     */
    public function __construct(
        private MeilisearchAdapter $meilisearchAdapter,
        private SearchIndexNameResolver $searchIndexNameResolver,
        private Batch $batch,
        private AttributeMapper $attributeMapper,
        private string $indexName,
        private SettingsInterface $settings,
        private int $batchSize = 10000,
        private string $indexPrimaryKey = 'id'
    ) { }

    /**
     * @param $dimensions
     * @param \Traversable $documents
     * @return IndexerInterface
     */
    public function saveIndex($dimensions, \Traversable $documents): IndexerInterface
    {
        foreach ($dimensions as $dimension) {
            $storeId = $dimension->getValue();
            $indexerId = $this->getIndexerId();
            $indexName = $this->searchIndexNameResolver->getIndexName($storeId, $this->indexName);

            $this->meilisearchAdapter->updateSettings($indexName, $this->settings->getSettings($indexerId));

            foreach ($this->batch->getItems($documents, $this->batchSize) as $batchDocuments) {
                $batchDocuments = $this->attributeMapper->map($indexerId, $batchDocuments, $storeId);
                $this->meilisearchAdapter->addDocs($indexName, $batchDocuments, $this->indexPrimaryKey);
            }
        }

        return $this;
    }

    /**
     * @param $dimensions
     * @param \Traversable $documents
     * @return void
     */
    public function deleteIndex($dimensions, \Traversable $documents): void
    {
        foreach ($dimensions as $dimension) {
            $storeId = $dimension->getValue();
            $indexName = $this->searchIndexNameResolver->getIndexName($storeId, $this->indexName);
            $this->meilisearchAdapter->deleteIndex($indexName);
        }
    }

    /**
     * @param $dimensions
     * @return void
     */
    public function cleanIndex($dimensions): void
    {
        foreach ($dimensions as $dimension) {
            $storeId = $dimension->getValue();
            $indexName = $this->searchIndexNameResolver->getIndexName($storeId, $this->indexName);
            $this->meilisearchAdapter->cleanIndex($indexName);
        }
    }

    /**
     * @param array $dimensions
     * @return true
     */
    public function isAvailable($dimensions = []): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getIndexerId(): string
    {
        return $this->searchIndexNameResolver->getIndexMapping($this->indexName);
    }
}
