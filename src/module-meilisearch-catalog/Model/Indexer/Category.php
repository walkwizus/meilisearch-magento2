<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchCatalog\Model\Indexer;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Search\Request\DimensionFactory;
use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Walkwizus\MeilisearchCatalog\Model\ResourceModel\Indexer\Category\Action\Full as FullAction;

class Category implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var DimensionFactory
     */
    protected DimensionFactory $dimensionFactory;

    /**
     * @var IndexerInterface
     */
    protected IndexerInterface $indexerHandler;

    /**
     * @var FullAction
     */
    protected FullAction $fullAction;

    /**
     * @param StoreManagerInterface $storeManager
     * @param DimensionFactory $dimensionFactory
     * @param IndexerInterface $indexerHandler
     * @param FullAction $fullAction
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        DimensionFactory $dimensionFactory,
        IndexerInterface $indexerHandler,
        FullAction $fullAction
    ) {
        $this->storeManager = $storeManager;
        $this->dimensionFactory = $dimensionFactory;
        $this->indexerHandler = $indexerHandler;
        $this->fullAction = $fullAction;
    }

    /**
     * @param $ids
     * @return void
     */
    public function execute($ids)
    {
        $storeIds = $this->getStoreIds();

        foreach ($storeIds as $storeId) {
            $dimension = $this->dimensionFactory->create(['name' => 'scope', 'value' => $storeId]);
            $this->indexerHandler->deleteIndex([$dimension], new \ArrayObject($ids));
            $this->indexerHandler->saveIndex([$dimension], new \ArrayObject($this->fullAction->getCategories($storeId, $ids)));
        }
    }

    /**
     * @return void
     */
    public function executeFull()
    {
        $storeIds = $this->getStoreIds();

        foreach ($storeIds as $storeId) {
            $dimension = $this->dimensionFactory->create(['name' => 'scope', 'value' => $storeId]);
            $this->indexerHandler->cleanIndex([$dimension]);
            $this->indexerHandler->saveIndex([$dimension], new \ArrayObject($this->fullAction->getCategories($storeId)));
        }
    }

    public function executeList(array $ids)
    {
        // TODO: Implement executeList() method.
    }

    public function executeRow($id)
    {
        // TODO: Implement executeRow() method.
    }

    /**
     * @return array
     */
    private function getStoreIds(): array
    {
        return array_keys($this->storeManager->getStores());
    }
}
