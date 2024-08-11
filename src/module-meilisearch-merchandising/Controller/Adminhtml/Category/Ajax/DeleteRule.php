<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Controller\Adminhtml\Category\Ajax;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Walkwizus\MeilisearchMerchandising\Model\CategoryFactory;
use Walkwizus\MeilisearchMerchandising\Api\CategoryRepositoryInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;

class DeleteRule extends Action implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    private JsonFactory $jsonFactory;

    /**
     * @var CategoryFactory
     */
    private CategoryFactory $categoryFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param CategoryFactory $categoryFactory
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        CategoryFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $categoryId = $this->getRequest()->getParam('category_id', false);

        try {
            $this->categoryRepository->deleteByCategoryId($categoryId);
        } catch (CouldNotDeleteException $couldNotDeleteException) {

        }

        $json = $this->jsonFactory->create();
        return $json->setData(['success'=> true, 'message' => __('Category was deleted')]);
    }
}
