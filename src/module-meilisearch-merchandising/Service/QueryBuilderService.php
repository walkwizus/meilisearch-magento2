<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Service;

use Walkwizus\MeilisearchMerchandising\Model\AttributeRuleProvider;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Walkwizus\MeilisearchBase\Index\AttributeNameResolver;

class QueryBuilderService
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
     * @param AttributeRuleProvider $attributeRuleProvider
     * @param AttributeRepositoryInterface $attributeRepository
     * @param AttributeNameResolver $attributeNameResolver
     */
    public function __construct(
        private AttributeRuleProvider $attributeRuleProvider,
        private AttributeRepositoryInterface $attributeRepository,
        private AttributeNameResolver $attributeNameResolver
    ) { }

    /**
     * @param array $rule
     * @return string
     */
    public function convertRulesToMeilisearchQuery(array $rule): string
    {
        return $this->buildQuery($rule);
    }

    /**
     * @param array $rule
     * @return string
     */
    private function buildQuery(array $rule): string
    {
        $meilisearchQuery = '';
        $condition = $rule['condition'] ?? 'AND';

        if (isset($rule['rules']) && is_array($rule['rules'])) {
            $subQueries = [];
            foreach ($rule['rules'] as $subRule) {
                $subQuery = $this->buildQuery($subRule);
                if (!empty($subQuery)) {
                    $subQueries[] = "($subQuery)";
                }
            }
            if (!empty($subQueries)) {
                $meilisearchQuery = implode(" $condition ", $subQueries);
            }
        } else {
            $field = $this->attributeNameResolver->getName($rule['field'], 'catalog_product');
            $operator = $this->operatorMapper[$rule['operator']];
            if (is_array($rule['value'])) {
                $value = "[" . implode(", ", array_map(function ($val) {
                        return is_numeric($val) ? $val : "\"$val\"";
                    }, $rule['value'])) . "]";
            } else {
                $value = (is_numeric($rule['value'])) ? $rule['value'] : "\"{$rule['value']}\"";
            }
            $meilisearchQuery = "$field $operator $value";
        }

        return $meilisearchQuery;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function convertAttributesToRules(): array
    {
        $attributes = $this->attributeRuleProvider->getAttributes();
        $rules = [];

        foreach ($attributes as $attribute) {
            $rule = [
                'id' => $attribute['code'],
                'label' => $attribute['label'],
                'operator' => 'equal',
            ];

            switch ($attribute['type']) {
                case 'text':
                    $rule['type'] = 'string';
                    $rule['operators'] = ['contains', 'not_contains'];
                    break;
                case 'select':
                    $rule['type'] = 'integer';
                    $rule['input'] = 'select';
                    $rule['operators'] = ['equal', 'not_equal'];
                    $rule['values'] = $this->getSelectValues($attribute['code']);
                    break;
                case 'multiselect':
                    $rule['type'] = 'integer';
                    $rule['input'] = 'select';
                    $rule['operators'] = ['in', 'not_in'];
                    $rule['multiple'] = true;
                    $rule['values'] = $this->getSelectValues($attribute['code']);
                    break;
                case 'boolean':
                    $rule['type'] = 'boolean';
                    $rule['input'] = 'radio';
                    $rule['values'] = [1 => 'Yes', 0 => 'No'];
                    break;
                case 'price':
                    $rule['type'] = 'double';
                    $rule['input'] = 'number';
                    break;
            }

            $rules[] = $rule;
        }

        return $rules;
    }

    /**
     * @param $attributeCode
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getSelectValues($attributeCode): array
    {
        $values = [];
        $attribute = $this->attributeRepository->get('catalog_product', $attributeCode);
        $options = $attribute->getSource()->getAllOptions();

        foreach ($options as $option) {
            if (isset($option['value'])) {
                $values[$option['value']] = $option['label'];
            }
        }

        return $values;
    }
}
