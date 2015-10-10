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
 * Event observers container
 *
 * @category   Eltrino
 * @package    Eltrino_Region
 */
class Eltrino_Region_Model_Observer
{
    /**
     * Save regions configuration for countries
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Eltrino_Region_Model_Observer
     */
    public function storeCountryDisabledRegions(Varien_Event_Observer $observer)
    {
        $scope = $this->_getStoreId($observer);
        if ($scope['scope'] != 'default' && $this->_saveUseDefault($scope['scope'], $scope['scope_id'])) {
            return $this;
        }

        $regions = Mage::app()->getRequest()->getPost('eltrino_region', array('disabled_regions' => array()));
        $steps = Mage::app()->getRequest()->getPost('steps', array());
        $regions = $regions['disabled_regions'];

        Mage::getModel('eltrino_region/entity')->storeCountriesDisabledRegions(
            $regions, $scope['scope'], $scope['scope_id']
        );
        Mage::getResourceModel('eltrino_region/step')->storeSteps(
            $steps, $scope['scope'], $scope['scope_id']
        );

        $this->clearCache();
        return $this;
    }

    protected function _getStoreId(Varien_Event_Observer $observer)
    {
        if ($observer->getStore()) {
            return array(
                'scope'    => 'stores',
                'scope_id' => Mage::app()->getStore($observer->getStore())->getId()
            );
        }
        if ($observer->getWebsite()) {
            return array(
                'scope'    => 'websites',
                'scope_id' => Mage::app()->getWebsite($observer->getWebsite())->getId()
            );
        }
        return array(
            'scope'    => 'default',
            'scope_id' => Mage_Core_Model_App::ADMIN_STORE_ID
        );
    }

    protected function _saveUseDefault($scope, $scopeId)
    {
        $useDefault = Mage::app()->getRequest()->getPost('region_use_default');
        if (is_null($useDefault)) {
            $useDefault = 0;
        }

        $configData = Mage::getModel('core/config_data');
        $configData->setScope($scope);
        $configData->setScopeId($scopeId);
        $configData->setPath(Eltrino_Region_Helper_Data::XML_PATH_USE_DEFAULT);
        $configData->setValue($useDefault);
        $configData->save();
        Mage::getConfig()->reinit();

        if ($useDefault) {
            Mage::getModel('eltrino_region/entity')->getResource()->deleteRegions($scope, $scopeId);
        }
        return $useDefault;
    }

    const CACHE_PREFIX = 'DIRECTORY_REGIONS_JSON_STORE';

    protected function clearCache()
    {
        Mage::app()->useCache('config');
        foreach (Mage::app()->getStores() as $store) {
            $cacheId = self::CACHE_PREFIX . $store->getId();
            Mage::app()->removeCache($cacheId);
        }
    }
}
