<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;

class RuleCondition implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $transport = $observer->getAdditional();
        $cond = $transport->getConditions();
        if (!is_array($cond)){
            $cond = array();
        }

        $types = array(
            'Customer' => 'Customer attributes',
        );
        foreach ($types as $typeCode => $typeLabel){
            $condition           = $this->_objectManager->create('Amasty\Payrestriction\Model\Rule\Condition\\' . $typeCode);
            $conditionAttributes = $condition->loadAttributeOptions()->getAttributeOption();

            $attributes = array();
            foreach ($conditionAttributes as $code=>$label) {
                $attributes[] = array(
                    'value' => 'Amasty\Payrestriction\Model\Rule\Condition\\'.$typeCode.'|' . $code,
                    'label' => $label,
                );
            }
            $cond[] = array(
                'value' => $attributes,
                'label' => __($typeLabel),
            );
        }

        $transport->setConditions($cond);

        return $this;
    }
}
