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
     * @var MeilisearchAdapter
     */
    protected MeilisearchAdapter $meilisearchAdapter;

    /**
     * @var SearchIndexNameResolver
     */
    protected SearchIndexNameResolver $searchIndexNameResolver;

    /**
     * @var Batch
     */
    protected Batch $batch;

    /**
     * @var AttributeMapper
     */
    protected AttributeMapper $attributeMapper;

    /**
     * @var string
     */
    protected string $indexName;

    /**
     * @var string
     */
    protected string $typeName;

    /**
     * @var SettingsInterface
     */
    protected SettingsInterface $settings;

    /**
     * @var int
     */
    protected int $batchSize;

    /**
     * @var string
     */
    protected string $indexPrimaryKey;

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
        MeilisearchAdapter $meilisearchAdapter,
        SearchIndexNameResolver $searchIndexNameResolver,
        Batch $batch,
        AttributeMapper $attributeMapper,
        string $indexName,
        string $typeName,
        SettingsInterface $settings,
        int $batchSize = 10,
        string $indexPrimaryKey = 'id'
    ) {
        $this->meilisearchAdapter = $meilisearchAdapter;
        $this->searchIndexNameResolver = $searchIndexNameResolver;
        $this->batch = $batch;
        $this->attributeMapper = $attributeMapper;
        $this->indexName = $indexName;
        $this->typeName = $typeName;
        $this->settings = $settings;
        $this->batchSize = $batchSize;
        $this->indexPrimaryKey = $indexPrimaryKey;
    }

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
