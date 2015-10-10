<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */    
class Amasty_List_AmadminlistController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        Mage::register('current_customer_id', (int) $this->getRequest()->getParam('customer_id'));
        $this->getResponse()->setBody($this->getLayout()->createBlock('amlist/adminhtml_customer_edit_tab')->toHtml());
    }
}