<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter\Aggregation;

use Magento\Framework\Search\Dynamic\IntervalInterface;
use Walkwizus\MeilisearchBase\SearchAdapter\ConnectionManager;
use Walkwizus\MeilisearchBase\SearchAdapter\SearchIndexNameResolver;
use Magento\CatalogSearch\Model\Indexer\Fulltext;

class Interval implements IntervalInterface
{
    const DELTA = 0.005;

    /**
     * @param ConnectionManager $connectionManager
     * @param SearchIndexNameResolver $searchIndexNameResolver
     * @param string $fieldName
     * @param string $storeId
     * @param array $entityIds
     */
    public function __construct(
        private ConnectionManager $connectionManager,
        private SearchIndexNameResolver $searchIndexNameResolver,
        private string $fieldName,
        private string $storeId,
        private array $entityIds
    ) { }

    /**
     * @param $limit
     * @param $offset
     * @param $lower
     * @param $upper
     * @return array
     */
    public function load($limit, $offset = null, $lower = null, $upper = null)
    {
        $from = $lower - self::DELTA;
        $to = $upper - self::DELTA;

        $client = $this->connectionManager->getConnection();
        $searchResult = $client
            ->index($this->searchIndexNameResolver->getIndexName($this->storeId, Fulltext::INDEXER_ID))
            ->search('',
                [
                    'offset' => $offset,
                    'limit' => $limit,
                    'filter' => $this->fieldName . ' >= ' . $from . ' AND ' . $this->fieldName . ' < ' . $to . ' AND id IN [' . implode(',', $this->entityIds) . ']',
                    'attributesToRetrieve' => [$this->fieldName],
                    'sort' => [$this->fieldName . ':asc']
                ]
            );

        return $this->arrayValuesToFloat($searchResult->toArray(), $this->fieldName);
    }

    /**
     * @param $data
     * @param $index
     * @param $lower
     * @return array|false
     */
    public function loadPrevious($data, $index, $lower = null)
    {
        if ($lower) {
            $from = $lower - self::DELTA;
        }

        if ($data) {
            $to = $data - self::DELTA;
        }

        $client = $this->connectionManager->getConnection();
        $searchResult = $client
            ->index($this->searchIndexNameResolver->getIndexName($this->storeId, Fulltext::INDEXER_ID))
            ->search('',
                [
                    'limit' => 0,
                    'filter' => $this->fieldName . ' >= ' . $from . ' AND ' . $this->fieldName . ' < ' . $to . ' AND id IN [' . implode(',', $this->entityIds) . ']',
                    'sort' => [$this->fieldName . ':asc']
                ]
            )->toArray();

        $offset = $searchResult['hitsCount'];
        if (!$offset) {
            return false;
        }

        return $this->load($index - $offset + 1, $offset - 1, $lower);
    }

    /**
     * @param $data
     * @param $rightIndex
     * @param $upper
     * @return array|false
     */
    public function loadNext($data, $rightIndex, $upper = null)
    {
        $from = $data + self::DELTA;
        $to = $data - self::DELTA;

        $client = $this->connectionManager->getConnection();
        $searchResult = $client
            ->index($this->searchIndexNameResolver->getIndexName($this->storeId, Fulltext::INDEXER_ID))
            ->search('',
                [
                    'limit' => 0,
                    'filter' => $this->fieldName . ' > ' . $from . ' AND ' . $this->fieldName . ' < ' . $to . ' AND id IN [' . implode(',', $this->entityIds) . ']',
                    'sort' => [$this->fieldName . ':asc']
                ]
            )->toArray();

        $offset = $searchResult['hitsCount'];
        if (!$offset) {
            return false;
        }

        $from = $data - self::DELTA;
        if ($upper !== null) {
            $to = $data - self::DELTA;
        }

        $client = $this->connectionManager->getConnection();
        $searchResult = $client
            ->index($this->searchIndexNameResolver->getIndexName($this->storeId, Fulltext::INDEXER_ID))
            ->search('',
                [
                    'offset' => $offset - 1,
                    'limit' => $rightIndex - $offset + 1,
                    'filter' => $this->fieldName . ' >= ' . $from . ' AND ' . $this->fieldName . ' < ' . $to . ' AND id IN [' . implode(',', $this->entityIds) . ']',
                    'sort' => [$this->fieldName . ':asc']
                ]
            );

        return array_reverse($this->arrayValuesToFloat($searchResult->toArray(), $this->fieldName));
    }

    /**
     * @param array $hits
     * @param string $fieldName
     * @return array
     */
    private function arrayValuesToFloat(array $hits, string $fieldName): array
    {
        $returnPrices = [];
        foreach ($hits['hits'] as $hit) {
            $returnPrices[] = (float)$hit[$fieldName];
        }

        return $returnPrices;
    }
}
