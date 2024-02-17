<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter\Query\Builder;

use Walkwizus\MeilisearchBase\SearchAdapter\SearchIndexNameResolver;
use Walkwizus\MeilisearchBase\SearchAdapter\Query\Builder\Aggregation as AggregationBuilder;
use Walkwizus\MeilisearchBase\SearchAdapter\Query\ValueTransformerPool;
use Magento\Framework\Search\Request\Query\BoolExpression;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;

class MatchQuery implements QueryInterface
{
    /**
     * Meilisearch condition for when the query must not appear in the matching documents.
     */
    public const QUERY_CONDITION_MUST_NOT = 'mustNot';

    /**
     * @var SearchIndexNameResolver
     */
    private SearchIndexNameResolver $searchIndexNameResolver;

    /**
     * @var AggregationBuilder
     */
    private AggregationBuilder $aggregationBuilder;

    /**
     * @var ValueTransformerPool
     */
    private ValueTransformerPool $valueTransformerPool;

    /**
     * @param SearchIndexNameResolver $searchIndexNameResolver
     * @param AggregationBuilder $aggregationBuilder
     * @param ValueTransformerPool $valueTransformerPool
     */
    public function __construct(
        SearchIndexNameResolver $searchIndexNameResolver,
        AggregationBuilder $aggregationBuilder,
        ValueTransformerPool $valueTransformerPool
    ) {
        $this->searchIndexNameResolver = $searchIndexNameResolver;
        $this->aggregationBuilder = $aggregationBuilder;
        $this->valueTransformerPool = $valueTransformerPool;
    }

    /**
     * @inheritdoc
     */
    public function build(array $selectQuery, RequestQueryInterface $requestQuery, $conditionType): array
    {
        $queryValue = $this->prepareQuery($requestQuery->getValue(), $conditionType);
        $queries = $this->buildQueries($requestQuery->getMatches(), $queryValue);
        $requestQueryBoost = $requestQuery->getBoost() ?: 1;

        foreach ($queries as $query) {
            $matchKey = $query['matchCondition'];
            foreach ($query['fields'] as $field => $matchQuery) {
                $matchQuery['boost'] = $requestQueryBoost + $matchQuery['boost'];
                $selectQuery['query'][$matchKey][$field] = $matchQuery;
            }
        }

        return $selectQuery;
    }

    /**
     * Prepare query
     *
     * @param string $queryValue
     * @param string $conditionType
     * @return array
     */
    private function prepareQuery(string $queryValue, string $conditionType): array
    {
        $condition = $conditionType === BoolExpression::QUERY_CONDITION_NOT
            ? self::QUERY_CONDITION_MUST_NOT
            : 'must';

        return [
            'condition' => $condition,
            'value' => $queryValue,
        ];
    }

    /**
     * @param array $matches
     * @param array $queryValue
     * @return array
     */
    private function buildQueries(array $matches, array $queryValue): array
    {
        $conditions = [];

        foreach ($matches as $match) {
            $resolvedField = $match['field'];
            //$transformedValue = $this->valueTransformerPool->transform($queryValue['value'], $resolvedField);
            $transformedValue = $queryValue['value'];

            if (null === $transformedValue) {
                //Value is incompatible with this field type.
                continue;
            }

            $matchCondition = $match['matchCondition'] ?? 'query';
            $fields = [];
            $fields[$resolvedField] = [
                'query' => $transformedValue,
                'boost' => $match['boost'] ?? 1,
            ];

            if (isset($match['analyzer'])) {
                $fields[$resolvedField]['analyzer'] = $match['analyzer'];
            }
            $conditions[] = [
                'matchCondition' => $matchCondition,
                'fields' => $fields,
            ];
        }

        return $conditions;
    }
}
