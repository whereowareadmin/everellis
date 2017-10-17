<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */

namespace Amasty\Payrestriction\Model;

class Rule extends \Magento\Rule\Model\AbstractModel
{
    const ALL_ORDERS = 0;
    const BACKORDERS_ONLY = 1;
    const NON_BACKORDERS = 2;

    const SALES_RULE_PRODUCT_CONDITION_NAMESPACE = 'Magento\\SalesRule\\Model\\Rule\\Condition\\Product';

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    protected $serializer;

    /**
     * @var ResourceModel\Rule
     */
    protected $ruleResource;

    /**
     * Rule constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param ResourceModel\Rule $ruleResource
     * @param \Amasty\Base\Model\Serializer $serializer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Amasty\Payrestriction\Model\ResourceModel\Rule $ruleResource,
        \Amasty\Base\Model\Serializer $serializer,
        array $data = []
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        $this->ruleResource = $ruleResource;
        parent::__construct(
            $context, $registry, $formFactory, $localeDate, null, null, $data
        );

        $this->serializer = $serializer;
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\Payrestriction\Model\ResourceModel\Rule');
    }


    public function getConditionsInstance()
    {
        return $this->objectManager->create('Amasty\Payrestriction\Model\Rule\Condition\Combine');
    }

    public function getActionsInstance()
    {
        return $this->objectManager->create('Magento\SalesRule\Model\Rule\Condition\Product\Combine');
    }


    public function restrict($method)
    {
        return (false !== strpos($this->getMethods(), ',' . $method->getCode() . ','));
    }

    public function afterSave()
    {
        //Saving attributes used in rule
        $ruleProductAttributes = array_merge(
            $this->_getUsedAttributes($this->getConditionsSerialized()),
            $this->_getUsedAttributes($this->getActionsSerialized())
        );
        if (count($ruleProductAttributes)) {
            $this->ruleResource->saveAttributes($this->getId(), $ruleProductAttributes);
        }

        return parent::afterSave();
    }

    /**
     * Return all product attributes used on serialized action or condition
     *
     * @param string $serializedString
     * @return array
     */
    protected function _getUsedAttributes($serializedString)
    {
        $result = [];
        $serializedString = $this->serializer->unserialize($serializedString);

        if (is_array($serializedString) && array_key_exists('conditions', $serializedString)) {
            $result = $this->recursiveFindAttributes($serializedString);
        }

        return $result;
    }

    /**
     * @param $loop
     * @return array
     */
    protected function recursiveFindAttributes($loop)
    {
        $arrayIterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($loop)
        );

        $result = [];
        $nextAttribute = false;

        foreach ($arrayIterator as $key => $value) {
            if ($key == 'type' && $value == self::SALES_RULE_PRODUCT_CONDITION_NAMESPACE) {
                $nextAttribute = true;
            }

            if ($key == 'attribute' && $nextAttribute) {
                $result[] = $value;
                $nextAttribute = false;
            }
        }

        return $result;
    }

    protected function _setWebsiteIds()
    {
        $websites = array();

        foreach ($this->storeManager->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $websites[$store->getId()] = $website->getId();
                }
            }
        }

        $this->setOrigData('website_ids', $websites);
    }


    public function beforeSave()
    {
        $this->_setWebsiteIds();
        return parent::beforeSave();
    }

    public function beforeDelete()
    {
        $this->_setWebsiteIds();
        return parent::beforeDelete();
    }

    public function activate()
    {
        $this->setIsActive(1);
        $this->save();
        return $this;
    }

    public function inactivate()
    {
        $this->setIsActive(0);
        $this->save();
        return $this;
    }
}
