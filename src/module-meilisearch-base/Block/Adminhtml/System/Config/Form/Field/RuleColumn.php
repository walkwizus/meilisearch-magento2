<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Context;
use Walkwizus\MeilisearchBase\Model\System\Config\Source\RankingRules;

class RuleColumn extends Select
{
    /**
     * @var RankingRules
     */
    protected RankingRules $rankingRules;

    /**
     * @param Context $context
     * @param RankingRules $rankingRules
     * @param array $data
     */
    public function __construct(
        Context $context,
        RankingRules $rankingRules,
        array $data = []
    ) {
        $this->rankingRules = $rankingRules;
        parent::__construct($context, $data);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setInputName($value): mixed
    {
        return $this->setName($value);
    }

    /**
     * @param $value
     * @return RuleColumn
     */
    public function setInputId($value): RuleColumn
    {
        return $this->setId($value);
    }

    /**
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    /**
     * @return array[]
     */
    private function getSourceOptions(): array
    {
        return $this->rankingRules->toOptionArray();
    }
}
