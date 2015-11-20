<?php

/**
 * Product:       Xtento_ProductExport (1.7.0)
 * ID:            fCw98dfDR6EH4ugjSph2lInidzBeO0hRoSkwlirUWoA=
 * Packaged:      2015-06-20T16:59:02+00:00
 * Last Modified: 2013-02-10T18:06:04+01:00
 * File:          app/code/local/Xtento/ProductExport/Block/Adminhtml/Profile/Grid.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_ProductExport_Block_Adminhtml_Profile_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('profile_id');
        $this->setId('xtento_productexport_profile_grid');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('xtento_productexport/profile_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('profile_id',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Profile ID'),
                'width' => '50px',
                'index' => 'profile_id',
                'type' => 'number'
            )
        );

        $this->addColumn('entity',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Export Type'),
                'index' => 'entity',
                'type' => 'options',
                'options' => Mage::getSingleton('xtento_productexport/system_config_source_export_entity')->toOptionArray()
            )
        );

        $this->addColumn('name',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Name'),
                'index' => 'name'
            )
        );

        $this->addColumn('enabled',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Profile Status'),
                'index' => 'enabled',
                'type' => 'options',
                'options' => array(
                    0 => Mage::helper('xtento_productexport')->__('Enabled'),
                    1 => Mage::helper('xtento_productexport')->__('Disabled'),
                ),
                'width' => '100px',
                'renderer' => 'xtento_productexport/adminhtml_profile_grid_renderer_status',
            )
        );

        $this->addColumn('configuration',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Configuration'),
                'index' => 'profile_id',
                'filter' => false,
                'renderer' => 'xtento_productexport/adminhtml_profile_grid_renderer_configuration',
                'width' => '160px'
            )
        );

        $this->addColumn('last_execution',
            array(
                'header' => Mage::helper('xtento_productexport')->__('Last Export'),
                'index' => 'last_execution',
                'type' => 'datetime'
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
                        'caption' => Mage::helper('xtento_productexport')->__('Edit Profile'),
                        'url' => array('base' => '*/*/edit'),
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
        $this->setMassactionIdField('profile_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('profile');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('adminhtml')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('adminhtml')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('update_status', array(
            'label' => Mage::helper('adminhtml')->__('Update Status'),
            'url' => $this->getUrl('*/*/massUpdateStatus'),
            'additional' => array(
                'enabled' => array(
                    'name' => 'enabled',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('adminhtml')->__('Enabled'),
                    'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
                )
            )
        ));
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