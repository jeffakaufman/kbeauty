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
 * Renderer for region field
 *
 * @category   Eltrino
 * @package    Eltrino_Region
 */
class Eltrino_Region_Model_Adminhtml_Customer_Renderer_Region extends Mage_Adminhtml_Model_Customer_Renderer_Region
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if ($country = $element->getForm()->getElement('country_id')) {
            $countryId = $country->getValue();
        }

        if (!isset($countryId) || isset(self::$_regionCollections[$countryId])) {
            return parent::render($element);
        }

        $disabledRegions = array();
        /* @var $disabledRegionsCollection Eltrino_Region_Model_Resource_Entity_Collection */
        $disabledRegionsCollection = Mage::getResourceModel('eltrino_region/entity_collection');
        $customer = Mage::registry('current_customer');

        if ($customer) {
            $storeId = $customer->getStoreId();
            if ($storeId) {
                /* @var $helper Eltrino_Region_Helper_Data */
                $helper = Mage::helper('eltrino_region');
                $scopeData = $helper->getScope($storeId);
                $disabledRegionsCollection
                    ->addFieldToFilter('scope', $scopeData['scope'])
                    ->addFieldToFilter('scope_id', $scopeData['scope_id']);
            } else {
                $disabledRegionsCollection
                    ->addFieldToFilter('scope', 'default')
                    ->addFieldToFilter('scope_id', 0);
            }

        }
        foreach ($disabledRegionsCollection as $item) {
            $disabledRegions[] = $item->getRegionId();
        }

        $directoryHelper = Mage::helper('eltrino_region/directory');
        $collection = Mage::getResourceModel('directory/region_collection')
            ->addCountryFilter($countryId);
        if (!empty($disabledRegions)) {
            $collection->addFieldToFilter(
                $directoryHelper->getRegionTableAlias() . ".region_id", array('nin' => $disabledRegions)
            );
        }
        self::$_regionCollections[$countryId] = $collection->toOptionArray();

        return parent::render($element);
    }
}
