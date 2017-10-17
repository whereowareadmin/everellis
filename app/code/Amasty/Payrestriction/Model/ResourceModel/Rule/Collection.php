<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Model\ResourceModel\Rule;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_coreDate;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        $this->_coreDate = $coreDate;
        $this->_localeDate = $localeDate;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, null, null);
    }


    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\Payrestriction\Model\Rule', 'Amasty\Payrestriction\Model\ResourceModel\Rule');
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    public function addAddressFilter($address)
    {
        $this->addFieldToFilter('is_active', 1);

        $storeId = $address->getQuote()->getStoreId();
        $storeId = intval($storeId);
        $this->getSelect()->where('stores="" OR stores LIKE "%,'.$storeId.',%"');

        $groupId = 0;
        if ($address->getQuote()->getCustomerId()){
            $groupId = $address->getQuote()->getCustomer()->getGroupId();
        }
        $groupId = intval($groupId);
        $this->getSelect()->where('cust_groups="" OR cust_groups LIKE "%,'.$groupId.',%"');
        $this->getSelect()->where('days="" OR days LIKE "%,' . $this->_coreDate->date('N') . ',%"');

        $locDate = $this->_localeDate->date();

        $timeStamp = $locDate->format('H') * 100 + $locDate->format('i') + 1;

        $this->getSelect()->where('time_from="" OR time_from="0" OR time_to="" OR time_to="0" OR
        (time_from < '.$timeStamp.' AND time_to > '.$timeStamp.') OR
        (time_from < '.$timeStamp. ' AND time_to < time_from) OR
        (time_to > '.$timeStamp. ' AND time_to < time_from)');
        return $this;
    }
}
