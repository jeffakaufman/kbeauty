<?php
 
class Productiveminds_Core_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
	
	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('productiveminds');
		$this->_title('Productiveminds Core');
		return $this;
	}
	
	protected function _isAllowed() {
		return true;
	}
 	
    public function indexAction() {
        $this->loadLayout();
        $this->_initAction();
        $this->renderLayout();
    }
 
}