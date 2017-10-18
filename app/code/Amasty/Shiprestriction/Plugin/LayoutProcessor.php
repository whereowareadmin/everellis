<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprestriction
 */


namespace Amasty\Shiprestriction\Plugin;

class LayoutProcessor
{
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $result
    ) {
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['price']['template'] = 'Amasty_Shiprestriction/tax-price';
        return $result;
    }
}
