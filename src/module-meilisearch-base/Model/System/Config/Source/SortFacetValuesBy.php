<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class SortFacetValuesBy implements OptionSourceInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            [
                'label' => 'Alpha',
                'value' => 'alpha'
            ],
            [
                'label' => 'Count',
                'value' => 'count'
            ]
        ];
    }
}
