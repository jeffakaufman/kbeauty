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
 * Customer model
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Eltrino_Region_Model_Customer extends Mage_Customer_Model_Customer
{
    /**
     * Customer addresses collection
     *
     * @return Mage_Customer_Model_Entity_Address_Collection
     */
    public function getAddressesCollection()
    {
        if ($this->_addressesCollection === null) {
            $this->_addressesCollection = $this->getAddressCollection()
                ->setCustomerFilter($this)
                ->addAttributeToSelect('*');

            $storeId = Mage::app()->getStore()->getId();
            /* @var $helper Eltrino_Region_Helper_Data */
            $helper = Mage::helper('eltrino_region');
            $scopeData = $helper->getScope($storeId);
            if ($helper->getBehaviour() == 'hide') {
                $disabledRegions = Mage::getResourceModel('eltrino_region/entity_collection')
                    ->addFieldToFilter('scope', $scopeData['scope'])
                    ->addFieldToFilter('scope_id', $scopeData['scope_id']);
                $excludeRegions = array();
                foreach ($disabledRegions as $region) {
                    $excludeRegions[] = $region->getRegionId();
                }
                if (count($excludeRegions) > 0) {
                    $this->_addressesCollection->addAttributeToFilter(
                        'region_id', array('nin' => $excludeRegions)
                    );
                }
            }

            foreach ($this->_addressesCollection as $address) {
                $address->setCustomer($this);
            }
        }

        return $this->_addressesCollection;
    }
}