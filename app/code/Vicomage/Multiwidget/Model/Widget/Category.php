<?php
namespace Vicomage\Multiwidget\Model\Widget;


/**
 * Class Rule
 */
class Category implements \Magento\Framework\Option\ArrayInterface
{

    const PREFIX_ROOT = '-';
    const REPEATER = '-';
    const PREFIX_END = '';

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Catalog\Model\Config\Source\Category
     */
    protected $_category;

    protected $_request;

    protected $_storeManager;

    protected  $_options = array();

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Config\Source\Category $category
    )
    {
        $this->_request = $request;
        $this->_storeManager = $storeManager;
        $this->_categoryFactory = $categoryFactory;
        $this->_category = $category;
    }

    public function toOptionArray()
    {
        if(!$this->_options){
            $store = $this->_request->getParam('store');
            if(!$store) $categories = $this->_category->toOptionArray();
            else {
                $rootCategoryId = $this->_storeManager->getStore($store)->getRootCategoryId();
                $label = $this->_categoryFactory->create()->load($rootCategoryId)->getName();
                $categories = array(array('value' => $rootCategoryId, 'label' => $label));
            }
            $options = array();
            foreach ($categories as $category) {
                $this->_options = array();
                if($category['value']) {
                    $this->_options[] = array('label' => __('All'), 'value' => '0');
                    $_categories = $this->_categoryFactory->create()->getCategories($category['value']);
                    if($_categories){
                        foreach ($_categories as $_category) {
                            $this->_options[] = array(
                                'label' => self::PREFIX_ROOT .$_category->getName(),
                                'value' => $_category->getEntityId()
                            );
                            if ($_category->hasChildren()) $this->_getChildOptions($_category->getChildren());
                        }
                        if($this->_options){
                            $options[] = array(
                                'label' => $category['label'],
                                'value' => $this->_options
                            );
                        }
                    }
                }
            }
            $this->_options = $options;
        }
        return $this->_options;
    }

    protected function _getChildOptions($categories)
    {
        foreach ($categories as $category) {
            $prefix = str_repeat(self::REPEATER, $category->getLevel() * 1) . self::PREFIX_END;
            $this->_options[] = array(
                'label' => $prefix . $category->getName(),
                'value' => $category->getEntityId()
            );
            if ($category->hasChildren()) $this->_getChildOptions($category->getChildren());
        }
    }

}
