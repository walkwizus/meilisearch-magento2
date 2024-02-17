<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchCatalog\Index\AttributeNameResolver\Product;

use Walkwizus\MeilisearchBase\Api\Index\AttributeNameResolverInterface;
use Magento\Customer\Model\Session as CustomerSession;

class Price implements AttributeNameResolverInterface
{
    /**
     * @var CustomerSession
     */
    protected CustomerSession $customerSession;

    /**
     * @param CustomerSession $customerSession
     */
    public function __construct(CustomerSession $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    /**
     * @param string $attributeName
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function resolve(string $attributeName): string
    {
        return 'price_' . $this->customerSession->getCustomerGroupId();
    }
}
