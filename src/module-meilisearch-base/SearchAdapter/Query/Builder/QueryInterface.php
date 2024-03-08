<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter\Query\Builder;

use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;

interface QueryInterface
{
    /**
     * @param array $selectQuery
     * @param RequestQueryInterface $requestQuery
     * @param string $conditionType
     * @return array
     */
    public function build(
        array $selectQuery,
        RequestQueryInterface $requestQuery,
        $conditionType
    );
}
