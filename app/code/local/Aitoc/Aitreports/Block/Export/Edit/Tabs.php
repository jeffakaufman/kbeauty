<?php

class Aitoc_Aitreports_Block_Export_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('export_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('aitreports')->__('Smart Reports'));
    }
 
    protected function _beforeToHtml()
    {
        $this->addTab('configuration', array(
            'label'   => Mage::helper('aitreports')->__('Configuration'),
            'title'   => Mage::helper('aitreports')->__('Configuration'),
            'content' => $this->getLayout()->createBlock('aitreports/export_edit_tab_configuration')->toHtml(), 
            ));

        $this->addTab('entities', array(
            'label'   => Mage::helper('aitreports')->__('Developer options (Entities)'),
            'title'   => Mage::helper('aitreports')->__('Developer options (Entities)'),
            'content' => $this->getLayout()->createBlock('aitreports/export_edit_tab_entities')->toHtml(), 
            ));

        $this->addTab('order_fields', array(
            'label'   => Mage::helper('aitreports')->__('Developer options (Fields)'),
            'title'   => Mage::helper('aitreports')->__('Developer options (Fields)'),
            'content' => $this->getLayout()->createBlock('aitreports/export_edit_tab_orderfields')->toHtml(), 
            ));

        $this->addTab('history', array(
            'label'   => Mage::helper('aitreports')->__('History'),
            'title'   => Mage::helper('aitreports')->__('History'),
            'content' => $this->getLayout()->createBlock('aitreports/export_edit_tab_history')->toHtml(), 
            ));
        
        $this->addTab('processor', array(
                'label'   => Mage::helper('aitreports')->__('Processor'),
                'title'   => Mage::helper('aitreports')->__('Processor'),
                'content' => $this->getLayout()->createBlock('aitreports/processor')->toHtml(),
                'class'   => 'aitreports_processor_tab',
            ));

        if(Mage::getSingleton('aitreports/processor_config')->haveActiveProcess())
        {
            $this->setActiveTab('processor');
        }            
            
        return parent::_beforeToHtml();
    }    
}
