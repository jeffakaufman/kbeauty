<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-02-10T18:06:04+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Destination/Grid.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Destination_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('destination_id');
        $this->setId('xtento_productexport_destination_grid');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    protected function _getCollectionClass()
    {
        return 'xtento_productexport/destination_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('destination_id',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Destination ID'),
                'width' => '50px',
                'index' => 'destination_id',
                'type' => 'number'
            )
        );

        $this->addColumn('type',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Type'),
                'index' => 'type',
                'type' => 'options',
                'options' => Mage::getSingleton('xtento_productexport/system_config_source_destination_type')->toOptionArray(),
            )
        );

        $this->addColumn('name',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Name'),
                'index' => 'name'
            )
        );

        $this->addColumn('configuration',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Configuration'),
                'index' => 'destination_id',
                'filter' => false,
                'renderer' => 'xtento_productexport/adminhtml_destination_grid_renderer_configuration',
                'width' => '180px'
            )
        );

        $this->addColumn('status',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Status'),
                'index' => 'destination_id',
                'filter' => false,
                'renderer' => 'xtento_productexport/adminhtml_destination_grid_renderer_status',
            )
        );

        $this->addColumn('last_result',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Last Result'),
                'index' => 'last_result',
                'type' => 'options',
                'options' => array(
                    0 => Mage::helper('xtento_productexport')->__('Failed'),
                    1 => Mage::helper('xtento_productexport')->__('Success'),
                ),
                'renderer' => 'xtento_productexport/adminhtml_destination_grid_renderer_result',
            )
        );

        $this->addColumn('last_result_message',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Last Result Message'),
                'index' => 'last_result_message',
                'type' => 'text'
            )
        );

        $this->addColumn('last_modification',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Last Modification'),
                'index' => 'last_modification',
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
                        'caption' => Mage::helper('xtento_productexport')->__('Edit Destination'),
                        'url' => array('base' => '*/productexport_destination/edit'),
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
        $this->setMassactionIdField('destination_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('destination');
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}