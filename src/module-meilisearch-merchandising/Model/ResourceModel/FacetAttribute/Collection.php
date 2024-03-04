<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Model\ResourceModel\FacetAttribute;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Walkwizus\MeilisearchMerchandising\Model\FacetAttribute;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(FacetAttribute::class, \Walkwizus\MeilisearchMerchandising\Model\ResourceModel\FacetAttribute::class);
    }
}
