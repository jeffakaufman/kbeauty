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
 * Generic helper for module
 *
 * @category   Eltrino
 * @package    Eltrino_Region
 */
class Eltrino_Region_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * XML path to config settings
     */
    const XML_PATH_USE_DEFAULT = 'eltrino_region/use_default';

    /**
     * Default module behaviour.
     */
    const DEFAULT_BEHAVIOUR = 'show';

    /**
     * Module behaviour
     *
     * @var string
     */
    protected $_behaviour;

    /**
     * Behavior getter.
     *
     * @return string
     */
    public function getBehaviour()
    {
        return $this->_behaviour;
    }

    /**
     * Class constructor
     * Set behaviour
     */
    public function __construct()
    {
        $configBehaviour = Mage::getStoreConfig('eltrino_region/general_fieldsets/behaviour');
        $this->_behaviour = ($configBehaviour != null) ? $configBehaviour : self::DEFAULT_BEHAVIOUR;
    }

    /**
     * Return list of available countries
     *
     * @return array
     */
    public function getCountriesList()
    {
        $countryCollection = Mage::getResourceModel('directory/country_collection');
        $countriesWithRegions = Mage::getResourceModel('eltrino_region/entity')->getCountriesWithRegions();
        $allowCountries = explode(',', (string)Mage::getStoreConfig('general/country/allow'));
        $allowCountries = array_intersect($allowCountries, $countriesWithRegions);
        if (!empty($allowCountries)) {
            $countryCollection->addFieldToFilter("country_id", array('in' => $allowCountries));
        }
        $optionArray = $countryCollection->toOptionArray(false);
        return $optionArray;
    }

    /**
     * Return list of common settings for given country.
     * For example for US - States Only (not all regions included).
     *
     * @param string $countryId
     *
     * @return array
     */
    public function getCommonSettingsList($countryId)
    {
        $optionArray = array();
        $commonSettingsNode = Mage::app()->getConfig()
            ->getNode('global/common_settings/' . $countryId);
        if ($commonSettingsNode) {
            $commonSettingsArr = $commonSettingsNode->asArray();
            foreach ($commonSettingsArr as $itemValue) {
                if (!isset($itemValue['label']) || !isset($itemValue['regions_code'])) {
                    continue;
                }
                $regionIds = array();
                $regionsCollection = Mage::getResourceModel('directory/region_collection')
                    ->addCountryFilter($countryId)
                    ->addRegionCodeFilter(array_keys($itemValue['regions_code']));
                foreach ($regionsCollection as $region) {
                    $regionIds[] = $region->getId();
                }
                $optionArray[] = array(
                    'value' => implode(',', $regionIds),
                    'label' => $itemValue['label']
                );
            }
        }
        return $optionArray;

    }

    /**
     * Return list of regions, if available, of given given country
     *
     * @param string $countryId
     *
     * @return array
     */
    public function getRegionsList($countryId)
    {
        $optionArray = array();
        $regionsCollection = Mage::getResourceModel('directory/region_collection')->addCountryFilter($countryId);
        foreach ($regionsCollection as $item) {
            $optionArray[] = array(
                'value' => $item->getData('region_id'),
                'label' => $item->getData('name') ? $item->getData('name') : $item->getData('default_name')
            );
        }
        return $optionArray;
    }

    /**
     * Retrieves from core_config_data value for use_default
     *
     * @param $scope
     * @param $scopeId
     *
     * @return bool
     */
    public function getConfigDataUseDefault($scope, $scopeId)
    {
        /* @var $configData Mage_Core_Model_Resource_Config_Data_Collection */
        $configData = Mage::getResourceModel('core/config_data_collection');
        $configData->addFieldToFilter('path', self::XML_PATH_USE_DEFAULT);
        $configData->addFieldToFilter('scope', $scope);
        $configData->addFieldToFilter('scope_id', $scopeId);
        $item = $configData->fetchItem();
        if ($item) {
            return (bool)$item->getValue();
        }
        return true; // true is default value
    }

    /**
     * Checks use_default settings and return actual storeId
     *
     * @param $storeId
     *
     * @return int
     */
    public function getScope($storeId)
    {
        if ($storeId == Mage_Core_Model_App::ADMIN_STORE_ID) {
            return $this->_getDefaultScope();
        }

        /* @var $helper Eltrino_Region_Helper_Data */
        $helper = Mage::helper('eltrino_region');

        if (!$helper->getConfigDataUseDefault('stores', $storeId)) {
            return array(
                'scope'    => 'stores',
                'scope_id' => $storeId
            );
        }
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        if (!$helper->getConfigDataUseDefault('websites', $websiteId)) {
            return array(
                'scope'    => 'websites',
                'scope_id' => $websiteId
            );
        }

        return $this->_getDefaultScope();
    }

    /**
     * Returns default scope
     *
     * @return array
     */
    protected function _getDefaultScope()
    {
        return array(
            'scope'    => 'default',
            'scope_id' => Mage_Core_Model_App::ADMIN_STORE_ID
        );
    }

    /**
     * Retrieves store & website from request, gets actual scope based on config use_default settings
     *
     * @return array
     */
    public function getActualScopeFromRequest()
    {
        /* @var $helper Eltrino_Region_Helper_Data */
        $helper = Mage::helper('eltrino_region');
        $request = Mage::app()->getRequest();
        if ($store = $request->getParam('store')) {
            $storeId = Mage::app()->getStore($store)->getId();
            if (!$helper->getConfigDataUseDefault('stores', $storeId)) {
                return array(
                    'scope_id' => $storeId,
                    'scope'    => 'stores'
                );
            }
        }
        if ($website = $request->getParam('website')) {
            $websiteId = Mage::app()->getWebsite($website)->getId();
            if (!$helper->getConfigDataUseDefault('websites', $websiteId)) {
                return array(
                    'scope_id' => $websiteId,
                    'scope'    => 'websites'
                );
            }
        }
        return array(
            'scope_id' => 0,
            'scope'    => 'default'
        );
    }
}
