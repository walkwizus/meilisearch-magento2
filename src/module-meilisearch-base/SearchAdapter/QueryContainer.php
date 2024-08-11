<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter;

class QueryContainer
{
    /**
     * @var array
     */
    private array $query;

    /**
     * @param array $query
     */
    public function __construct(array $query)
    {
        $this->query = $query;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }
}
