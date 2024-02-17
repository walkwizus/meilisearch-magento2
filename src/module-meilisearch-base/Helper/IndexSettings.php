<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\SerializerInterface;

class IndexSettings extends AbstractHelper
{
    const MEILISEARCH_INDICES_CONFIG_PATH = 'meilisearch_indices/%s/%s';

    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;

    /**
     * @param Context $context
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Context $context,
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    /**
     * @param string $index
     * @return string
     */
    public function getIndexPrefix(string $index): string
    {
        return $this->scopeConfig->getValue(sprintf(self::MEILISEARCH_INDICES_CONFIG_PATH, $index, 'prefix'));
    }

    /**
     * @param string $index
     * @return int
     */
    public function getIndexPagination(string $index): int
    {
        return (int)$this->scopeConfig->getValue(sprintf(self::MEILISEARCH_INDICES_CONFIG_PATH, $index, 'pagination'));
    }

    /**
     * @param string $index
     * @return array
     */
    public function getIndexRankingRules(string $index): array
    {
        $data = [];
        $rules = $this->scopeConfig->getValue(sprintf(self::MEILISEARCH_INDICES_CONFIG_PATH, $index, 'ranking_rules'));
        if (!is_array($rules)) {
            $rules = $this->serializer->unserialize($rules);
        }

        foreach ($rules as $rule) {
            $data[] = $rule['rule'];
        }

        return array_unique($data);
    }

    /**
     * @param string $index
     * @return bool
     */
    public function isTypoToleranceEnabled(string $index): bool
    {
        return (bool)$this->scopeConfig->getValue(sprintf(self::MEILISEARCH_INDICES_CONFIG_PATH, $index, 'typo_tolerance_enabled'));
    }

    /**
     * @param string $index
     * @return int
     */
    public function getTypoToleranceOneTypo(string $index): int
    {
        return (int)$this->scopeConfig->getValue(sprintf(self::MEILISEARCH_INDICES_CONFIG_PATH, $index, 'typo_tolerance_onetypo'));
    }

    /**
     * @param string $index
     * @return int
     */
    public function getTypoToleranceTwoTypo(string $index): int
    {
        return (int)$this->scopeConfig->getValue(sprintf(self::MEILISEARCH_INDICES_CONFIG_PATH, $index, 'typo_tolerance_twotypo'));
    }

    /**
     * @param string $index
     * @return array
     */
    public function getTypoToleranceDisableOnWords(string $index): array
    {
        $words = $this->scopeConfig->getValue(sprintf(self::MEILISEARCH_INDICES_CONFIG_PATH, $index, 'typo_disable_on_words'));
        return $words ? explode(',', $words) : [];
    }

    /**
     * @param string $index
     * @return array
     */
    public function getTypoToleranceDisableOnAttributes(string $index): array
    {
        $attributes = $this->scopeConfig->getValue(sprintf(self::MEILISEARCH_INDICES_CONFIG_PATH, $index, 'typo_tolerance_disable_on_attributes'));

        return $attributes ? explode(',', $attributes) : [];
    }

    /**
     * @param string $index
     * @return int
     */
    public function getFacetsMaxValue(string $index): int
    {
        return (int)$this->scopeConfig->getValue(sprintf(self::MEILISEARCH_INDICES_CONFIG_PATH, $index, 'facets_max_value'));
    }

    /**
     * @param string $index
     * @return string
     */
    public function getFacetsSortBy(string $index): string
    {
        return $this->scopeConfig->getValue(sprintf(self::MEILISEARCH_INDICES_CONFIG_PATH, $index, 'facets_sort_by')) ?? 'alpha';
    }
}
