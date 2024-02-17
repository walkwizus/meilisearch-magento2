<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter\Query\Builder;

use Magento\Framework\Search\RequestInterface;
use Walkwizus\MeilisearchBase\SearchAdapter\SearchIndexNameResolver;
use Walkwizus\MeilisearchBase\Index\AttributeProvider;
use Walkwizus\MeilisearchBase\Index\AttributeNameResolver;

class Sort
{
    /**
     * List of fields that need to skipp by default.
     */
    private const DEFAULT_SKIPPED_FIELDS = [
        'entity_id',
        'relevance'
    ];

    /**
     * @var SearchIndexNameResolver
     */
    protected SearchIndexNameResolver $searchIndexNameResolver;

    /**
     * @var AttributeProvider
     */
    protected AttributeProvider $attributeProvider;

    /**
     * @var AttributeNameResolver
     */
    protected AttributeNameResolver $attributeNameResolver;

    /**
     * @var array
     */
    protected array $skippedFields = [];

    /**
     * @param SearchIndexNameResolver $searchIndexNameResolver
     * @param AttributeProvider $attributeProvider
     * @param AttributeNameResolver $attributeNameResolver
     * @param array $skippedFields
     */
    public function __construct(
        SearchIndexNameResolver $searchIndexNameResolver,
        AttributeProvider $attributeProvider,
        AttributeNameResolver $attributeNameResolver,
        array $skippedFields = []
    ) {
        $this->searchIndexNameResolver = $searchIndexNameResolver;
        $this->attributeProvider = $attributeProvider;
        $this->attributeNameResolver = $attributeNameResolver;
        $this->skippedFields = array_merge(self::DEFAULT_SKIPPED_FIELDS, $skippedFields);
    }

    /**
     * @param RequestInterface $request
     * @return array
     */
    public function getSort(RequestInterface $request): array
    {
        $sorts = [];

        if (!method_exists($request, 'getSort')) {
            return $sorts;
        }

        foreach ($request->getSort() as $sort) {
            if (in_array($sort['field'], $this->skippedFields)) {
                continue;
            }

            $indexName = $this->searchIndexNameResolver->getIndexMapping($request->getIndex());
            $fieldName = $this->attributeNameResolver->getName($sort['field'], $indexName);
            $sorts[] = $fieldName . ':' . strtolower($sort['direction']);
        }

        return array_values($sorts);
    }
}
