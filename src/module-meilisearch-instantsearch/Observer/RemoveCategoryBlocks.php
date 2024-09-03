<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchInstantSearch\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Walkwizus\MeilisearchInstantSearch\Helper\Data as MeilisearchHelper;

class RemoveCategoryBlocks implements ObserverInterface
{
    /**
     * @var array|string[]
     */
    private array $blocksToRemove = [
        'category.products',
        'catalog.leftnav'
    ];

    /**
     * @param MeilisearchHelper $meilisearchHelper
     */
    public function __construct(private MeilisearchHelper $meilisearchHelper) {}

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $layout = $observer->getLayout();

        if ($this->meilisearchHelper->isInstantSearchEnabled()) {
            foreach ($this->blocksToRemove as $blockToRemove) {
                $block = $layout->getBlock($blockToRemove);
                if ($block) {
                    $layout->unsetElement($blockToRemove);
                }
            }
        }
    }
}
