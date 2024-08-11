<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Model\Adapter;

use Meilisearch\Client;
use Walkwizus\MeilisearchBase\SearchAdapter\ConnectionManager;
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
    private ConnectionManager $connectionManager;

    /**
     * @param ConnectionManager $connectionManager
     * @throws LocalizedException
     */
    public function __construct(
        ConnectionManager $connectionManager
    ) {
        $this->connectionManager = $connectionManager;

        try {
            $this->client = $this->connectionManager->getConnection();
        } catch (\Exception $e) {
            throw new LocalizedException(__('The search failed because of a search engine misconfiguration.'));
        }
    }

    /**
     * @return bool
     */
    public function isHealthy(): bool
    {
        return $this->client->isHealthy();
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
     * @param array $queries
     * @return mixed
     */
    public function multiSearch(array $queries)
    {
        return $this->client->multiSearch($queries);
    }

    /**
     * @param $indexName
     * @return array|null
     */
    public function getIndex($indexName)
    {
        return $this->client->index($indexName)->fetchRawInfo();
    }

    /**
     * @param string $indexName
     * @param array $documents
     * @param string $primaryKey
     * @return $this
     */
    public function addDocs(string $indexName, array $documents, string $primaryKey): static
    {
        if (count($documents)) {
            try {
                $this->client->updateIndex($indexName, ['primaryKey' => $primaryKey]);
                $index = $this->client->index($indexName);
                $index->addDocumentsInBatches($documents, count($documents), $primaryKey);
            } catch (\Exception $e) {

            }
        }

        return $this;
    }

    /**
     * @param $indexName
     * @param array $settings
     * @return $this
     */
    public function updateSettings($indexName, array $settings): static
    {
        if (count($settings) > 0) {
            $this->client->index($indexName)->updateSettings($settings);
        }

        return $this;
    }

    /**
     * @param string $indexName
     * @return void
     */
    public function deleteIndex(string $indexName)
    {
        $this->client->deleteIndex($indexName);
    }

    /**
     * @param string $indexName
     * @return $this
     */
    public function cleanIndex(string $indexName): static
    {
        $this->client->index($indexName)->deleteAllDocuments();
        return $this;
    }
}
