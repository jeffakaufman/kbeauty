<?php

class Aitoc_Aitreports_Block_Export_Log extends Mage_Adminhtml_Block_Sales_Order
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'aitreports';
        $this->_controller = 'export_log';
        $currentExport     = Mage::registry('current_export');

        $this->_addButton('back', array(
            'label'   => $this->getBackButtonLabel(),
            'onclick' => 'setLocation(\''.$this->getBackUrl().'\')',
            'class'   => 'back', 
            ));

        $this->_addButton('delete', array(
            'label'   => $this->helper('aitreports')->__('Delete'), 
            'onclick' => 'if (!confirm(\''.Mage::helper('aitreports')->__('Are you sure you want to do this?').'\')) {return false;} setLocation(\''.$this->getUrl('*/*/delete', array('id' => $currentExport->getId())).'\')',
            'class'   => 'delete', 
            ));

        $this->_addButton('download', array(
            'label'   => $this->helper('aitreports')->__('Download'),
            'onclick' => 'setLocation(\''.$this->getUrl('*/*/download', array('id' => $currentExport->getId())).'\')',
            'class'   => 'save', 
            ));

        $this->_removeButton('add');
    }

    public function getHeaderText()
    {
        $currentExport = Mage::registry('current_export');

        return Mage::helper('aitreports')->__('Exported Orders %s', $currentExport->getFilename());
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/index');
    }
}

