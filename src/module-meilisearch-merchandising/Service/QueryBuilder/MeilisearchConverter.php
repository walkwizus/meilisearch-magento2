<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Service\QueryBuilder;

class MeilisearchConverter
{
    /**
     * @var array
     */
    private array $operatorMapper = [
        'equal' => '=',
        'not_equal' => '!=',
        'in' => 'IN',
        'not_in' => 'NOT IN',
        'less' => '<',
        'less_or_equal' => '<=',
        'greater' => '>',
        'greater_or_equal' => '>=',
        'between' => 'TO',
        'is_null' => 'IS NULL',
        'is_not_null' => 'IS NOT NULL'
    ];

    /**
     * @param array $rule
     * @return array
     */
    public function buildMeilisearchQuery(array $rule): array
    {
        if (!$rule['valid']) {
            return [];
        }

        return $this->buildQuery($rule);
    }

    /**
     * @param array $rule
     * @return array
     */
    private function buildQuery(array $rule): array
    {
        $meilisearchQuery = [];
        $condition = $rule['condition'] ?? 'AND';

        if (isset($rule['rules']) && is_array($rule['rules'])) {
            foreach ($rule['rules'] as $subRule) {
                $subQuery = $this->buildQuery($subRule);
                if (!empty($subQuery)) {
                    $meilisearchQuery[] = $subQuery;
                }
            }
        } else {
            $field = $rule['field'];
            $operator = $this->operatorMapper[$rule['operator']];
            $value = $rule['value'];

            $meilisearchQuery[] = "$field $operator $value";
        }

        if (!empty($meilisearchQuery)) {
            return ($condition === 'OR') ? [$meilisearchQuery] : $meilisearchQuery;
        }

        return [];
    }
}
