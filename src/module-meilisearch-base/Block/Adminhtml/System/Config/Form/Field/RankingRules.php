<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchBase\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class RankingRules extends AbstractFieldArray
{
    /**
     * @var ?RuleColumn
     */
    protected ?RuleColumn $rule = null;

    /**
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn('rule', [
            'label' => __('Rule'),
            'renderer' => $this->getRuleOptionRender(),
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * @param DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return RuleColumn|null
     * @throws LocalizedException
     */
    protected function getRuleOptionRender(): ?RuleColumn
    {
        if (!$this->rule) {
            $this->rule = $this->getLayout()->createBlock(
                RuleColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->rule;
    }
}
