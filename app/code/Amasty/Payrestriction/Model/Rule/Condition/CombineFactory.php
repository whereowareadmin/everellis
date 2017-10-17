<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */

namespace Amasty\Payrestriction\Model\Rule\Condition;

class CombineFactory extends \Magento\SalesRule\Model\Rule\Condition\CombineFactory
{
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Amasty\\Payrestriction\\Model\\Rule\\Condition\\Combine')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }
}
