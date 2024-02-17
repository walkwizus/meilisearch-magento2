<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchCatalog\Service;

use Magento\Catalog\Model\Session as CatalogSession;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class GetCurrentCategoryService
{
    /**
     * @var CategoryInterface|null
     */
    private ?CategoryInterface $currentCategory = null;

    /**
     * Current Category ID
     *
     * @var int|null
     */
    private ?int $categoryId = null;

    /**
     * @var CatalogSession
     */
    private CatalogSession $catalogSession;

    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @param CatalogSession $catalogSession
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        CatalogSession $catalogSession,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->catalogSession = $catalogSession;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return int|null
     */
    public function getCategoryId(): ?int
    {
        if (is_null($this->categoryId)) {
            $currentCategoryId = $this->catalogSession->getData('last_viewed_category_id');
            if ($currentCategoryId) {
                $this->categoryId = (int)$currentCategoryId;
            }
        }

        return $this->categoryId;
    }
    /**
     * @return CategoryInterface|null
     */
    public function getCategory(): ?CategoryInterface
    {
        if (is_null($this->currentCategory)) {
            $categoryId = $this->getCategoryId();
            if (!$categoryId) {
                return null;
            }
            try {
                $this->currentCategory = $this->categoryRepository->get($categoryId);
            } catch (NoSuchEntityException $e) {
                return null;
            }
        }

        return $this->currentCategory;
    }
}
