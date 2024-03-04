<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Model;

use Magento\Framework\Model\AbstractModel;
use Walkwizus\MeilisearchMerchandising\Api\Data\FacetInterface;

class Facet extends AbstractModel implements FacetInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Facet::class);
    }

    /**
     * @ingeritdoc
     */
    public function getIndexName()
    {
        return $this->getData(self::INDEX_NAME);
    }

    /**
     * @ingeritdoc
     */
    public function setIndexName($indexName)
    {
        return $this->setData(self::INDEX_NAME, $indexName);
    }
}
