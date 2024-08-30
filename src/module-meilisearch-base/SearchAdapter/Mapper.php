<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter;

use InvalidArgumentException;
use Walkwizus\MeilisearchBase\SearchAdapter\Query\Builder as QueryBuilder;
use Walkwizus\MeilisearchBase\SearchAdapter\Query\Builder\MatchQuery as MatchQueryBuilder;
use Magento\Framework\Search\Request\Query\BoolExpression as BoolQuery;
use Magento\Framework\Search\Request\Query\Filter as FilterQuery;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Request\Filter\Range;
use Magento\Framework\Search\Request;

class Mapper
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param MatchQueryBuilder $matchQueryBuilder
     */
    public function __construct(
        private QueryBuilder $queryBuilder,
        private MatchQueryBuilder $matchQueryBuilder
    ) { }

    /**
     * Build adapter dependent query
     *
     * @param RequestInterface $request
     * @return array
     */
    public function buildQuery(RequestInterface $request): array
    {
        $searchQuery = $this->queryBuilder->initQuery($request);
        $searchQuery['query'] = array_merge(
            $searchQuery['query'],
            $this->processQuery(
                $request->getQuery(),
                [],
                BoolQuery::QUERY_CONDITION_MUST
            )
        );
        $searchQuery['filter'] = $this->buildFilterString($request);

        return $searchQuery;
    }

    /**
     * Process query
     *
     * @param RequestQueryInterface $requestQuery
     * @param array $selectQuery
     * @param string $conditionType
     * @return array
     * @throws InvalidArgumentException
     */
    protected function processQuery(
        RequestQueryInterface $requestQuery,
        array $selectQuery,
        string $conditionType
    ): array {
        return match ($requestQuery->getType()) {
            RequestQueryInterface::TYPE_MATCH => $this->matchQueryBuilder->build($selectQuery, $requestQuery, $conditionType),
            RequestQueryInterface::TYPE_BOOL => $this->processBoolQuery($requestQuery, $selectQuery),
            RequestQueryInterface::TYPE_FILTER => $this->processFilterQuery($requestQuery, $selectQuery, $conditionType),
            default => throw new InvalidArgumentException(sprintf(
                'Unknown query type \'%s\'',
                $requestQuery->getType()
            )),
        };
    }

    /**
     * Process bool query
     *
     * @param BoolQuery $query
     * @param array $selectQuery
     * @return array
     */
    protected function processBoolQuery(
        BoolQuery $query,
        array $selectQuery
    ): array {
        $selectQuery = $this->processBoolQueryCondition(
            $query->getMust(),
            $selectQuery,
            BoolQuery::QUERY_CONDITION_MUST
        );

        $selectQuery = $this->processBoolQueryCondition(
            $query->getShould(),
            $selectQuery,
            BoolQuery::QUERY_CONDITION_SHOULD
        );

        return $this->processBoolQueryCondition(
            $query->getMustNot(),
            $selectQuery,
            BoolQuery::QUERY_CONDITION_NOT
        );
    }

    /**
     * Process bool query condition (must, should, must_not)
     *
     * @param RequestQueryInterface[] $subQueryList
     * @param array $selectQuery
     * @param string $conditionType
     * @return array
     */
    protected function processBoolQueryCondition(
        array $subQueryList,
        array $selectQuery,
        string $conditionType
    ): array {
        foreach ($subQueryList as $subQuery) {
            $selectQuery = $this->processQuery($subQuery, $selectQuery, $conditionType);
        }

        return $selectQuery;
    }

    /**
     * Process filter query
     *
     * @param FilterQuery $query
     * @param array $selectQuery
     * @param string $conditionType
     * @return array
     */
    private function processFilterQuery(
        FilterQuery $query,
        array $selectQuery,
        string $conditionType
    ): array {
        if ($query->getReferenceType() === FilterQuery::REFERENCE_FILTER) {
            $selectQuery[] = [
                'filter' => $query->getReference(),
                'condition' => $conditionType,
            ];
        }

        return $selectQuery;
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    private function buildFilterString(RequestInterface $request): string
    {
        $filters = [];
        $query = $request->getQuery();

        if ($query instanceof BoolQuery) {
            $filterQueries = $query->getMust();
            $conditions = [];

            foreach ($filterQueries as $filterQuery) {
                if ($filterQuery instanceof FilterQuery) {
                    $filter = $filterQuery->getReference();
                    $field = $filter->getField();
                    $value = $filter->getValue();

                    if (is_array($value)) {
                        $orConditions = [];
                        foreach ($value as $singleValue) {
                            $orConditions[] = "{$field} = {$singleValue}";
                        }
                        $conditions[] = '(' . implode(' OR ', $orConditions) . ')';
                    } else {
                        $conditions[] = "{$field} = {$value}";
                    }
                }
            }

            if (!empty($conditions)) {
                $filters[] = implode(' AND ', $conditions);
            }
        }

        return implode(' AND ', $filters);
    }
}
