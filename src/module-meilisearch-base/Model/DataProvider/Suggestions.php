<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Model\DataProvider;

use Magento\AdvancedSearch\Model\SuggestedQueriesInterface;
use Magento\Search\Model\QueryResult;
use Walkwizus\Meilisearch\SearchAdapter\SearchIndexNameResolver;
use Magento\Search\Model\QueryInterface;
use Walkwizus\Meilisearch\SearchAdapter\ConnectionManager;

class Suggestions implements SuggestedQueriesInterface
{
    /**
     * @param QueryInterface $query
     * @return array|QueryResult[]
     */
    public function getItems(QueryInterface $query): array
    {
        return [];
    }

    /**
     * @return bool
     */
    public function isResultsCountEnabled()
    {
        return false;
    }
}
