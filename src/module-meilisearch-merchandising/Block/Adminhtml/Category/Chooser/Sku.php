<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Block\Adminhtml\Category\Chooser;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class Sku extends Template
{
    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        Template\Context $context,
        protected CollectionFactory $collectionFactory,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    public function getProductCollection()
    {

    }
}
