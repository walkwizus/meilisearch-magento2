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
     * @param ConnectionManager $connectionManager
     * @param SearchIndexNameResolver $searchIndexNameResolver
     * @throws LocalizedException
     */
    public function __construct(
        private ConnectionManager $connectionManager,
        private SearchIndexNameResolver $searchIndexNameResolver
    ) {
        try {
            $this->client = $this->connectionManager->getConnection();
        } catch (\Exception $e) {
            throw new LocalizedException(__('The search failed because of a search engine misconfiguration.'));
        }
    }

    /**
     * @param $index
     * @param string $query
     * @param array $params
     * @return SearchResult
     */
    public function search($index, string $query = '', array $params = []): SearchResult
    {
        return $this->client->index($index)->search($query, $params);
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
