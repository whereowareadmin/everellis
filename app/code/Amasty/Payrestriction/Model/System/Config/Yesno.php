<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Model\System\Config;

class Yesno extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    const YES = 1;
    const NO = 0;

    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('Yes'), 'value' => self::YES],
                ['label' => __('No'), 'value' => self::NO]
            ];
        }

        return $this->_options;
    }

}
