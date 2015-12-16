<?php
 
class Productiveminds_Sitesecurity_Adminhtml_CountrycatController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('productiveminds');
		$this->_title('Categories - Site Security');
		return $this;
	}
	
	protected function _isAllowed() {
		return true;
	}
 
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initAction();
        $this->renderLayout();
    }
    
    public function newAction()
    {
        $model = Mage::getModel('sitesecurity/countrycat');
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
        	$model->setData($data);
        }
        Mage::register('sitesecurity_data', $model);
     	$this->loadLayout();
     	$this->_initAction();
  		$this->_title('Adding new Category');
  		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$this->_addContent($this->getLayout()->createBlock('sitesecurity/adminhtml_countrycat_edit'))
    		->_addLeft($this->getLayout()->createBlock('sitesecurity/adminhtml_countrycat_edit_tabs'));
    	$this->renderLayout();
    }
 
    public function editAction()
    {
        $cat_id = $this->getRequest()->getParam('cat_id', null);
        $model = Mage::getModel('sitesecurity/countrycat');
        $model->load((int) $cat_id);
        if ($model->getCatId()) {
        	$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        	if (!empty($data)) {
        		$model->setData($data);
        	}
            Mage::register('sitesecurity_data', $model);
            $this->loadLayout();
            $this->_initAction();
            $this->_title($model->getCatId() ? $model->getTitle() : $this->__('New Entry'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('sitesecurity/adminhtml_countrycat_edit'))
            ->_addLeft($this->getLayout()->createBlock('sitesecurity/adminhtml_countrycat_edit_tabs'));
            $this->renderLayout();
    	} else {
          	Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sitesecurity')->__('Category does not exist'));
          	$this->_redirect('*/*/');
      	}
    }
 
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost())
        {
            $model = Mage::getModel('sitesecurity/countrycat');
            $cat_id = $this->getRequest()->getParam('cat_id');
            if ($cat_id) {
                $model->load($cat_id);
            }
            $model->setData($data);
 
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($cat_id) {
                    $model->setCatId($cat_id);
                }
                $model->save();
 
                if (!$model->getCatId()) {
                    Mage::throwException(Mage::helper('sitesecurity')->__('Error saving Category'));
                }
 
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sitesecurity')->__('Category was successfully saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
 
                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back') || $this->getRequest()->getParam('duplicated')) {
                    $this->_redirect('*/*/edit', array('cat_id' => $model->getCatId()));
                    return;
                } else {
                    $this->_redirect('*/*/');
                    return;
                }
 
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('cat_id' => $this->getRequest()->getParam('cat_id')));
                return;
            }
 
            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sitesecurity')->__('No data found to save'));
        $this->_redirect('*/*/');
    }
 
    public function deleteAction()
    {
        if ($cat_id = $this->getRequest()->getParam('cat_id')) {
            try {
                $model = Mage::getModel('sitesecurity/countrycat');
                $model->setCatId($cat_id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sitesecurity')->__('The item has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('cat_id' => $this->getRequest()->getParam('cat_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the item to delete.'));
        $this->_redirect('*/*/');
    }
    
}