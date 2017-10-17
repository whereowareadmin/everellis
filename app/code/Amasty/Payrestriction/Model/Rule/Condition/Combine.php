<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */

namespace Amasty\Payrestriction\Model\Rule\Condition;

class Combine extends \Magento\SalesRule\Model\Rule\Condition\Combine
{
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Amasty\Payrestriction\Model\Rule\Condition\Address $conditionAddress,
        array $data = []
    ) {

        parent::__construct($context, $eventManager, $conditionAddress, $data);
        $this->_conditionAddress = $conditionAddress;

        $this->setType('Amasty\Payrestriction\Model\Rule\Condition\Combine');
    }

    public function getNewChildSelectOptions()
    {
        $addressAttributes = $this->_conditionAddress->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($addressAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Amasty\Payrestriction\Model\Rule\Condition\Address|' . $code,
                'label' => $label,
            ];
        }

        $conditions = [['value' => '', 'label' => __('Please choose a condition to add.')]];
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => 'Amasty\Payrestriction\Model\Rule\Condition\Product\Subselect',
                    'label' => __('Products subselection')
                ],
                [
                    'value' => $this->getType(),
                    'label' => __('Conditions combination')
                ],
                ['label' => __('Cart Attribute'), 'value' => $attributes]
            ]
        );

        $additional = new \Magento\Framework\DataObject();
        $this->_eventManager->dispatch('salesrule_rule_condition_combine', ['additional' => $additional]);
        $additionalConditions = $additional->getConditions();
        if ($additionalConditions) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }

}