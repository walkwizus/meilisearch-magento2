<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Api\Index;

interface SettingsInterface
{
    public function getSettings(string $indexName): array;
}
