<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-03-17T16:34:49+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Log/Grid.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function getFormMessages()
    {
        $formMessages = array();
        $formMessages[] = array('type' => 'notice', 'message' => Mage::helper('xtento_productexport')->__('Any exports get logged here. Find failed exports or download exported files.'));
        return $formMessages;
    }

    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('log_id');
        $this->setId('xtento_productexport_log_grid');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
    }

    protected function _getCollectionClass()
    {
        return 'xtento_productexport/log_collection';
    }

    protected function _prepareCollection()
    {
        if ($this->getCollection()) {
            return parent::_prepareCollection();
        }
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->getSelect()->joinLeft(array('profile' => $collection->getTable('xtento_productexport/profile')), 'main_table.profile_id = profile.profile_id', array('concat(profile.name," (ID: ", profile.profile_id,")") as profile', 'profile.entity', 'profile.name'));
        if ($this->getRequest()->getParam('log_id', false) && !$this->getRequest()->getParam('ajax', false) == true) {
            $collection->addFieldToFilter('log_id', $this->getRequest()->getParam('log_id'));
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('log_id',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Export ID'),
                'width' => '50px',
                'index' => 'log_id',
                'type' => 'number'
            )
        );

        $this->addColumn('export_type',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Export Type'),
                'index' => 'export_type',
                'type' => 'options',
                'options' => Mage::getSingleton('xtento_productexport/system_config_source_export_type')->toOptionArray(),
                'renderer' => 'xtento_productexport/adminhtml_log_grid_renderer_type',
            )
        );

        $this->addColumn('entity',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Entity'),
                'index' => 'entity',
                'type' => 'options',
                'options' => Mage::getSingleton('xtento_productexport/system_config_source_export_entity')->toOptionArray()
            )
        );

        $this->addColumn('profile',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Profile'),
                'index' => 'profile',
                'filter_index' => 'name'
            )
        );

        $this->addColumn('files',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Exported Files'),
                'index' => 'files',
                'renderer' => 'xtento_productexport/adminhtml_log_grid_renderer_filename',
            )
        );

        $this->addColumn('destination_ids',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Destinations'),
                'index' => 'destination_ids',
                'renderer' => 'xtento_productexport/adminhtml_log_grid_renderer_destination',
                'filter' => false
            )
        );

        $this->addColumn('records_exported',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Record Count'),
                'index' => 'records_exported',
                'type' => 'number'
            )
        );

        $this->addColumn('result',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Result'),
                'index' => 'result',
                'type' => 'options',
                'options' => Mage::getSingleton('xtento_productexport/system_config_source_log_result')->toOptionArray(),
                'renderer' => 'xtento_productexport/adminhtml_log_grid_renderer_result',
            )
        );

        $this->addColumn('result_message',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Result Message'),
                'index' => 'result_message',
            )
        );

        $this->addColumn('created_at',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Executed At'),
                'index' => 'created_at',
                'type' => 'datetime'
            )
        );

        $this->addColumn('action',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('xtento_productexport')->__('Download file(s)'),
                        'url' => array('base' => '*/productexport_log/download'),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
            ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('log_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('log');

        $this->getMassactionBlock()->addItem('download', array(
            'label' => Mage::helper('xtento_productexport')->__('Download file(s)'),
            'url' => $this->getUrl('*/*/massDownload')
        ));

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('adminhtml')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('adminhtml')->__('Are you sure?')
        ));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/productexport_log/download', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
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