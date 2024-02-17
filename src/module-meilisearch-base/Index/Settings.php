<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Index;

use Walkwizus\MeilisearchBase\Api\Index\SettingsInterface;
use Walkwizus\MeilisearchBase\Helper\IndexSettings;

class Settings implements SettingsInterface
{
    /**
     * @var IndexSettings
     */
    protected IndexSettings $indexSettings;

    /**
     * @var AttributeProvider
     */
    protected AttributeProvider $attributeProvider;

    /**
     * @param IndexSettings $indexSettings
     * @param AttributeProvider $attributeProvider
     */
    public function __construct(
        IndexSettings $indexSettings,
        AttributeProvider $attributeProvider
    ) {
        $this->indexSettings = $indexSettings;
        $this->attributeProvider = $attributeProvider;
    }

    /**
     * @param string $indexName
     * @return array
     */
    public function getSettings(string $indexName): array
    {
        return [
            'displayedAttributes' => ['*'],
            'distinctAttribute' => null,
            'faceting' => [
                'maxValuesPerFacet' => $this->indexSettings->getFacetsMaxValue($indexName),
                'sortFacetValuesBy' => [
                    '*' => $this->indexSettings->getFacetsSortBy($indexName)
                ]
            ],
            'filterableAttributes' => $this->attributeProvider->getFilterableAttributes($indexName),
            'pagination' => [
                'maxTotalHits' => $this->indexSettings->getIndexPagination($indexName)
            ],
            'rankingRules' => $this->indexSettings->getIndexRankingRules($indexName),
            'searchableAttributes' => $this->attributeProvider->getSearchableAttributes($indexName),
            'sortableAttributes' => $this->attributeProvider->getSortableAttributes($indexName),
            'stopWords' => [],
            //'synonyms' => [],
            'typoTolerance' => [
                'enabled' => $this->indexSettings->isTypoToleranceEnabled($indexName),
                'minWordSizeForTypos' => [
                    'oneTypo' => $this->indexSettings->getTypoToleranceOneTypo($indexName),
                    'twoTypos' => $this->indexSettings->getTypoToleranceTwoTypo($indexName),
                ],
                'disableOnWords' => $this->indexSettings->getTypoToleranceDisableOnWords($indexName),
                'disableOnAttributes' => $this->indexSettings->getTypoToleranceDisableOnAttributes($indexName),
            ]
        ];
    }
}
