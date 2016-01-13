<?php
/**
 * Singleton
 */
class Aitoc_Aitreports_Model_Citiesdma extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        parent::_construct();
        $this->_init('aitreports/citiesdma');
    }

    public function processCsvFile() {
        /**
         * For now this functional will only be used on local server, to insert data and apply it to table via sql, because it contain a lot of single requests and might not be executed on some servers.
         * Last file used - AdWords_API_Cities-DMA_Regions_2013-09-27.csv
         */
        return false;
        $this->_removeOldData();
        $name = 'AdWords_API_Cities-DMA_Regions_2013-09-27.csv';
        $file = Mage::getRoot().DS.'code'.DS.'local'.DS.'Aitoc'.DS.'Aitreports'.DS.'Model'.DS.'System'.DS.'Config'.DS.$name;
        if(!file_exists($file)) {
            return false;
        }
        $f = fopen($file, 'r');
        $row = fgetcsv($f);
        $count = 0;
        $resource = Mage::getSingleton('core/resource')->getConnection('core_write');
        $table = $this->getResource()->getMainTable();
        while($row = fgetcsv($f)) {

            $insert = array(
                'id' => null,
                'city' => $row[0],
                'criteria' => $row[1],
                'region_name' => $row[2],
                'region_code' => $row[3]
            );
            $count++;
            $resource->insert($table, $insert);
        }
    }

    protected function _removeOldData()
    {
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');

        $table = $this->getResource()->getMainTable();#$resource->getTableName('aitpagecache/target_page');
        if ($connection->delete($table, '1'))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}
