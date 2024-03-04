<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Walkwizus\MeilisearchMerchandising\Model\ResourceModel\Facet\CollectionFactory;
use Walkwizus\MeilisearchMerchandising\Model\ResourceModel\Facet\Collection;
use Walkwizus\MeilisearchMerchandising\Model\ResourceModel\FacetAttribute\CollectionFactory as FacetAttributeCollectionFactory;
use Walkwizus\MeilisearchMerchandising\Model\ResourceModel\FacetAttribute\Collection as FacetAttributeCollection;

class Facet extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var FacetAttributeCollection
     */
    protected FacetAttributeCollection $facetAttributeCollection;

    /**
     * @var array
     */
    private array $loadedData;

    /**
     * @param $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param FacetAttributeCollectionFactory $facetAttributeCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        FacetAttributeCollectionFactory $facetAttributeCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->facetAttributeCollection = $facetAttributeCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        if (!isset($this->loadedData)) {
            $this->loadedData = [];

            foreach ($this->collection->getItems() as $item) {
                $itemId = $item->getData('id');
                $facetAttributeCollection = $this->facetAttributeCollection
                    ->addFieldToFilter('facet_id', $itemId)
                    ->setOrder('position', 'ASC');

                $this->loadedData[$itemId] = $item->getData();
                $this->loadedData[$itemId]['facet_attributes'] = $facetAttributeCollection->toArray()['items'];
            }
        }

        return $this->loadedData;
    }
}
