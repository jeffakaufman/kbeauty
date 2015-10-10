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
class Eltrino_Region_Data_Collection extends Varien_Data_Collection
{
    protected $_data = array();

    /**
     * @var array $data
     *
     * @return Eltrino_Region_Data_Collection
     */
    public function setDataArray(array $data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * @var bool $printQuery
     * @var bool $logQuery
     *
     * @return Eltrino_Region_Data_Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $this->_totalRecords = count($this->_data);
        $this->_setIsLoaded();

        $paginator = Zend_Paginator::factory($this->_data);
        $paginator->setCurrentPageNumber($this->getCurPage());
        $paginator->setItemCountPerPage($this->getPageSize());

        foreach ($paginator as $country) {
            $object = new Varien_Object();
            $object->setData($country);
            $this->addItem($object);
        }

        return $this;
    }
}