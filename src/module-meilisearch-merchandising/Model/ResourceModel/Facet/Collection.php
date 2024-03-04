<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Model\ResourceModel\Facet;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Walkwizus\MeilisearchMerchandising\Model\Facet;
use Walkwizus\MeilisearchMerchandising\Model\ResourceModel\FacetAttribute;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(Facet::class, \Walkwizus\MeilisearchMerchandising\Model\ResourceModel\Facet::class);
    }
}
