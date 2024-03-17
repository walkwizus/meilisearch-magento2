<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Controller\Adminhtml\Category\Ajax;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use Walkwizus\MeilisearchMerchandising\Service\QueryBuilder\MeilisearchConverter;
use Walkwizus\MeilisearchBase\SearchAdapter\SearchIndexNameResolver;
use Magento\Framework\Controller\Result\JsonFactory;
use Walkwizus\MeilisearchBase\Model\Adapter\Meilisearch;
use Magento\Framework\Controller\ResultInterface;

class Preview extends Action implements HttpPostActionInterface
{
    /**
     * @param Context $context
     * @param Meilisearch $client
     * @param MeilisearchConverter $meilisearchConverter
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        private Meilisearch $client,
        private MeilisearchConverter $meilisearchConverter,
        private SearchIndexNameResolver $searchIndexNameResolver,
        private JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $rules = $this->getRequest()->getParam('rules');
        $storeId = $this->getRequest()->getParam('storeId');

        $filters = $this->meilisearchConverter->buildMeilisearchQuery($rules);
        $indexName = $this->searchIndexNameResolver->getIndexName($storeId, 'catalog_product');
        $result = $this->client->search($indexName, '', ['filter' => $filters]);

        return $this->jsonFactory->create()->setData($result->toArray());
    }
}
