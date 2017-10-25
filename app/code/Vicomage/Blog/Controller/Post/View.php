<?php
/**
 * Copyright © 2015 Ihor Vansach (ihor@Vicomage.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Vicomage\Blog\Controller\Post;

/**
 * Blog post view
 */
class View extends \Magento\Framework\App\Action\Action
{

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
    }

    /**
     * View Blog post action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $post = $this->_initPost();
        if (!$post) {
            $this->_forward('index', 'noroute', 'cms');
            return;
        }

        $this->_objectManager->get('\Magento\Framework\Registry')->register('current_blog_post', $post);

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Init Post
     *
     * @return \Vicomage\Blog\Model\Post || false
     */
    protected function _initPost()
    {
        $id = $this->getRequest()->getParam('id');
        $storeId = $this->_storeManager->getStore()->getId();

        $post = $this->_objectManager->create('Vicomage\Blog\Model\Post')->load($id);

        if (!$post->isVisibleOnStore($storeId)) {
            return false;
        }

        $post->setStoreId($storeId);

        return $post;
    }

}
