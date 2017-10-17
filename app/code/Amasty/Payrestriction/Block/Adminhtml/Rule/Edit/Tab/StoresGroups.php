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
use Magento\Customer\Api\GroupManagementInterface;


class StoresGroups extends Generic implements TabInterface
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var GroupManagementInterface
     */
    protected $_groupManagement;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_converter;

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
        GroupManagementInterface $customerGroup,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Convert\DataObject $converter,
        array $data = []
    ) {
        $this->_groupManagement = $customerGroup;
        $this->systemStore = $systemStore;
        $this->_converter = $converter;
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
        $fldStore = $form->addFieldset('apply_in', ['legend' => __('Apply In')]);

        $fldStore->addField(
            'for_admin',
            'select',
            [
                'name'      => 'for_admin',
                'label'     => __('Admin Area'),
                'values'   => [
                    '0' => __('No'),
                    '1' => __('Yes')
                ]
            ]
        );
        $fldStore->addField(
            'stores',
            'multiselect',
            [
                'name'      => 'stores[]',
                'label'     => __('Stores'),
                'values' => $this->systemStore->getStoreValuesForForm(false, false),
                'note'      => __('Leave empty or select all to apply the rule to any store'),
            ]
        );

        $fldCust = $form->addFieldset('apply_for', array('legend'=> __('Apply For')));
        $fldCust->addField(
            'cust_groups',
            'multiselect',
            [
                'name'      => 'cust_groups[]',
                'label'     => __('Customer Groups'),
                'values'    => $this->getAllOptions(),
                'note'      => __('Leave empty or select all to apply the rule to any group'),
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getAllOptions()
    {
        $groups = $this->_groupManagement->getLoggedInGroups();
        $groups[] = $this->_groupManagement->getNotLoggedInGroup();
        $options = $this->_converter->toOptionArray($groups, 'id', 'code');

        return $options;
    }


    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Stores & Customer Groups');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Stores & Customer Groups');
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
