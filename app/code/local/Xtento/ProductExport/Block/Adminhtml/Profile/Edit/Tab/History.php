<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-02-10T18:57:17+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Profile/Edit/Tab/History.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Profile_Edit_Tab_History extends Xtento_ProductExport_Block_Adminhtml_History_Grid
{
    protected function _getProfile()
    {
        return Mage::registry('product_export_profile') ? Mage::registry('product_export_profile') : Mage::getModel('xtento_productexport/profile')->load($this->getRequest()->getParam('id'));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('xtento_productexport/history_collection');
        $collection->addFieldToFilter('main_table.profile_id', $this->_getProfile()->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        unset($this->_columns['profile']);
        foreach ($this->_columns as $key => $column) {
            if ($key == 'history_id') {
                continue;
            }
            // Rename column IDs so they're not posted to the profile information
            $column->setId('col_' . $column->getId());
            $this->_columns['col_' . $key] = $column;
            unset($this->_columns[$key]);
        }
    }

    protected function _prepareMassaction()
    {
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/historyGrid', array('_current' => true));
    }
}