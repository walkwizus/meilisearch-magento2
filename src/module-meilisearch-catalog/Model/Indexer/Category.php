<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchCatalog\Model\Indexer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Walkwizus\MeilisearchBase\Model\Adapter\Meilisearch as MeilisearchAdapter;
use Magento\Framework\Search\Request\DimensionFactory;
use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Walkwizus\MeilisearchCatalog\Model\ResourceModel\Indexer\Category\Action\Full as FullAction;

class Category implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var MeilisearchAdapter
     */
    protected MeilisearchAdapter $meilisearchAdapter;

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
     * @param MeilisearchAdapter $meilisearchAdapter
     * @param StoreManagerInterface $storeManager
     * @param DimensionFactory $dimensionFactory
     * @param IndexerInterface $indexerHandler
     * @param FullAction $fullAction
     */
    public function __construct(
        MeilisearchAdapter $meilisearchAdapter,
        StoreManagerInterface $storeManager,
        DimensionFactory $dimensionFactory,
        IndexerInterface $indexerHandler,
        FullAction $fullAction
    ) {
        $this->meilisearchAdapter = $meilisearchAdapter;
        $this->storeManager = $storeManager;
        $this->dimensionFactory = $dimensionFactory;
        $this->indexerHandler = $indexerHandler;
        $this->fullAction = $fullAction;
    }

    /**
     * @param $ids
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute($ids)
    {
        if (!$this->meilisearchAdapter->isHealthy()) {
            return;
        }

        $storeIds = $this->getStoreIds();

        foreach ($storeIds as $storeId) {
            $dimension = $this->dimensionFactory->create(['name' => 'scope', 'value' => $storeId]);
            $this->indexerHandler->deleteIndex([$dimension], new \ArrayObject($ids));
            $this->indexerHandler->saveIndex([$dimension], new \ArrayObject($this->fullAction->getCategories($storeId, $ids)));
        }
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function executeFull()
    {
        $this->execute([]);
    }

    /**
     * @param array $ids
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    /**
     * @param $id
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function executeRow($id)
    {
        $this->execute([$id]);
    }

    /**
     * @return array
     */
    private function getStoreIds(): array
    {
        return array_keys($this->storeManager->getStores());
    }
}
