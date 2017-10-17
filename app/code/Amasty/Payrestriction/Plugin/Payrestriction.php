<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Plugin;

use Magento\Payment\Helper\Data;

class Payrestriction {

    protected $_allRules = null;
    protected $_ruleCollection;
    protected $_ruleModel;
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;


    public function __construct(
        \Amasty\Payrestriction\Model\ResourceModel\Rule\Collection $ruleCollection,
        \Amasty\Payrestriction\Model\Rule $ruleModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $appState,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->_ruleCollection = $ruleCollection;
        $this->_ruleModel = $ruleModel;
        $this->storeManager = $storeManager;
        $this->appState = $appState;
        $this->quoteRepository = $quoteRepository;
    }

    public function aroundGetStoreMethods(
        Data $subject,
        \Closure $proceed,
        $store = null,
        $quote = null
    ) {

        $methods = $proceed($store, $quote);

        if (!$quote){
            return $methods;
        }

        $quote->collectTotals();
        $this->quoteRepository->save($quote);

        $address = $quote->getShippingAddress();

        $items   = $quote->getAllItems();
        $address->setItemsToValidateRestrictions($items);

        $hasBackOrders = false;
        $hasNoBackOrders = false;
        foreach ($items as $item){
            if ($item->getBackorders() > 0 ){
                $hasBackOrders = true;
            } else {
                $hasNoBackOrders = true;
            }
            if ($hasBackOrders && $hasNoBackOrders) {
                break;
            }
        }

        foreach ($methods as $k => $method){
            foreach ($this->getRules($address, $items) as $rule){

                $validBackOrder = true;
                switch ($rule->getOutOfStock()) {
                    case \Amasty\Payrestriction\Model\Rule::BACKORDERS_ONLY:
                        if(($hasBackOrders && $hasNoBackOrders) || (!$hasBackOrders && $hasNoBackOrders)) {
                            $validBackOrder = false;
                        } elseif($hasBackOrders) {
                            $validBackOrder = true;
                        }
                        break;
                    case \Amasty\Payrestriction\Model\Rule::NON_BACKORDERS:
                        $validBackOrder = $hasBackOrders ? false : true;
                        if(($hasBackOrders && $hasNoBackOrders) || ($hasBackOrders && !$hasNoBackOrders)) {
                            $validBackOrder = false;
                        } elseif($hasNoBackOrders) {
                            $validBackOrder = true;
                        }
                        break;
                }


                if ($validBackOrder && $rule->restrict($method)){
                    if ($rule->validate($address)
                    ){
                        unset($methods[$k]);
                    }//if validate
                }//if restrict
            }//rules
        }//methods

        return $methods;
    }

    public function getRules($address, $items)
    {
        if (is_null($this->_allRules)){
            $this->_allRules = $this->_ruleCollection
                ->addAddressFilter($address)
            ;
            if ($this->appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
                $this->_allRules->addFieldToFilter('for_admin', 1);
            }

            $this->_allRules->load();

            foreach ($this->_allRules as $rule){

                $rule->afterLoad();
            }
        }

        return  $this->_allRules;
    }
}
