<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-02-10T18:57:17+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/History/Grid.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_History_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function getFormMessages()
    {
        $formMessages = array();
        $formMessages[] = array('type' => 'notice', 'message' => Mage::helper('xtento_productexport')->__("Exported objects get logged here. You can see when an object was exported. Look up the execution log entry to see why. You can also delete objects here and have them re-exported if \"Export only new objects\" is set to \"Yes\"."));
        return $formMessages;
    }

    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('history_id');
        $this->setId('xtento_productexport_history_grid');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
    }

    protected function _getProfile()
    {
        return Mage::registry('product_export_profile') ? Mage::registry('product_export_profile') : Mage::getModel('xtento_productexport/profile')->load($this->getRequest()->getParam('id'));
    }

    protected function _prepareCollection()
    {
        if ($this->getCollection()) {
            return parent::_prepareCollection();
        }
        $collection = Mage::getResourceModel('xtento_productexport/history_collection');
        $collection->getSelect()->joinLeft(array('profile' => $collection->getTable('xtento_productexport/profile')), 'main_table.profile_id = profile.profile_id', array('concat(profile.name," (ID: ", profile.profile_id,")") as profile', 'profile.entity', 'profile.name'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $this->addColumn('history_id',
            array(
                'header' => Mage::helper('xtento_productexport')->__('History ID'),
                'width' => '50px',
                'index' => 'history_id',
                'type' => 'number'
            )
        );

        $this->addColumn('log_id',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Log / Export ID'),
                'width' => '50px',
                'index' => 'log_id',
                'type' => 'number'
            )
        );

        $this->addColumn('profile',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Profile'),
                'index' => 'profile',
                'filter_index' => 'name'
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

        $this->addColumn('entity_id',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Entity ID'),
                'width' => '50px',
                'index' => 'entity_id',
                'type' => 'number',
                'filter_index' => 'main_table.entity_id'
            )
        );

        $this->addColumn('exported_at',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Exported At'),
                'index' => 'exported_at',
                'type' => 'datetime'
            )
        );

        $this->addColumn('view_log_entry',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Log Entry'),
                'type' => 'action',
                'getter' => 'getLogId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('xtento_productexport')->__('View Execution Log Entry'),
                        'url' => array('base' => '*/productexport_log/'),
                        'field' => 'log_id',
                        'target' => '_blank'
                    ),
                ),
                'filter' => false,
                'sortable' => false,
            )
        );

        $this->addColumn('delete',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('xtento_productexport')->__('Delete Entry'),
                        'url' => array('base' => '*/productexport_history/delete'),
                        'field' => 'id'
                    ),
                ),
                'filter' => false,
                'sortable' => false,
            )
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('history_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('history');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('adminhtml')->__('Delete Entries'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('adminhtml')->__('Are you sure? These objects will eventually get re-exported.')
        ));
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