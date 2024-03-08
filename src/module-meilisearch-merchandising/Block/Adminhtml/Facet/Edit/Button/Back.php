<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Block\Adminhtml\Facet\Edit\Button;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Back implements ButtonProviderInterface
{
    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        private UrlInterface $urlBuilder
    ) { }

    /**
     * @return array
     */
    public function getButtonData(): array
    {
        $url = $this->urlBuilder->getUrl('*/*/');

        return [
            'label' => __('Back'),
            'class' => 'back',
            'on_click' => sprintf("location.href = '%s';", $url),
        ];
    }
}
