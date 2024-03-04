<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Api\Data;

interface FacetInterface
{
    const INDEX_NAME = 'index_name';

    /**
     * @return string
     */
    public function getIndexName();

    /**
     * @param string $indexName
     * @return $this
     */
    public function setIndexName($indexName);
}
