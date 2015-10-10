<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-03-18T12:47:56+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Profile/Edit/Tab/Destination.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Profile_Edit_Tab_Destination extends Xtento_ProductExport_Block_Adminhtml_Destination_Grid
{
    protected function getFormMessages()
    {
        $formMessages = array();
        $formMessages[] = array('type' => 'notice', 'message' => Mage::helper('xtento_productexport')->__('Export destinations are local directories, FTP/SFTP/HTTP servers, E-Mail recipients or webservices where exported files are saved. If you just want to export manually or from the "Catalog" grids, no export destination must be set up. Please click <a href="'.Mage::helper('adminhtml')->getUrl('*/productexport_destination').'" target="_blank">here</a> to add new export destinations.'));
        return $formMessages;
    }

    protected function _getProfile()
    {
        return Mage::getModel('xtento_productexport/profile')->load($this->getRequest()->getParam('id'));
    }

    protected function _prepareColumns()
    {
        $this->addColumn('col_destinations', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'col_destinations',
            'values' => $this->getSelectedDestinations(),
            'align' => 'center',
            'index' => 'destination_id'
        ));

        parent::_prepareColumns();
        unset($this->_columns['action']);
        foreach ($this->_columns as $key => $column) {
            if ($key == 'destination_id' || $key == 'col_destinations') {
                continue;
            }
            // Rename column IDs so they're not posted to the profile information
            $column->setId('col_'.$column->getId());
            $this->_columns['col_'.$key] = $column;
            unset($this->_columns[$key]);
        }

        $this->addColumn('action',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('xtento_productexport')->__('Edit Destination'),
                        'url' => array('base' => '*/productexport_destination/edit'),
                        'field' => 'id',
                        'target' => '_blank'
                    )
                ),
                'filter' => false,
                'sortable' => false,
            )
        );
    }

    protected function _prepareMassaction()
    {
    }

    public function getRowUrl($row)
    {
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/destinationGrid', array('_current' => true));
    }

    public function getSelectedDestinations()
    {
        $array = explode("&", $this->_getProfile()->getDestinationIds());
        return $array;
    }

    protected function _toHtml()
    {
        if ($this->getRequest()->getParam('ajax')) {
            return parent::_toHtml();
        }
        return $this->_getFormMessages() . parent::_toHtml();
    }

    protected function _getFormMessages()
    {
        $html = '<div id="messages"><ul class="messages">';
        foreach ($this->getFormMessages() as $formMessage) {
            $html .= '<li class="' . $formMessage['type'] . '-msg"><ul><li><span>' . $formMessage['message'] . '</span></li></ul></li>';
        }
        $html .= '</ul></div>';
        return $html;
    }
}