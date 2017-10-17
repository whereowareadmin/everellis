<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Block\Adminhtml\Rule\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Amasty\Payrestriction\Model\System\Config\Status;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Amasty\Payrestriction\Helper\Data;


class Restrictions extends Generic implements TabInterface
{

    /**
     * @var \Amasty\Payrestriction\Model\System\Config\Status
     */
    protected $status;


    protected $amprhelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Status $status
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Status $status,
        Data $amprhelper,
        array $data = []
    ) {
        $this->status = $status;
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
        $form->setHtmlIdPrefix('rule_');
        $fieldset = $form->addFieldset('apply_in', ['legend' => __('General')]);
        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', ['name' => 'rule_id']);
        }
        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'is_active',
            'select',
            [
                'name'      => 'is_active',
                'label'     => __('Status'),
                'title' => __('Status'),
                'values'   => $this->status->toOptionArray()
            ]
        );
        $fieldset->addField(
            'methods',
            'multiselect',
            [
                'name'      => 'methods[]',
                'label'     => __('Methods'),
                'values'   => $this->amprhelper->getAllMethods(),
                'required' => true
            ]
        );
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }


    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Restrictions');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Restrictions');
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
