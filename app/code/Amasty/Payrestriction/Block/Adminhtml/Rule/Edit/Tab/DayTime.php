<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Block\Adminhtml\Rule\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Amasty\Payrestriction\Helper\Data;


class DayTime extends Generic implements TabInterface
{

    protected $amprhelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Data $amprhelper,
        array $data = []
    ) {
        $this->amprhelper = $amprhelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form before rendering HTML
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_amasty_payrestriction_rule');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fldInfo = $form->addFieldset('daystime', array('legend'=> __('Days and Time')));

        $fldInfo->addField('days', 'multiselect', array(
            'label'     => __('Days of the week'),
            'name'      => 'days[]',
            'values'    => $this->amprhelper->getAllDays(),
            'note'      => __('Leave empty or select all to apply the rule every day'),
        ));

        $fldInfo->addField('time_from', 'select', array(
            'label'     => __('Time From:'),
            'name'      => 'time_from',
            'values'   => $this->amprhelper->getAllTimes(),
        ));

        $fldInfo->addField('time_to', 'select', array(
            'label'     => __('Time To:'),
            'name'      => 'time_to',
            'values'   => $this->amprhelper->getAllTimes(),
        ));

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }


    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Days & Time');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Days & Time');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
