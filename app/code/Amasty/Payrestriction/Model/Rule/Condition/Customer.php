<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */

namespace Amasty\Payrestriction\Model\Rule\Condition;

class Customer extends \Magento\Rule\Model\Condition\AbstractCondition
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    )
    {
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);

    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }



    public function loadAttributeOptions()
    {
        $customerAttributes = $this->_objectManager->get('Magento\Customer\Model\ResourceModel\Customer')
            ->loadAllAttributes()
            ->getAttributesByCode();
        $attributes = array();

        if (isset($customerAttributes['default_billing'])) {
            unset($customerAttributes['default_billing']);
        }
        if (isset($customerAttributes['default_shipping'])) {
            unset($customerAttributes['default_shipping']);
        }
        if (isset($customerAttributes['disable_auto_group_change'])) {
            unset($customerAttributes['disable_auto_group_change']);
        }

        if (isset($customerAttributes['created_in'])) {
            unset($customerAttributes['created_in']);
        }

        if (isset($customerAttributes['confirmation'])) {
            $confirmation = $customerAttributes['confirmation'];
            $confirmation->setFrontendInput('select');
            $confirmation->setSourceModel('Amasty\Payrestriction\Model\System\Config\Yesno');
        }

        foreach ($customerAttributes as $attribute) {
            if (!($attribute->getFrontendLabel()) || !($attribute->getAttributeCode())) {
                continue;
            }

            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }
        asort($attributes);
        $this->setAttributeOption($attributes);
        return $this;
    }

    public function getInputType()
    {
        $customerAttribute = $this->_objectManager->get('Magento\Customer\Model\ResourceModel\Customer')->getAttribute($this->getAttribute());

        switch ($customerAttribute->getFrontendInput()) {

            case 'boolean':
                return 'select';
            case 'text':
                return 'string';
            case 'datetime':
                return 'date';
            default :
                return $customerAttribute->getFrontendInput();
        }
    }

    public function getValueElement()
    {
        $element = parent::getValueElement();
        switch ($this->getInputType()) {
            case 'date':
                $element->setClass('hasDatepicker');
                break;
        }
        return $element;
    }

    public function getExplicitApply()
    {
        return ($this->getInputType() == 'date');
    }

    public function getValueElementType()
    {
        $customerAttribute = $this->_objectManager->get('Magento\Customer\Model\ResourceModel\Customer')->getAttribute($this->getAttribute());

        switch ($customerAttribute->getFrontendInput()) {
            case 'boolean':
                return 'select';
            default :
                return $customerAttribute->getFrontendInput();
        }
    }

    public function getValueSelectOptions()
    {
        $selectOptions = array();
        $attributeObject = $this->_objectManager->get('Magento\Customer\Model\ResourceModel\Customer')->getAttribute($this->getAttribute());

        if (is_object($attributeObject) && $attributeObject->usesSource() ) {
            if ($attributeObject->getFrontendInput() == 'multiselect') {
                $addEmptyOption = false;
            } else {
                $addEmptyOption = true;
            }
            $selectOptions = $attributeObject->getSource()->getAllOptions($addEmptyOption);
        }

        $key = 'value_select_options';

        if (!$this->hasData($key)) {
            $this->setData($key, $selectOptions);
        }

        return $this->getData($key);
    }

    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $customer = $model;
        if (!$customer instanceof \Magento\Customer\Model\Data\Customer) {
            $customer = $model->getQuote()->getCustomer();
            $attr = $this->getAttribute();

            $allAttr = $customer->__toArray();

            if ($attr != 'entity_id' && !array_key_exists($attr, $allAttr)){
                $address = $model->getQuote()->getBillingAddress();
                $allAttr[$attr] = $address->getData($attr);
            }
            if ($attr == 'confirmation') {
                if (array_key_exists($attr, $allAttr)) {
                    if ($allAttr[$attr] == null) {
                        $allAttr['confirmation'] = 1;
                    } else {
                        $allAttr['confirmation'] = 0;
                    }
                }
            }

            $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->setData($allAttr);
        }
        return parent::validate($customer);
    }

}
