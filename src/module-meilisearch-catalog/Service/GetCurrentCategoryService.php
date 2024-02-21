<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchCatalog\Service;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class GetCurrentCategoryService
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @param RequestInterface $request
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        RequestInterface $request,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->request = $request;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return int|null
     */
    public function getCategoryId(): ?int
    {
        return (int)$this->request->getParam('id', false);
    }

    /**
     * @return CategoryInterface|false
     */
    public function getCategory(): ?CategoryInterface
    {
        $categoryId = $this->getCategoryId();
        if ($categoryId) {
            try {
                return $this->categoryRepository->get($categoryId);
            } catch (NoSuchEntityException|\Exception $e) {
                return false;
            }
        }

        return false;
    }
}
