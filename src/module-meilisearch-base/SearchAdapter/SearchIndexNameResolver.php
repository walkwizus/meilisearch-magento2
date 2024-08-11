<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter;

use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Walkwizus\MeilisearchBase\Helper\IndexSettings;

class SearchIndexNameResolver
{
    const DEFAULT_INDEX = 'catalog_product';

    /**
     * @var IndexSettings
     */
    private IndexSettings $indexSettings;

    /**
     * @param IndexSettings $indexSettings
     */
    public function __construct(IndexSettings $indexSettings)
    {
        $this->indexSettings = $indexSettings;
    }

    /**
     * @param $storeId
     * @param $indexerId
     * @return string
     */
    public function getIndexName($storeId, $indexerId): string
    {
        $mappedIndexerId = $this->getIndexMapping($indexerId);
        $prefix = $this->indexSettings->getIndexPrefix($mappedIndexerId);

        if ($prefix != '') {
            $prefix = $prefix . '_';
        }

        return $prefix . $mappedIndexerId . '_' . $storeId;
    }

    /**
     * @param $indexerId
     * @return string
     */
    public function getIndexMapping($indexerId): string
    {
        return ($indexerId == Fulltext::INDEXER_ID) ? self::DEFAULT_INDEX : $indexerId;
    }
}
