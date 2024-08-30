<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Index;

class AttributeNameResolver
{
    /**
     * @param array $resolvers
     */
    public function __construct(
        private array $resolvers = []
    ) { }

    /**
     * @param string $attributeName
     * @param $indexName
     * @return string
     */
    public function getName(string $attributeName, $indexName): string
    {
        $resolvers = $this->resolve($indexName);

        foreach ($resolvers as $key => $resolver) {
            if ($attributeName == $key) {
                return $resolver->resolve($attributeName);
            }
        }

        return $attributeName;
    }

    /**
     * @param $indexName
     * @return array
     */
    private function resolve($indexName): array
    {
        return $this->resolvers[$indexName] ?? [];
    }
}
