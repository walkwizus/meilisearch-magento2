<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * @param $storeId
     * @return mixed
     */
    public function getProductUrlSuffix($storeId = null): mixed
    {
        return $this->scopeConfig->getValue(
            ProductUrlPathGenerator::XML_PATH_PRODUCT_URL_SUFFIX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
