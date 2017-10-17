<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */

namespace Amasty\Payrestriction\Controller\Adminhtml\Rule;

class Save extends \Amasty\Payrestriction\Controller\Adminhtml\Rule
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('rule_id');
        $model = $this->_objectManager->create('Amasty\Payrestriction\Model\Rule');
        $data = $this->getRequest()->getPostValue();
        if ($data) {

            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
            }
            unset($data['rule']);
            $model->setData($data);  // common fields
            $model->loadPost($data); // rules

            $model->setId($id);
            $session = $this->_objectManager->get('Magento\Backend\Model\Session');
            try {
                $this->prepareForSave($model);

                $model->save();

                $session->setPageData(false);

                $this->messageManager->addSuccess(__('Payment Restriction has been successfully saved'));

                if ($this->getRequest()->getParam('back')){
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                }
                else {
                    $this->_redirect('*/*');
                }
            }
            catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $session->setPageData($model->getData());
                $this->_redirect('*/*/edit', array('id' => $id));
            }
            return;
        }

        $this->messageManager->addError(__('Unable to find a record to save'));
        $this->_redirect('*/*');
    }

    public function prepareForSave($model)
    {
        $fields = array('stores', 'cust_groups', 'methods', 'days');
        foreach ($fields as $f){
            // convert data from array to string
            $val = $model->getData($f);
            $model->setData($f, '');
            if (is_array($val)){
                // need commas to simplify sql query
                $model->setData($f, ',' . implode(',', $val) . ',');
            }
        }

        return true;
    }
}
