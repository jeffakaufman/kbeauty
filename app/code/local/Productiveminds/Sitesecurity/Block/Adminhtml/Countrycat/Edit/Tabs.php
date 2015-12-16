<?php

class Productiveminds_Sitesecurity_Block_Adminhtml_Countrycat_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sitesecurity_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('sitesecurity')->__('Sitesecurity Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label'     => Mage::helper('sitesecurity')->__('Standard Information'),
            'title'     => Mage::helper('sitesecurity')->__('Standard Information'),
            'content'   => $this->getLayout()->createBlock('sitesecurity/adminhtml_countrycat_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}