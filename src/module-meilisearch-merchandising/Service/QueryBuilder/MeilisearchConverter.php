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
        //'not_between',
        'is_null' => 'IS NULL',
        'is_not_null' => 'IS NOT NULL'
    ];

    public function buildMeilisearchQuery($rule)
    {
        if (isset($rule['condition'])) {
            $subQueries = array_map([$this, 'buildMeilisearchQuery'], $rule['rules']);
            $condition = strtolower($rule['condition']);
            return '(' . implode(" $condition ", $subQueries) . ')';
        } else {
            $field = $rule['field'];
            $operator = strtolower($rule['operator']);
            $value = $rule['value'];

            switch ($operator) {
                case 'equal':
                    return "$field:$value";
                case 'not_equal':
                    return "$field:NOT $value";
                case 'not_in':
                    return "NOT $field:$value";
                default:
                    return '';
            }
        }
    }
}
