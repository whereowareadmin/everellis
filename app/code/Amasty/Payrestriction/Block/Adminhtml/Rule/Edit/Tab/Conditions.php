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
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;


class Conditions extends Generic implements TabInterface
{

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $_conditions;


    protected $_rendererFieldset;

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
        Fieldset $rendererFieldset,
        \Magento\Rule\Block\Conditions $conditions,
        array $data = []
    ) {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_conditions = $conditions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form before rendering HTML
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_amasty_payrestriction_rule');

        $form = $this->_formFactory->create();

        $renderer = $this->_rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl('amasty_payrestriction/rule/newConditionHtml/form/rule_conditions_fieldset')
        );

        $fldTree = $form->addFieldset(
            'rule_conditions_fieldset',
            ['legend'=> __('Apply the rule only if the following conditions are met (leave blank for all products)')]
        )->setRenderer(
            $renderer
        );

        $fldTree->addField(
            'conditions',
            'text',
            ['name' => 'conditions', 'label' => __('Conditions'), 'title' => __('Conditions'), 'required' => true]
        )->setRule(
            $model
        )->setRenderer(
            $this->_conditions
        );

        $fldAdvanced = $form->addFieldset('advanced', array('legend'=> __('Backorders')));

        $fldAdvanced->addField('out_of_stock', 'select', array(
            'label'     => __('Apply the rule to'),
            'name'      => 'out_of_stock',
            'options'   => [
                __('All orders'),
                __('Backorders only'),
                __('Non backorders')
            ],
        ));

        $this->setForm($form);
        $form->setValues($model->getData());
        return parent::_prepareForm();
    }


    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Conditions');
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
