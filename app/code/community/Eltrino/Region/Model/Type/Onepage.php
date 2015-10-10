<?php
/**
 * Remove or Change Displayed States and Regions
 *
 * LICENSE
 *
 * This source file is subject to the Eltrino LLC EULA
 * that is bundled with this package in the file LICENSE_EULA.txt.
 * It is also available through the world-wide-web at this URL:
 * http://eltrino.com/license-eula.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@eltrino.com so we can send you a copy immediately.
 *
 * @category    Eltrino
 * @package     Eltrino_Region
 * @copyright   Copyright (c) 2014 Eltrino LLC. (http://eltrino.com)
 * @license     http://eltrino.com/license-eula.txt  Eltrino LLC EULA
 */

/**
 * One page checkout processing model
 *
 * @category   Eltrino
 * @package    Eltrino_Region
 */
class Eltrino_Region_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
    /**
     * Default message to be shown
     */
    const DEF_DISABLED_MESS = 'This address is not allowed to use on this website';

    /**
     * Actual message to be shown
     *
     * @var mixed|string
     */
    protected $_disabledRegionMess;

    /**
     * Class constructor
     * Set disabled address region message
     */
    public function __construct()
    {
        parent::__construct();
        $configMessage = Mage::getStoreConfig('eltrino_region/general_fieldsets/address_region_disabled_message');
        $this->_disabledRegionMess = ($configMessage != null) ? $configMessage : self::DEF_DISABLED_MESS;
    }

    /**
     * Check if the region is allowed.
     *
     * @param int $customerAddressId
     * @return bool
     */
    protected function _checkRegion($customerAddressId)
    {
        $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);

        $storeId = Mage::app()->getStore()->getId();
        /* @var $helper Eltrino_Region_Helper_Data */
        $helper = Mage::helper('eltrino_region');
        $scopeData = $helper->getScope($storeId);

        $disabledRegions = Mage::getResourceModel('eltrino_region/entity_collection')
            ->addFieldToFilter('scope', $scopeData['scope'])
            ->addFieldToFilter('scope_id', $scopeData['scope_id'])
            ->addFieldToFilter('region_id', $customerAddress->getRegionId());

        return (count($disabledRegions) == 0);
    }

    /**
     * Save billing address information to quote
     * This method is called by One Page Checkout JS (AJAX) while saving the billing information.
     *
     * @param   array $data
     * @param   int $customerAddressId
     * @return  Mage_Checkout_Model_Type_Onepage
     */
    public function saveBilling($data, $customerAddressId)
    {
        if (!empty($customerAddressId)) {
            if ($this->_checkRegion($customerAddressId) == false) {
                return array('error' => 1,
                    'message' => Mage::helper('eltrino_region')->__($this->_disabledRegionMess)
                );
            }
        }

        return parent::saveBilling($data, $customerAddressId);
    }

    /**
     * Save checkout shipping address
     *
     * @param   array $data
     * @param   int $customerAddressId
     * @return  Mage_Checkout_Model_Type_Onepage
     */
    public function saveShipping($data, $customerAddressId)
    {
        if (!empty($customerAddressId)) {
            if ($this->_checkRegion($customerAddressId) == false) {
                return array('error' => 1,
                    'message' => Mage::helper('eltrino_region')->__($this->_disabledRegionMess)
                );
            }
        }

        return parent::saveShipping($data, $customerAddressId);
    }
}
