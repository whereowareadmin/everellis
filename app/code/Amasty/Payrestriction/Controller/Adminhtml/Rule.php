<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Items controller
 */
abstract class Rule extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $_resultLayoutFactory = null;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
    }

    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Amasty_Payrestriction::sales_restiction');
        $resultPage->addBreadcrumb(__('Payment Restrictions'), __('Payment Restrictions'));
        $resultPage->getConfig()->getTitle()->prepend(__('Payment Restrictions'));

        return $resultPage;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Payrestriction::sales_restiction');
    }
}
