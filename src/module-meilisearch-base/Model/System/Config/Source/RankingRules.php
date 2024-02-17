<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class RankingRules implements OptionSourceInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['label' => 'Words', 'value' => 'words'],
            ['label' => 'Typo', 'value' => 'typo'],
            ['label' => 'Proximity', 'value' => 'proximity'],
            ['label' => 'Attribute', 'value' => 'attribute'],
            ['label' => 'Sort', 'value' => 'sort'],
            ['label' => 'Exactness', 'value' => 'exactness'],
        ];
    }
}
