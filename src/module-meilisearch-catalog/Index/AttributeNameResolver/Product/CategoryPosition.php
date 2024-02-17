<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchCatalog\Index\AttributeNameResolver\Product;

use Walkwizus\MeilisearchBase\Api\Index\AttributeNameResolverInterface;
use Walkwizus\MeilisearchCatalog\Service\GetCurrentCategoryService;

class CategoryPosition implements AttributeNameResolverInterface
{
    /**
     * @var GetCurrentCategoryService
     */
    protected GetCurrentCategoryService $getCurrentCategoryService;

    /**
     * @param GetCurrentCategoryService $getCurrentCategoryService
     */
    public function __construct(GetCurrentCategoryService $getCurrentCategoryService)
    {
        $this->getCurrentCategoryService = $getCurrentCategoryService;
    }

    /**
     * @param string $attributeName
     * @return string
     */
    public function resolve(string $attributeName): string
    {
        $categoryId = $this->getCurrentCategoryService->getCategoryId();
        return $attributeName . '_category_' . $categoryId ?? '0';
    }
}
