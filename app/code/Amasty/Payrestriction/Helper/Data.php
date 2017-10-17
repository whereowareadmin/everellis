<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const STORAGE_KEY = 'ampayrestriction_products';

    /**
     * @var \Magento\Framework\App\Config\Initial
     */
    protected $_initialConfig;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\Initial $initialConfig,
        \Magento\Framework\Registry $registry
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context);
        $this->_initialConfig = $initialConfig;
    }

    public function getAllMethods()
    {
        $hash = array();
        foreach ($this->getPaymentMethods() as $code=>$config){
            if (!empty($config['title'])){
                $label = '';
                if (!empty($config['group'])){
                    $label = ucfirst($config['group']) . ' - ';
                }
                $label .= $config['title'];
                if (!empty($config['allowspecific']) && !empty($config['specificcountry'])){
                    $label .= ' in ' . $config['specificcountry'];
                }
                $hash[$code] = $label;

            }
        }
        asort($hash);

        $methods = array();
        foreach ($hash as $code => $label){
            $methods[] = array('value' => $code, 'label' => $label);
        }

        return $methods;
    }

    public function getPaymentMethods()
    {
        return $this->_initialConfig->getData('default')['payment'];
    }

    public function getAllDays()
    {
        return array(
            array('value'=>'7', 'label' => __('Sunday')),
            array('value'=>'1', 'label' => __('Monday')),
            array('value'=>'2', 'label' => __('Tuesday')),
            array('value'=>'3', 'label' => __('Wednesday')),
            array('value'=>'4', 'label' => __('Thursday')),
            array('value'=>'5', 'label' => __('Friday')),
            array('value'=>'6', 'label' => __('Saturday')),
        );
    }

    public function getAllTimes()
    {
        $timeArray = array();
        $timeArray[0] = 'Please select...';

        for($i = 0 ; $i < 24 ; $i++){
            for($j = 0; $j < 60 ; $j=$j+15){
                $timeStamp = $i.':'.$j;
                $timeFormat = date ('H:i',strtotime($timeStamp));
                $timeArray[$i * 100 + $j + 1] = $timeFormat;
            }
        }
        return $timeArray;
    }

    public function getProducts()
    {
        $names = $this->coreRegistry->registry(self::STORAGE_KEY);
        if (empty($names)) {
            $names = array();
        }

        return $names;
    }

    public function addProduct($name)
    {
        $oldNames = $this->getProducts();
        if (!in_array($name, $oldNames)) {
            $oldNames[] = $name;
        }
        $this->_saveProducts($oldNames);

        return $this;
    }

    protected function _saveProducts($names)
    {
        $this->clearProducts();
        $this->coreRegistry->register(self::STORAGE_KEY, $names);

        return $this;
    }

    public function clearProducts()
    {
        $this->coreRegistry->unregister(self::STORAGE_KEY);
    }

}
