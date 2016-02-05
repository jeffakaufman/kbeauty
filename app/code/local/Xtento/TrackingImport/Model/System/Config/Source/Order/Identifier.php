<?php

/**
 * Product:       Xtento_TrackingImport (2.1.2)
 * ID:            9Hiec+sXo9Z8XvyLsrsgPILEHN0W5+Sn/0xZtemTYL0=
 * Packaged:      2015-11-13T21:34:24+00:00
 * Last Modified: 2014-06-24T22:08:35+02:00
 * File:          app/code/local/Xtento/TrackingImport/Model/System/Config/Source/Order/Identifier.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_TrackingImport_Model_System_Config_Source_Order_Identifier
{

    public function toOptionArray()
    {
        $identifiers[] = array('value' => 'order_increment_id', 'label' => Mage::helper('xtento_trackingimport')->__('Order Increment ID'));
        $identifiers[] = array('value' => 'order_entity_id', 'label' => Mage::helper('xtento_trackingimport')->__('Order Entity ID (entity_id, internal Magento ID)'));
        $identifiers[] = array('value' => 'invoice_increment_id', 'label' => Mage::helper('xtento_trackingimport')->__('Invoice Increment ID'));
        $identifiers[] = array('value' => 'shipment_increment_id', 'label' => Mage::helper('xtento_trackingimport')->__('Shipment Increment ID'));
        $identifiers[] = array('value' => 'creditmemo_increment_id', 'label' => Mage::helper('xtento_trackingimport')->__('Credit Memo Increment ID'));
        return $identifiers;
    }

}
