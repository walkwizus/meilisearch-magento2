<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchInstantSearch\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const XML_MEILISEARCH_FRONTEND_AUTOCOMPLETE_ENABLED = 'meilisearch_frontend/autocomplete/enabled';
    const XML_MEILISEARCH_FRONTEND_INSTANTSEARCH_ENABLED = 'meilisearch_frontend/instantsearch/enabled';

    /**
     * @return mixed
     */
    public function isAutocompleteEnabled(): mixed
    {
        return $this->scopeConfig->getValue(self::XML_MEILISEARCH_FRONTEND_AUTOCOMPLETE_ENABLED);
    }

    /**
     * @return mixed
     */
    public function isInstantSearchEnabled(): mixed
    {
        return $this->scopeConfig->getValue(self::XML_MEILISEARCH_FRONTEND_INSTANTSEARCH_ENABLED);
    }
}
