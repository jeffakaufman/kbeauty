<?php
 
class Productiveminds_Sitesecurity_Adminhtml_CountryController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('productiveminds');
		$this->_title('Countries - Site Security');
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
    
    protected function _getStore() {
    	$storeId = (int) $this->getRequest()->getParam('store', 0);
    	return Mage::app()->getStore($storeId);
    }
    
    
    public function newAction()
    {
        $model = Mage::getModel('sitesecurity/country');
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
        	$model->setData($data);
        }
        Mage::register('sitesecurity_data', $model);
     	$this->loadLayout();
     	$this->_initAction();
  		$this->_title('Adding new Item');
  		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$this->_addContent($this->getLayout()->createBlock('sitesecurity/adminhtml_country_edit'))
    		->_addLeft($this->getLayout()->createBlock('sitesecurity/adminhtml_country_edit_tabs'));
    	$this->renderLayout();
    }
 
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('sitesecurity/country');
        $model->load((int) $id);
        if ($model->getId()) {
        	$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        	if (!empty($data)) {
        		$model->setData($data);
        	}
            Mage::register('sitesecurity_data', $model);
            $this->loadLayout();
            $this->_initAction();
            $this->_title($model->getId() ? $model->getCountry() : $this->__('New Entry'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('sitesecurity/adminhtml_country_edit'))
            ->_addLeft($this->getLayout()->createBlock('sitesecurity/adminhtml_country_edit_tabs'));
            $this->renderLayout();
    	} else {
          	Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sitesecurity')->__('Item does not exist'));
          	$this->_redirect('*/*/');
      	}
    }
 
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost())
        {
            $model = Mage::getModel('sitesecurity/country');
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
           // $model->setCatId();
            //$model->setData($data);
 
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $model->setId($id);
                }
                // set the store_id to current scope, the _afterXX method will use ths accordingly.
                $store = $this->_getStore();
                $model->setStoreId($store->getId());
                $model->setCatId($this->getRequest()->getParam('cat_id'));
                $model->setStatus($this->getRequest()->getParam('status'));
                $model->save();
 
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('sitesecurity')->__('Error saving item'));
                }
 
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sitesecurity')->__('Item was successfully saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
 
                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back') || $this->getRequest()->getParam('duplicated')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                } else {
                    $this->_redirect('*/*/');
                    return;
                }
 
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
 
            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sitesecurity')->__('No data found to save'));
        $this->_redirect('*/*/');
    }
    
   
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('sitesecurity/country');
                $model->setId($id);
                
                // set the store_id to current scope, the _afterXX method will use ths accordingly.
                $store = $this->_getStore();
                $model->setStoreId($store->getId());
                $model->delete();
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sitesecurity')->__('The item has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the item to delete.'));
        $this->_redirect('*/*/');
    }
    
    public function massStatusAction()
    {
    	$sitesecurityIds = $this->getRequest()->getParam('sitesecuritys');
    	if (!is_array($sitesecurityIds)) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select items(s)'));
    	} else {
    		try {
    			foreach ($sitesecurityIds as $sitesecurityId) {
    				$model = Mage::getModel('sitesecurity/country')
    				->load($sitesecurityId)
    				->setStatus($this->getRequest()->getParam('status'))
    				->setIsMassupdate(true);
    				
    				$model->save();
    			}
    			Mage::getSingleton('adminhtml/session')->addSuccess(
    					Mage::helper('adminhtml')->__(
    							'%d items(s) were successfully deleted', count($sitesecurityIds)
    					)
    			);
    		} catch (Exception $e) {
    			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    		}
    	}
    	$this->_redirect('*/*/index');
    }
    
    public function massGroupsAction()
    {
    	$sitesecurityIds = $this->getRequest()->getParam('sitesecuritys');
    	if (!is_array($sitesecurityIds)) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select items(s)'));
    	} else {
    		try {
    			foreach ($sitesecurityIds as $sitesecurityId) {
    				$model = Mage::getModel('sitesecurity/country')
    				->load($sitesecurityId)
    				->setCatId($this->getRequest()->getParam('cat_id'))
    				->setIsMassupdate(true);
    
    				$model->save();
    			}
    			Mage::getSingleton('adminhtml/session')->addSuccess(
    					Mage::helper('adminhtml')->__(
    							'%d items(s) were successfully deleted', count($sitesecurityIds)
    					)
    			);
    		} catch (Exception $e) {
    			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    		}
    	}
    	$this->_redirect('*/*/index');
    }
    
    public function massDeleteAction() {
    	$sitesecurityIds = $this->getRequest()->getParam('sitesecuritys');
    	if (!is_array($sitesecurityIds)) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select items(s)'));
    	} else {
    		try {
    			foreach ($sitesecurityIds as $sitesecurityId) {
	                $model = Mage::getModel('sitesecurity/country');
	                $model->setId($sitesecurityId);
	                
	                // set the store_id to current scope, the _afterXX method will use ths accordingly.
	                $store = $this->_getStore();
	                $model->setStoreId($store->getId());
	                $model->delete();
    			}
    			Mage::getSingleton('adminhtml/session')->addSuccess(
    					Mage::helper('adminhtml')->__(
    							'%d items(s) were successfully deleted', count($sitesecurityIds)
    					)
    			);
    		} catch (Exception $e) {
    			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    		}
    	}
    	$this->_redirect('*/*/');
    }
 
}