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
class Eltrino_Region_Model_Resource_Region extends Mage_Directory_Model_Resource_Region
{
    protected $_countryTable;

    protected function _construct()
    {
        parent::_construct();
        $this->_countryTable = $this->getTable('directory/country');
    }

    /**
     * Save regions
     *
     * @var int    $countryId
     * @var array  $regions
     * @var string $locale
     *
     * @return void
     */
    public function saveRegions($countryId, array $regions, $locale)
    {
        $adapter = $this->_getWriteAdapter();
        foreach ($regions as $region) {
            if (array_key_exists('delete', $region)) {
                $condition = array('region_id = ?' => (int)$region['id']);
                $adapter->delete($this->getMainTable(), $condition);
            } elseif (array_key_exists('id', $region)) {
                $regionNameData = array('name' => $region['name']);
                $condition = array('region_id = ?' => (int)$region['id'], 'locale = ?' => $locale);
                $adapter->update($this->_regionNameTable, $regionNameData, $condition);
            } else {
                $regionData = array('country_id'   => $countryId,
                                    'code'         => $region['name'],
                                    'default_name' => $region['name']);
                $adapter->insert($this->getMainTable(), $regionData);
                $regionNameData = array('locale'    => $locale,
                                        'region_id' => $adapter->lastInsertId(),
                                        'name'      => $region['name']);
                $adapter->insert($this->_regionNameTable, $regionNameData);
            }
        }
    }

    /**
     * Get countries list with regions
     *
     * @return array
     */
    public function getCountryAvailableRegionsList()
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->distinct(true)
            ->from(array('dc' => $this->getMainTable()), array('country_id'))
            ->join(
                array('dcr' => $this->_countryTable),
                'dc.country_id = dcr.country_id',
                array()
            );
        $countriesList = $adapter->fetchAll($select);
        $countriesListInverted = array();
        foreach ($countriesList as $country) {
            $countriesListInverted[$country['country_id']] = true;
        }

        return $countriesListInverted;
    }
}