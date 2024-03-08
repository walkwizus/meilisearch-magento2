<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Controller\Adminhtml\Category\Ajax;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use Walkwizus\MeilisearchMerchandising\Service\QueryBuilder\MeilisearchConverter;
use Magento\Framework\Controller\Result\JsonFactory;
use Walkwizus\MeilisearchBase\Model\Adapter\Meilisearch;

class Preview extends Action implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        private Meilisearch $client,
        private MeilisearchConverter $meilisearchConverter,
        private JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $rules = $this->getRequest()->getParam('rules');
        $query = $this->meilisearchConverter->buildMeilisearchQuery($rules);
        $result = $this->client->search(1, 'catalog_product', '', $query);

        return $this->jsonFactory->create()->setData($result->toArray());
    }
}
