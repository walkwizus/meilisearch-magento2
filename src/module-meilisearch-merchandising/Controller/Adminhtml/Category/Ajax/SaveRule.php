<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Controller\Adminhtml\Category\Ajax;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\Result\JsonFactory;
use Walkwizus\MeilisearchMerchandising\Model\CategoryFactory;
use Walkwizus\MeilisearchMerchandising\Api\CategoryRepositoryInterface;
use Magento\Framework\Controller\ResultInterface;

class SaveRule extends Action implements HttpPostActionInterface
{
    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param CategoryFactory $categoryFactory
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Context $context,
        private JsonFactory $jsonFactory,
        private CategoryFactory $categoryFactory,
        private CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $categoryId = $this->getRequest()->getParam('category_id', false);
        $rules = $this->getRequest()->getParam('rules', false);

        unset($rules['valid']);

        try {
            $categoryRule = $this->categoryRepository->getByCategoryId($categoryId);
            $categoryRule->setCategoryId($categoryId);
            $categoryRule->setQuery(json_encode($rules));
        } catch (NoSuchEntityException $e) {
            $categoryRule = $this->categoryFactory->create();
            $categoryRule->setCategoryId($categoryId);
            $categoryRule->setQuery(json_encode($rules));
        }

        $this->categoryRepository->save($categoryRule);

        $json = $this->jsonFactory->create();
        return $json->setData(['success'=> true, 'message' => __('Category was saved')]);
    }
}
