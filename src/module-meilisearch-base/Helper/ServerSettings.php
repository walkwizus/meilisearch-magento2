<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class ServerSettings extends AbstractHelper
{
    const MEILISEARCH_SERVER_SETTINGS_ADDRESS = 'meilisearch_server/settings/address';
    const MEILISEARCH_SERVER_SETTINGS_API_KEY = 'meilisearch_server/settings/api_key';
    const MEILISEARCH_SERVER_SETTINGS_CLIENT_ADDRESS = 'meilisearch_server/settings/client_address';
    const MEILISEARCH_SERVER_SETTINGS_CLIENT_API_KEY = 'meilisearch_server/settings/client_api_key';

    /**
     * @return string
     */
    public function getServerSettingsAddress(): string
    {
        return $this->scopeConfig->getValue(self::MEILISEARCH_SERVER_SETTINGS_ADDRESS) ?? '';
    }

    /**
     * @return mixed
     */
    public function getServerSettingsApiKey(): mixed
    {
        return $this->scopeConfig->getValue(self::MEILISEARCH_SERVER_SETTINGS_API_KEY);
    }

    /**
     * @return string
     */
    public function getServerSettingsClientAddress(): string
    {
        return $this->scopeConfig->getValue(self::MEILISEARCH_SERVER_SETTINGS_CLIENT_ADDRESS) ?? '';
    }

    /**
     * @return string
     */
    public function getServerSettingsClientApiKey(): string
    {
        return $this->scopeConfig->getValue(self::MEILISEARCH_SERVER_SETTINGS_CLIENT_API_KEY) ?? '';
    }
}
