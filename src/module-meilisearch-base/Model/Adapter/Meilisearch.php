<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Model\Adapter;

use Meilisearch\Client;
use Walkwizus\MeilisearchBase\SearchAdapter\ConnectionManager;
use Walkwizus\MeilisearchBase\SearchAdapter\SearchIndexNameResolver;
use Meilisearch\Search\SearchResult;
use Magento\Framework\Exception\LocalizedException;

class Meilisearch
{
    /**
     * @var Client|null
     */
    protected ?Client $client;

    /**
     * @var ConnectionManager
     */
    protected ConnectionManager $connectionManager;

    /**
     * @var SearchIndexNameResolver
     */
    protected SearchIndexNameResolver $searchIndexNameResolver;

    /**
     * @param ConnectionManager $connectionManager
     * @param SearchIndexNameResolver $searchIndexNameResolver
     * @throws LocalizedException
     */
    public function __construct(
        ConnectionManager $connectionManager,
        SearchIndexNameResolver $searchIndexNameResolver
    ) {
        $this->connectionManager = $connectionManager;
        $this->searchIndexNameResolver = $searchIndexNameResolver;

        try {
            $this->client = $this->connectionManager->getConnection();
        } catch (\Exception $e) {
            throw new LocalizedException(__('The search failed because of a search engine misconfiguration.'));
        }
    }

    /**
     * @param $storeId
     * @param $index
     * @param string $query
     * @param array $filter
     * @return SearchResult
     */
    public function search($storeId, $index, string $query = '', array $filter = []): SearchResult
    {
        $indexName = $this->getIndexName($storeId, $index);
        return $this->client->index($indexName)->search($query, ['filter' => $filter]);
    }

    /**
     * @param array $documents
     * @param $storeId
     * @param $mappedIndexerId
     * @param string $primaryKey
     * @return $this
     */
    public function addDocs(array $documents, $storeId, $mappedIndexerId, string $primaryKey): static
    {
        if (count($documents)) {
            try {
                $indexName = $this->getIndexName($storeId, $mappedIndexerId);
                $this->client->updateIndex($indexName, ['primaryKey' => $primaryKey]);
                $index = $this->client->index($indexName);
                $index->addDocumentsInBatches($documents, count($documents), $primaryKey);
            } catch (\Exception $e) {

            }
        }

        return $this;
    }

    /**
     * @param array $settings
     * @param $storeId
     * @param $mappedIndexerId
     * @return $this
     */
    public function updateSettings(array $settings, $storeId, $mappedIndexerId): static
    {
        if (count($settings) > 0) {
            $indexName = $this->getIndexName($storeId, $mappedIndexerId);
            $this->client->index($indexName)->updateSettings($settings);
        }

        return $this;
    }

    /**
     * @param $storeId
     * @param $mappedIndexerId
     * @return void
     */
    public function deleteIndex($storeId, $mappedIndexerId)
    {
        $indexName = $this->getIndexName($storeId, $mappedIndexerId);
        $this->client->deleteIndex($indexName);
    }

    /**
     * @param $storeId
     * @param $mappedIndexerId
     * @return $this
     */
    public function cleanIndex($storeId, $mappedIndexerId): static
    {
        $indexName = $this->getIndexName($storeId, $mappedIndexerId);
        $this->client->index($indexName)->deleteAllDocuments();

        return $this;
    }

    /**
     * @param $storeId
     * @param $mappedIndexerId
     * @return string
     */
    private function getIndexName($storeId, $mappedIndexerId): string
    {
        return $this->searchIndexNameResolver->getIndexName($storeId, $mappedIndexerId);
    }
}
