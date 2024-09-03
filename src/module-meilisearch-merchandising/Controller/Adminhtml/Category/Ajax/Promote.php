<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Controller\Adminhtml\Category\Ajax;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Walkwizus\MeilisearchBase\Model\Adapter\Meilisearch;
use Walkwizus\MeilisearchBase\SearchAdapter\SearchIndexNameResolver;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;

class Promote extends Action
{
    /**
     * @param Context $context
     * @param SearchIndexNameResolver $searchIndexNameResolver
     * @param Meilisearch $meilisearchAdapter
     */
    public function __construct(
        Context $context,
        private SearchIndexNameResolver $searchIndexNameResolver,
        private Meilisearch $meilisearchAdapter
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('product_id');
        $storeId = $this->getRequest()->getParam('store_id');
        $categoryId = $this->getRequest()->getParam('category_id');

        $indexName = $this->searchIndexNameResolver->getIndexName($storeId, 'catalog_product');

        $this->meilisearchAdapter->updateDocuments($indexName, [
            'id' => $productId,
            'category_promote' => [$categoryId => 0]
        ]);

        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setData(['message' => 'ok']);

        return $result;
    }
}
