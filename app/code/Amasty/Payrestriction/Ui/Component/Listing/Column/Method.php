<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Ui\Component\Listing\Column;

class Method implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $options;
    protected $amHelper;

    public function __construct(
        \Amasty\Payrestriction\Helper\Data $amHelper
    )
    {
        $this->amHelper = $amHelper;
    }

    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = $this->amHelper->getAllMethods();
        }

        return $this->options;
    }
}
