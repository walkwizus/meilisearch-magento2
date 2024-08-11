<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Model\ResourceModel;

use Magento\CatalogSearch\Model\ResourceModel\EngineInterface;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Magento\Catalog\Model\Product\Visibility;

class Engine implements EngineInterface
{
    /**
     * @var Visibility
     */
    private Visibility $catalogProductVisibility;

    /**
     * @param Visibility $catalogProductVisibility
     */
    public function __construct(
        Visibility $catalogProductVisibility
    ) {
        $this->catalogProductVisibility = $catalogProductVisibility;
    }

    /**
     * @return array
     */
    public function getAllowedVisibility(): array
    {
        return $this->catalogProductVisibility->getVisibleInSiteIds();
    }

    /**
     * @return bool
     */
    public function allowAdvancedIndex(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function processAttributeValue($attribute, $value)
    {
        return $value;
    }

    /**
     * @param $index
     * @param string $separator
     * @return array
     */
    public function prepareEntityIndex($index, $separator = ' '): array
    {
        return $index;
    }

    /**
     * @inheritdoc
     */
    public function isAvailable(): bool
    {
        return true;
    }
}
