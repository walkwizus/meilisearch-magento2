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
     * @param string $typeName
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
        private string $typeName,
        private SettingsInterface $settings,
        private int $batchSize = 10000,
        private string $indexPrimaryKey = 'id'
    ) { }

    /**
     * @param $dimensions
     * @param \Traversable $documents
     * @return $this|IndexerInterface
     */
    public function saveIndex($dimensions, \Traversable $documents): IndexerInterface|static
    {
        foreach ($dimensions as $dimension) {
            $storeId = $dimension->getValue();
            $indexerId = $this->getIndexerId();

            $this->meilisearchAdapter->updateSettings($this->settings->getSettings($indexerId), $storeId, $indexerId);

            foreach ($this->batch->getItems($documents, $this->batchSize) as $batchDocuments) {
                $batchDocuments = $this->attributeMapper->map($indexerId, $batchDocuments, $storeId);
                $this->meilisearchAdapter->addDocs($batchDocuments, $storeId, $indexerId, $this->indexPrimaryKey);
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
            $indexerId = $this->getIndexerId();

            $this->meilisearchAdapter->deleteIndex($storeId, $indexerId);
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
            $indexerId = $this->getIndexerId();

            $this->meilisearchAdapter->cleanIndex($storeId, $indexerId);
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
    private function getIndexerId(): string
    {
        return $this->searchIndexNameResolver->getIndexMapping($this->indexName);
    }
}
