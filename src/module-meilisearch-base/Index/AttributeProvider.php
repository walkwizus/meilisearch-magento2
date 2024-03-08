<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Index;

use Walkwizus\MeilisearchBase\Api\Index\AttributeProviderInterface;

class AttributeProvider
{
    /**
     * @param AttributeProviderInterface[] $providers
     */
    public function __construct(
        private array $providers = []
    ) { }

    /**
     * @param string $indexName
     * @return array
     */
    public function getFilterableAttributes(string $indexName): array
    {
        $providers = $this->resolve($indexName);
        return $this->getAttributes('filterable', $providers);
    }

    /**
     * @param string $indexName
     * @return array
     */
    public function getSearchableAttributes(string $indexName): array
    {
        $providers = $this->resolve($indexName);
        return $this->getAttributes('searchable', $providers);
    }

    /**
     * @param string $indexName
     * @return array
     */
    public function getSortableAttributes(string $indexName): array
    {
        $providers = $this->resolve($indexName);
        return $this->getAttributes('sortable', $providers);
    }

    /**
     * @param string $type
     * @param array $providers
     * @return array
     */
    private function getAttributes(string $type, array $providers): array
    {
        $attributes = [];
        foreach ($providers as $provider) {
            if (!$provider instanceof AttributeProviderInterface) {
                throw new \LogicException('Attribute provider must implement "Walkwizus\MeilisearchBase\Api\Index\AttributeProviderInterface".');
            }
            $method = 'get' . ucfirst($type) . 'Attributes';
            $attributes += array_flip($provider->$method());
        }

        return array_keys($attributes);
    }

    /**
     * @param $indexName
     * @return AttributeProviderInterface|array
     */
    private function resolve($indexName): AttributeProviderInterface|array
    {
        return $this->providers[$indexName] ?? [];
    }
}
