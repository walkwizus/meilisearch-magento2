<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Controller\Adminhtml\Category\Ajax;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Action\FullFactory;
use Walkwizus\MeilisearchBase\Index\AttributeMapper;
use Walkwizus\MeilisearchBase\Api\Index\SettingsInterface;
use Walkwizus\MeilisearchBase\Model\Adapter\Meilisearch;

class Promote extends Action
{
    /**
     * @param Context $context
     * @param FullFactory $fullFactory
     * @param AttributeMapper $attributeMapper
     * @param SettingsInterface $settings
     * @param Meilisearch $meilisearchAdapter
     */
    public function __construct(
        Context $context,
        private FullFactory $fullFactory,
        private AttributeMapper $attributeMapper,
        private SettingsInterface $settings,
        private Meilisearch $meilisearchAdapter
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $productId = $this->getRequest()->getParam('product_id');
        $storeId = $this->getRequest()->getParam('store_id');
        $categoryId = $this->getRequest()->getParam('category_id');

        $full = $this->fullFactory->create();
        $documents = $full->rebuildStoreIndex(1, [$productId]);

        $indexName = 'merchandising_category_' . $storeId;

        $this->meilisearchAdapter->updateSettings($indexName, $this->settings->getSettings('catalog_product'));

        foreach ($documents as $document) {
            $product[$productId] = $document;
            $productData = $this->attributeMapper->map('catalog_product', $product, $storeId);
            $this->meilisearchAdapter->addDocs($indexName, $productData, 'id');
        }
    }
}
