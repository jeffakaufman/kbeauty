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
 * Model for configuration entity
 *
 * @category   Eltrino
 * @package    Eltrino_Region
 */
class Eltrino_Region_Model_Entity extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('eltrino_region/entity');
    }

    /**
     * Store countries with disabled regions configuration
     *
     * @param array $regions
     * @param       $scope
     * @param       $scopeId
     *
     * @return Eltrino_Region_Model_Entity
     */
    public function storeCountriesDisabledRegions(array $regions = array(), $scope, $scopeId)
    {
        $this->getResource()->storeCountriesDisabledRegions($regions, $scope, $scopeId);
        return $this;
    }

    /**
     * Retrieve countries with disabled regions configuration
     *
     * @param $scopeId
     * @param $scope
     *
     * @return array
     */
    public function fetchCountriesDisabledRegions($scopeId, $scope = null)
    {
        if (!is_null($scope)) {
            return $this->getResource()->fetchCountriesDisabledRegions($scope, $scopeId);
        }

        /* @var $helper Eltrino_Region_Helper_Data */
        $helper = Mage::helper('eltrino_region');
        $scopeData = $helper->getScope($scopeId);
        return $this->getResource()->fetchCountriesDisabledRegions($scopeData['scope'], $scopeData['scope_id']);
    }
}
