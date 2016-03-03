<?php
class Productiveminds_Core_Adminhtml_SupportController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() {
		$this->loadLayout()
		->_setActiveMenu('productiveminds')
		->_title($this->__('Productiveminds Support'));
		
		$this->_addBreadcrumb(Mage::helper('productivemindscore')->__('Productiveminds Support'),
				Mage::helper('productivemindscore')->__('Productiveminds Support'));
		
		return $this;
	}
	
	protected function _isAllowed() {
		return true;
	}
 
	
    public function indexAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('productiveminds')
             ->_title($this->__('Productiveminds Support'));

        $this->_addBreadcrumb(Mage::helper('productivemindscore')->__('Productiveminds Support'),
                              Mage::helper('productivemindscore')->__('Productiveminds Support'));
        $this->renderLayout();
    }
    
    public function newAction()
    {
    	$this->loadLayout();
    	$this->_initAction();
    	$this->_title('Create new Ticket');
    	$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
    	$this->_addContent($this->getLayout()->createBlock('productivemindscore/adminhtml_support_edit'))
    	->_addLeft($this->getLayout()->createBlock('productivemindscore/adminhtml_support_edit_tabs'));
    	$this->renderLayout();
    }

    public  function saveAction()
    {   
    	$subject = 'Customer ticket for: '.$this->getRequest()->getParam('typeofissue', false);
        $message = $this->getRequest()->getParam('message', false);
        $systeminfo = $this->getRequest()->getParam('systeminfo', false);
        $username = $this->getRequest()->getParam('username', false);
        $useremail = $this->getRequest()->getParam('useremail', false);
        $emailBody = "<p>{$subject}</p> <p>{$message}</p> <p>{$systeminfo}</p> <br/> <p>Productiveminds Support</p><br/><br/>";
                
        Mage::getModel('productivemindscore/email')->sendMail($subject, $useremail, $username, $emailBody, $_FILES['attachments']);
        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
    }
}
