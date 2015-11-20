<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2014-11-10T23:41:49+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Destination.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Model_Destination extends Mage_Core_Model_Abstract
{
    /*
     * Destination model containing information about "destinations" where exported files can be saved on
     */

    /*
     * Destination Types
     */
    const TYPE_LOCAL = 'local';
    const TYPE_FTP = 'ftp';
    const TYPE_SFTP = 'sftp';
    const TYPE_HTTP = 'http';
    const TYPE_EMAIL = 'email';
    const TYPE_WEBSERVICE = 'webservice';
    const TYPE_CUSTOM = 'custom';

    protected function _construct()
    {
        parent::_construct();
        $this->_init('xtento_productexport/destination');
    }

    /*
     * Return destination types
     */
    public function getTypes()
    {
        $values = array();
        $values[self::TYPE_LOCAL] = Mage::helper('xtento_productexport')->__('Local Directory');
        $values[self::TYPE_FTP] = Mage::helper('xtento_productexport')->__('FTP Server');
        $values[self::TYPE_SFTP] = Mage::helper('xtento_productexport')->__('SFTP Server');
        $values[self::TYPE_HTTP] = Mage::helper('xtento_productexport')->__('HTTP Server');
        $values[self::TYPE_EMAIL] = Mage::helper('xtento_productexport')->__('E-Mail Recipient(s)');
        $values[self::TYPE_WEBSERVICE] = Mage::helper('xtento_productexport')->__('Webservice/API');
        $values[self::TYPE_CUSTOM] = Mage::helper('xtento_productexport')->__('Custom Class');
        return $values;
    }

    /*
     * Set last result message for this destination
     */
    public function setLastResultMessage($message)
    {
        $this->setData('last_result_message', date('c', Mage::getModel('core/date')->timestamp(time())) . ": " . $message);
        return $this;
    }

    /*
     * Save files on destination
     */
    public function saveFiles($generatedFiles)
    {
        $destinationClass = Mage::getModel('xtento_productexport/destination_' . $this->getData('type'), array('destination' => $this));
        if ($destinationClass !== false) {
            return $destinationClass->saveFiles($generatedFiles);
        }
    }

    /*
     * Retrieve profiles which are using this destination.
     */
    public function getProfileUsage()
    {
        $profileUsage = array();
        $profileCollection = Mage::getModel('xtento_productexport/profile')->getCollection();
        $profileCollection->addFieldToFilter('destination_ids', array('neq' => ''));
        $profileCollection->getSelect()->order('entity ASC');
        foreach ($profileCollection as $profile) {
            $destinationIds = explode("&", $profile->getData('destination_ids'));
            if (in_array($this->getId(), $destinationIds)) {
                $profileUsage[] = $profile;
            }
        }
        return $profileUsage;
    }

    /*
     * Overwrite ID when importing destinations. Thanks to ST for the idea.
     */
    public function saveWithId()
    {
        // First check if the ID we've set exists as a model right now.
        $realId = $this->getId();
        $idExists = Mage::getModel($this->_resourceName)->setId(null)->load($realId)->getId();

        // Perform the regular saving routine as if it's a new model
        if (!$idExists) {
            $this->setId(null);
        }
        $this->save();

        // Update the new model we created with the original ID.
        if (!$idExists) {
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $write->update(
                $this->_getResource()->getMainTable(),
                array($this->_getResource()->getIdFieldName() => $realId),
                array("`{$this->_getResource()->getIdFieldName()}` = {$this->getId()}")
            );
            #$write->commit();
        }

        return $this;
    }

    /*
     * Fix bad user input for specific configuration values when requested by the module
     */
    public function getHostname() {
        $hostname = $this->getData('hostname');
        $hostname = str_replace(array('ftp://', 'http://'), '', $hostname);
        $hostname = trim($hostname);
        return $hostname;
    }

    public function getPort() {
        $port = $this->getData('port');
        $port = preg_replace('/[^0-9]/', '', $port);
        return $port;
    }

    public function getTimeout() {
        $timeout = $this->getData('timeout');
        $timeout = preg_replace('/[^0-9]/', '', $timeout);
        return $timeout;
    }
}