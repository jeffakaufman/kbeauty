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
 * Helper to obtain directory information
 *
 * @category   Eltrino
 * @package    Eltrino_Region
 */
class Eltrino_Region_Helper_Directory extends Mage_Directory_Helper_Data
{
    public function getRegionJson()
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $templateType = null;
        if (isset($backtrace[0]['file'])) {
            $templateName = $backtrace[0]['file'];
            if (strpos($templateName, 'billing') !== false) {
                $templateType = 'billing';
            } elseif (strpos($templateName, 'shipping') !== false) {
                $templateType = 'shipping';
            }
        }

        Varien_Profiler::start('TEST: ' . __METHOD__);
        $cacheKey = 'DIRECTORY_REGIONS_JSON_STORE' . Mage::app()->getStore()->getId();
        if (Mage::app()->useCache('config')) {
            $json = Mage::app()->loadCache($cacheKey);
        }
        if (empty($json)) {
            $countryIds = array();
            foreach ($this->getCountryCollection() as $country) {
                $countryIds[] = $country->getCountryId();
            }

            $storeId = Mage::app()->getStore()->getId();
            /* @var $helper Eltrino_Region_Helper_Data */
            $helper = Mage::helper('eltrino_region');
            $scopeData = $helper->getScope($storeId);

            $collection = Mage::getModel('directory/region')->getResourceCollection()
                ->addCountryFilter($countryIds);

            $disabledStepCollection = Mage::getResourceModel('eltrino_region/step_collection')
                ->addFieldToFilter('type_id', array('in' => array('both', $templateType)))
                ->addFieldToFilter('scope', $scopeData['scope'])
                ->addFieldToFilter('scope_id', $scopeData['scope_id']);

            $applyToFieldsets = array();
            foreach ($disabledStepCollection as $item) {
                $applyToFieldsets[] = $item->getFieldsetId();
            }

            if (!empty($applyToFieldsets)) {
                $disabledRegionsCollection = Mage::getResourceModel('eltrino_region/entity_collection')
                    ->addFieldToFilter('scope', $scopeData['scope'])
                    ->addFieldToFilter('scope_id', $scopeData['scope_id'])
                    ->addFieldToFilter('fieldset_id', array('in' => $applyToFieldsets));
                $disabledRegionsCollection->getSelect()
                    ->distinct(true)
                    ->group('region_id');
                $disabledRegions = array();
                foreach ($disabledRegionsCollection as $item) {
                    $disabledRegions[] = $item->getRegionId();
                }

                if (!empty($disabledRegions)) {
                    $collection->addFieldToFilter(
                        $this->getRegionTableAlias() . ".region_id", array('nin' => $disabledRegions)
                    );
                }
            }

            $regions = array();
            foreach ($collection as $region) {
                if (!$region->getRegionId()) {
                    continue;
                }
                $regions[$region->getCountryId()][$region->getRegionId()] = array(
                    'code' => $region->getCode(),
                    'name' => $this->__($region->getName())
                );
            }
            $json = Mage::helper('core')->jsonEncode($regions);

            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache($json, $cacheKey, array('config'));
            }
        }

        Varien_Profiler::stop('TEST: ' . __METHOD__);
        return $json;
    }

    /**
     * Retrieve table alias for region table depending
     * on Magento version. If Magento version lower
     * then 1.6 result will 'region' and in other case 'main_table'.
     *
     * @return string
     */
    public function getRegionTableAlias()
    {
        if (version_compare('1.6', Mage::getVersion()) == 1) {
            return 'region';
        }
        return 'main_table';
    }
}
