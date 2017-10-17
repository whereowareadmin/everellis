<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Block\Adminhtml\Rule\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('amasty_payrestriction_rule_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Rule Configuration'));
    }

    protected function _beforeToHtml()
    {

        $tabs = array(
            'restrictions'  => ['title' => 'Restrictions', 'block' => 'Restrictions'],
            'stores_groups' => ['title' => 'Stores & Customer Groups', 'block' => 'StoresGroups'],
            'daystime' => ['title' => 'Days & Time', 'block' => 'DayTime'],
            'conditions' => ['title' => 'Conditions', 'block' => 'Conditions']
        );

        foreach ($tabs as $code => $data){

            $this->addTab(
                $code,
                [
                    'label' => __($data['title']),
                    'title' => __($data['title']),
                    'content' => $this->getLayout()->createBlock(
                        'Amasty\Payrestriction\Block\Adminhtml\Rule\Edit\Tab\\' . $data['block']
                    )->toHtml()
                ]
            );

        }

        return parent::_beforeToHtml();
    }
}
