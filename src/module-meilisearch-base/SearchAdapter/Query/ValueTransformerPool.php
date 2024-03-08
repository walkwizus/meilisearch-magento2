<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\SearchAdapter\Query;

class ValueTransformerPool
{
    /**
     * @param ValueTransformerInterface[] $valueTransformers
     */
    public function __construct(private array $valueTransformers = [])
    {
        foreach ($valueTransformers as $valueTransformer) {
            if (!$valueTransformer instanceof ValueTransformerInterface) {
                throw new \InvalidArgumentException(
                    sprintf('"%s" is not an instance of ValueTransformerInterface.', get_class($valueTransformer))
                );
            }
        }

        $this->transformers = $valueTransformers;
    }

    /**
     * Get value transformer related to field type.
     *
     * @param string $fieldType
     * @return ValueTransformerInterface|null
     */
    public function get(string $fieldType): ?ValueTransformerInterface
    {
        return $this->transformers[$fieldType] ?? null;
    }
}
