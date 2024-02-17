<?php

namespace Walkwizus\MeilisearchBase\SearchAdapter\Query;

interface ValueTransformerInterface
{
    /**
     * Transform the given value for the specific field type.
     *
     * @param mixed $value
     * @return mixed
     */
    public function transform($value);
}
