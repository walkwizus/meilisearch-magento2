<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Api\Index;

interface AttributeNameResolverInterface
{
    /**
     * @param string $attributeName
     * @return string
     */
    public function resolve(string $attributeName): string;
}
