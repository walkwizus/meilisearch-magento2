<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Facet extends AbstractDb
{
    const MAIN_TABLE = 'meilisearch_merchandising_facet';

    const ID_FIELD_NAME = 'id';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD_NAME);
    }
}
