<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Api\Data;

interface CategoryInterface
{
    const CATEGORY_ID = 'category_id';

    const QUERY = 'query';

    /**
     * @return int
     */
    public function getCategoryId();

    /**
     * @param int $categoryId
     * @return $this
     */
    public function setCategoryId($categoryId);

    /**
     * @return string
     */
    public function getQuery();

    /**
     * @param string $query
     * @return $this
     */
    public function setQuery(string $query);
}
