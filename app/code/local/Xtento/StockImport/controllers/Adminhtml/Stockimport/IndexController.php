<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2013-11-23T16:13:03+01:00
 * File:          app/code/local/Xtento/StockImport/controllers/Adminhtml/Stockimport/IndexController.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Adminhtml_StockImport_IndexController extends Xtento_StockImport_Controller_Abstract
{
    public function redirectAction()
    {
        $redirectController = Mage::getStoreConfig('stockimport/general/default_page');
        if (!$redirectController) {
            $redirectController = 'stockimport_manual';
        }
        $this->_redirect('*/'.$redirectController);
    }

    public function installationAction() {
        Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('xtento_stockimport')->__('The extension has not been installed properly. The required database tables have not been created yet. Please check out our <a href="http://support.xtento.com/wiki/Troubleshooting:_Database_tables_have_not_been_initialized" target="_blank">wiki</a> for instructions. After following these instructions access the module at Catalog > Stock Import again.'));
        $this->loadLayout();
        $this->renderLayout();
    }

    public function disabledAction() {
        Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('xtento_stockimport')->__('The extension is currently disabled. Please go to System > XTENTO Extensions > Stock Import Configuration to enable it. After that access the module at Catalog > Stock Import again.'));
        $this->loadLayout();
        $this->renderLayout();
    }
}