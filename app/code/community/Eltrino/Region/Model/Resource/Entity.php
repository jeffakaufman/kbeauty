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
 * Resource model for configuration entity
 *
 * @category   Eltrino
 * @package    Eltrino_Region
 */
class Eltrino_Region_Model_Resource_Entity extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('eltrino_region/entity', 'entity_id');
    }

    /**
     * Store disabled regions configuration
     *
     * @param array $regions
     * @param       $scope
     * @param       $scopeId
     *
     * @return $this
     */
    public function storeCountriesDisabledRegions(array $regions = array(), $scope, $scopeId)
    {
        $data = array();
        foreach ($regions as $index => $region) {
            $countryId = key($region);
            foreach ($region[$countryId] as $regionId) {
                $data[] = array(
                    'country_id'  => $countryId,
                    'region_id'   => $regionId,
                    'scope_id'    => $scopeId,
                    'scope'       => $scope,
                    'fieldset_id' => $index);
            }
        }

        // before store $regions all previous data should be removed
        $this->deleteRegions($scope, $scopeId);

        if (!empty($data)) {
            $this->_getWriteAdapter()->insertMultiple($this->getMainTable(), $data);
        }

        return $this;
    }

    /**
     * Fetch rows of disabled regions configuration
     *
     * @param $scope
     * @param $scopeId
     *
     * @return array
     */
    public function fetchCountriesDisabledRegions($scope, $scopeId)
    {
        $data = array();

        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('*'))
            ->where('scope = ?', $scope)
            ->where('scope_id = ?', $scopeId);
        $rows = $this->_getReadAdapter()->fetchAll($select);

        foreach ($rows as $row) {
            $data[$row['fieldset_id']][$row['country_id']][] = $row['region_id'];
        }

        return $data;
    }

    /**
     * Used for deleting regions
     *
     * @param $scope
     * @param $scopeId
     */
    public function deleteRegions($scope, $scopeId)
    {
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(), array('scope = ?' => $scope, 'scope_id = ?' => $scopeId)
        );
    }

    /**
     * Used for storing common settings
     *
     * @param array $commonSettings
     *
     * @return $this
     */
    public function storeCommonSettings(array $commonSettings = array())
    {
        $data = array();
        foreach ($commonSettings as $countryId => $commonSetting) {
            $data[] = array('country_id' => $countryId, 'common_settings' => $commonSetting);
        }

        $this->_trancateTable($this->getTable('eltrino_region/common_settings'));

        if (!empty($data)) {
            $this->_getWriteAdapter()->insertMultiple($this->getTable('eltrino_region/common_settings'), $data);
        }
        return $this;
    }

    /**
     * Retrieve array of countries ids which have regions in system
     *
     * @return array
     */
    public function getCountriesWithRegions()
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('directory/country_region'), 'DISTINCT(country_id)');
        $rows = $this->_getReadAdapter()->fetchCol($select);
        return $rows;
    }

    public function getMaxFieldsetId()
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), array('fieldset_id'))
            ->order('fieldset_id ' . Varien_Data_Collection::SORT_ORDER_DESC)
            ->limit(1);
        return $adapter->fetchOne($select);
    }

    /**
     * Truncate given table
     *
     * @param string $table
     *
     * @return Eltrino_Region_Model_Resource_Entity
     */
    protected function _trancateTable($table)
    {
        $this->_getWriteAdapter()->query('TRUNCATE TABLE ' . $table);
        return $this;
    }
}
