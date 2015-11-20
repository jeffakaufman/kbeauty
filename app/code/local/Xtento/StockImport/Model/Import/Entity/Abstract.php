<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2013-08-09T16:05:03+02:00
 * File:          app/code/local/Xtento/StockImport/Model/Import/Entity/Abstract.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

abstract class Xtento_StockImport_Model_Import_Entity_Abstract extends Varien_Object
{
    /**
     * Resource models, read/write adapater
     */
    /** @var $_resourceModel Mage_Core_Model_Resource */
    protected $_resourceModel;
    /** @var $_readAdapter Varien_Db_Adapter_Pdo_Mysql */
    protected $_readAdapter;
    /** @var $_writeAdapter Varien_Db_Adapter_Pdo_Mysql */
    protected $_writeAdapter;

    /**
     * Database table name cache
     */
    protected $_tableNames = array();

    /*
     * Construct - set up resource model and database connection
     */
    function __construct()
    {
        $this->_resourceModel = Mage::getSingleton('core/resource');
        $this->_readAdapter = $this->_resourceModel->getConnection('core_read');
        $this->_writeAdapter = $this->_resourceModel->getConnection('core_write');
    }

    /*
     * Get database table name for entity
     */
    protected function getTableName($entity)
    {
        if (!isset($this->_tableNames[$entity])) {
            try {
                $this->_tableNames[$entity] = $this->_resourceModel->getTableName($entity);
            } catch (Exception $e) {
                return false;
            }
        }
        return $this->_tableNames[$entity];
    }
    /*
    * Return configuration value
    */
    function getConfig($key)
    {
        $configuration = $this->getProfile()->getConfiguration();
        if (isset($configuration[$key])) {
            return $configuration[$key];
        } else {
            return false;
        }
    }

    function getConfigFlag($key)
    {
        return (bool)$this->getConfig($key);
    }

    function getLogEntry() {
        return Mage::registry('stock_import_log');
    }
}