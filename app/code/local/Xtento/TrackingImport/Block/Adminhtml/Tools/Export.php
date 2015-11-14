<?php

/**
 * Product:       Xtento_TrackingImport (2.1.2)
 * ID:            9Hiec+sXo9Z8XvyLsrsgPILEHN0W5+Sn/0xZtemTYL0=
 * Packaged:      2015-11-13T21:34:24+00:00
 * Last Modified: 2013-11-03T16:33:42+01:00
 * File:          app/code/local/Xtento/TrackingImport/Block/Adminhtml/Tools/Export.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_TrackingImport_Block_Adminhtml_Tools_Export extends Mage_Adminhtml_Block_Template
{
    public function getProfiles()
    {
        $profileCollection = Mage::getModel('xtento_trackingimport/profile')->getCollection();
        $profileCollection->getSelect()->order('name ASC');
        return $profileCollection;
    }

    public function getSources()
    {
        $sourceCollection = Mage::getModel('xtento_trackingimport/source')->getCollection();
        $sourceCollection->getSelect()->order('name ASC');
        return $sourceCollection;
    }
}