<?php

declare(strict_types=1);

namespace Walkwizus\MeilisearchMerchandising\Block\Adminhtml\Category\Query;

use Magento\Backend\Block\Template;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class Builder extends Template
{
    /**
     * @var AttributeCollectionFactory
     */
    protected AttributeCollectionFactory $attributeCollectionFactory;

    /**
     * @var AttributeRepositoryInterface
     */
    protected AttributeRepositoryInterface $attributeRepository;

    /**
     * @param Template\Context $context
     * @param AttributeCollectionFactory $attributeCollectionFactory
     * @param AttributeRepositoryInterface $attributeRepository
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        Template\Context $context,
        AttributeCollectionFactory $attributeCollectionFactory,
        AttributeRepositoryInterface $attributeRepository,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->attributeRepository = $attributeRepository;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    /**
     * @return string
     */
    public function getSaveRuleUrl(): string
    {
        return $this->getUrl('meilisearch_merchandising/category/ajax_saverule');
    }

    /**
     * @return string
     */
    public function getPreviewUrl(): string
    {
        return $this->getUrl('meilisearch_merchandising/category/ajax_preview');
    }

    /**
     * @return false|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFilters(): bool|string
    {
        $attributes = $this->getAttributes();
        $rules = $this->transformAttributesToRules($attributes);

        return json_encode($rules);
    }

    /**
     * @param $attributes
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function transformAttributesToRules($attributes): array
    {
        $rules = [];

        foreach ($attributes as $attribute) {
            $rule = [
                'id' => $attribute['code'],
                'operator' => 'equal',
                'value' => '',
            ];

            switch ($attribute['type']) {
                case 'text':
                    $rule['type'] = 'string';
                    break;
                case 'select':
                case 'multiselect':
                    $rule['type'] = 'integer';
                    $rule['input'] = 'select';
                    $rule['values'] = $this->getSelectValues($attribute['code']);
                    break;
                case 'boolean':
                    $rule['type'] = 'integer';
                    $rule['input'] = 'radio';
                    $rule['values'] = [1 => 'Yes', 0 => 'No'];
                    break;
            }

            $rules[] = $rule;
        }

        return $rules;
    }

    /**
     * @param $attributeCode
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getSelectValues($attributeCode): array
    {
        $values = [];
        $attribute = $this->attributeRepository->get('catalog_product', $attributeCode);
        $options = $attribute->getSource()->getAllOptions();

        foreach ($options as $option) {
            if (isset($option['value'])) {
                $values[$option['value']] = $option['label'];
            }
        }

        return $values;
    }

    /**
     * @return array
     */
    protected function getAttributes(): array
    {
        $attributes = $this->attributeCollectionFactory
            ->create()
            ->addFieldToFilter('frontend_input', ['in' => ['text', 'select', 'multiselect', 'boolean']]);

        $data = [];
        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $data[] = [
                'code' => $attribute->getAttributeCode(),
                'type' => $attribute->getFrontendInput()
            ];
        }

        return $data;
    }
}
