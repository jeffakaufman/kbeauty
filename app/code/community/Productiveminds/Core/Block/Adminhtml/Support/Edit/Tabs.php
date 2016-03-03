<?php

class Productiveminds_Core_Block_Adminhtml_Support_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct()
    {
        parent::__construct();
        $this->setId('productivemindscore_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('productivemindscore')->__('Productiveminds Support'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('ticket_section', array(
            'label'     => Mage::helper('productivemindscore')->__('Create a support ticket'),
            'title'     => Mage::helper('productivemindscore')->__('Create a support ticket'),
            'content'   => $this->getLayout()->createBlock('productivemindscore/adminhtml_support_edit_tab_form')->toHtml(),
        ));

        /* TODO
         $this->addTab('productivemindscore_section', array(
            'label'     => Mage::helper('productivemindscore')->__('CONTACT INFORMATION'),
            'title'     => Mage::helper('productivemindscore')->__('CONTACT INFORMATION'),
            'content'   => $this->getLayout()->createBlock('productivemindscore/adminhtml_support_edit_tab_Options')->toHtml(),
        ));
        */

        return parent::_beforeToHtml();
    }
}