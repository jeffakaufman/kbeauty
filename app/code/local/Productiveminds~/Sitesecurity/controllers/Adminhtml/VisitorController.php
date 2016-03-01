<?php

class Productiveminds_Sitesecurity_Adminhtml_VisitorController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('productiveminds');
		$this->_title('Site Visitors - Site Security');
		return $this;
	}
	
	protected function _isAllowed() {
		return true;
	}

    public function indexAction()
    {
        $this->_title($this->__('Visitors'))->_title($this->__('Visitors'));
        if($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }
     	$this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('sitesecurity/adminhtml_customer_visitor', 'Site Security'));
        $this->_addBreadcrumb(Mage::helper('sitesecurity')->__('Visitors'), Mage::helper('sitesecurity')->__('Visitors'));
        $this->_addBreadcrumb(Mage::helper('sitesecurity')->__('Visitors'), Mage::helper('sitesecurity')->__('Visitors'));
        $this->renderLayout();
    }

    public function massDeleteAction() {
    	$sitesecurityIds = $this->getRequest()->getParam('sitesecuritys');
    	if (!is_array($sitesecurityIds)) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select shome items(s)'));
    	} else {
    		try {
    			foreach ($sitesecurityIds as $sitesecurityId) {
    				$model = Mage::getModel('sitesecurity/visitor');
    				$model->setId($sitesecurityId);
    				$model->delete();
    			}
    			Mage::getSingleton('adminhtml/session')->addSuccess(
    					Mage::helper('adminhtml')->__( '%d items(s) were successfully deleted', count($sitesecurityIds) )
    			);
    		} catch (Exception $e) {
    			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    		}
    	}
    	$this->_redirect('*/*/');
    }
}
