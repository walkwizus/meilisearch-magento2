<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Walkwizus\MeilisearchMerchandising\Model\ResourceModel\MerchandisingCategory\Collection;
use Walkwizus\MeilisearchMerchandising\Model\ResourceModel\MerchandisingCategory\CollectionFactory;

class MerchBuilder extends AbstractDataProvider
{
    protected $collection;

    private array $loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if (!isset($this->loadedData)) {
            $this->loadedData = [];
            foreach ($this->collection->getItems() as $item) {
                $this->loadedData[$item->getData('id')] = $item->getData();
            }
        }
        return $this->loadedData;
    }
}
