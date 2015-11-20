<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2013-06-26T22:55:32+02:00
 * File:          app/code/local/Xtento/StockImport/Block/Adminhtml/Profile.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Block_Adminhtml_Profile extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'xtento_stockimport';
        $this->_controller = 'adminhtml_profile';
        $this->_headerText = Mage::helper('xtento_stockimport')->__('Stock Import - Profiles');
        $this->_addButtonLabel = Mage::helper('xtento_stockimport')->__('Add New Profile');
        parent::__construct();
    }

    protected function _toHtml()
    {
        return $this->getLayout()->createBlock('xtento_stockimport/adminhtml_widget_menu')->toHtml() . parent::_toHtml();
    }
}