<?php
 
namespace Vicomage\Testimonial\Controller\Index;

class View extends \Magento\Framework\App\Action\Action
{
    
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
