<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter;

class QueryContainer
{
    /**
     * @param array $query
     */
    public function __construct(
        private array $query
    ) { }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }
}
