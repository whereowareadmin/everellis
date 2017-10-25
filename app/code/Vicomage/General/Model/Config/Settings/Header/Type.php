<?php
namespace Vicomage\General\Model\Config\Settings\Header;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Type 1')],
            ['value' => '2', 'label' => __('Type 2')],
            ['value' => '3', 'label' => __('Type 3')],
            ['value' => '4', 'label' => __('Type 4')],
            ['value' => '5', 'label' => __('Type 5')],
            ['value' => '6', 'label' => __('Type 6')],
            ['value' => '7', 'label' => __('Type 7')],
            ['value' => '8', 'label' => __('Type 8')]
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            '1' => __('Type 1'),
            '2' => __('Type 2'),
            '3' => __('Type 3'),
            '4' => __('Type 4'),
            '5' => __('Type 5'),
            '6' => __('Type 6'),
            '7' => __('Type 7'),
            '8' => __('Type 8')
        ];
    }
}
