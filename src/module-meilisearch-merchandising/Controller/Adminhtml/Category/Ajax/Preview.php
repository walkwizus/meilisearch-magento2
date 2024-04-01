<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Controller\Adminhtml\Category\Ajax;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use Walkwizus\MeilisearchBase\Model\Adapter\Meilisearch;
use Walkwizus\MeilisearchMerchandising\Service\QueryBuilderService;
use Walkwizus\MeilisearchBase\SearchAdapter\SearchIndexNameResolver;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

class Preview extends Action implements HttpPostActionInterface
{
    /**
     * @param Context $context
     * @param Meilisearch $client
     * @param QueryBuilderService $queryBuilderService
     * @param SearchIndexNameResolver $searchIndexNameResolver
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        private Meilisearch $client,
        private QueryBuilderService $queryBuilderService,
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
        $page = max((int)$this->getRequest()->getParam('page'), 1);
        $limit = (int)$this->getRequest()->getParam('limit', 20);

        $filters = $this->queryBuilderService->convertRulesToMeilisearchQuery($rules);
        $indexName = $this->searchIndexNameResolver->getIndexName($storeId, 'catalog_product');

        $offset = ($page - 1) * $limit;

        $result = $this->client->search($indexName, '', [
            'filter' => $filters,
            'limit' => $limit,
            'offset' => $offset
        ]);

        return $this->jsonFactory->create()->setData($result->toArray());
    }
}
