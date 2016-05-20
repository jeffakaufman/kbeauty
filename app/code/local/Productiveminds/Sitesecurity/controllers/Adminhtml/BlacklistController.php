<?php
 
class Productiveminds_Sitesecurity_Adminhtml_BlacklistController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('productiveminds');
		$this->_title('Blacklisted Ip Addresses - Site Security ');
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
    
    public function blacklistipfromorderAction() {
    	$pms_sitesecurity_ip = $this->getRequest()->getParam('ip');	
    	try {
    		$user = Mage::getSingleton('admin/session')->getData('user');
    		$isAlreadyBlacklisted = Mage::getModel('sitesecurity/blacklist')->load($pms_sitesecurity_ip, 'remote_addr');
    		if (null != $isAlreadyBlacklisted && !empty($isAlreadyBlacklisted) && $isAlreadyBlacklisted->getStatus() == 1) {
    			Mage::getSingleton('adminhtml/session')->addError(
    				Mage::helper('adminhtml')->__(Mage::getModel('sitesecurity/security')->getLong2ip($pms_sitesecurity_ip) . ' is already blacklisted')
    			);
    		} else {
    			$blacklistModel = Mage::getModel('sitesecurity/blacklist');
    			$blacklistModel->setAclCode(Productiveminds_Sitesecurity_Model_Security::ACL_CODE_BLACKLIST_IP);
    			$blacklistModel->setUserId($user->getId());
    			//$blacklistModel->setServerAddr($pms_sitesecurity_ip);
    			$blacklistModel->setRemoteAddr($pms_sitesecurity_ip);
    			$blacklistModel->setBlacklistedFrom(Productiveminds_Sitesecurity_Model_Security::BLACKLISTED_FROM_ORDER);
    			$blacklistModel->setStatus(Productiveminds_Sitesecurity_Model_System_Config_Source_Status::ALLOWED);
    			$blacklistModel->setUpdatedAt(gmdate('Y-m-d H:i:s'));
    			$blacklistModel->save();
    			
    			Mage::getSingleton('adminhtml/session')->addSuccess(
    				Mage::helper('adminhtml')->__(Mage::getModel('sitesecurity/security')->getLong2ip($pms_sitesecurity_ip) . ' is successully blacklisted')
    			);
    		}
    	} catch (Exception $e) {
    		Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    	}
    	$ordersUrl = $this->getUrl('adminhtml/sales_order/index');
    	$refererUrl = $this->_getRefererUrl();
    	if (empty($refererUrl)) {
    		$refererUrl = empty($ordersUrl) ? $this->getUrl('adminhtml') : $ordersUrl;
    	}
    	$this->getResponse()->setRedirect($refererUrl);
    	return $this;
    }
    
    public function blacklistipfromreviewAction() {
    	$sitesecurityIds = $this->getRequest()->getParam('reviews');
    	if (!is_array($sitesecurityIds)) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select shome items(s)'));
    	} else {
    		try {
    			foreach ($sitesecurityIds as $sitesecurityId) {
    				$review = Mage::getModel('review/review')->load($sitesecurityId);
    				$user = Mage::getSingleton('admin/session')->getData('user');
    				$isAlreadyBlacklisted = Mage::getModel('sitesecurity/blacklist')->load($review->getPmsSitesecurityIp(), 'remote_addr');
    				if (null != $isAlreadyBlacklisted && !empty($isAlreadyBlacklisted) && $isAlreadyBlacklisted->getStatus() == 1) {
    					// Do nothing, IP address is already blacklisted.
    				} else {
    					$blacklistModel = Mage::getModel('sitesecurity/blacklist');
    					$blacklistModel->setAclCode(Productiveminds_Sitesecurity_Model_Security::ACL_CODE_BLACKLIST_IP);
    					$blacklistModel->setUserId($user->getId());
    					//$blacklistModel->setServerAddr($review->getPmsSitesecurityIp());
    					$blacklistModel->setRemoteAddr($review->getPmsSitesecurityIp());
    					$blacklistModel->setBlacklistedFrom(Productiveminds_Sitesecurity_Model_Security::BLACKLISTED_FROM_REVIEW);
    					$blacklistModel->setStatus(Productiveminds_Sitesecurity_Model_System_Config_Source_Status::ALLOWED);
    					$blacklistModel->setUpdatedAt(gmdate('Y-m-d H:i:s'));
    					$blacklistModel->save();
    				}
    				unset($isAlreadyBlacklisted);
    			}
    			Mage::getSingleton('adminhtml/session')->addSuccess(
    					Mage::helper('adminhtml')->__('%d IP address(es) were successully blacklisted', count($sitesecurityIds))
    			);
    		} catch (Exception $e) {
    			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    		}
    	}
    	$reviewsUrl = $this->getUrl('adminhtml/catalog_product_review/index');
    	$refererUrl = $this->_getRefererUrl();
    	if (empty($refererUrl)) {
    		$refererUrl = empty($reviewsUrl) ? $this->getUrl('adminhtml') : $reviewsUrl;
    	}
    	$this->getResponse()->setRedirect($refererUrl);
    	return $this;
    }
    
    public function massblacklistattemptAction() {
    	$sitesecurityIds = $this->getRequest()->getParam('sitesecuritys');
    	if (!is_array($sitesecurityIds)) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select shome items(s)'));
    	} else {
    		try {
    			foreach ($sitesecurityIds as $sitesecurityId) {
    				$visitor = Mage::getModel('sitesecurity/sitesecure')->load($sitesecurityId);
    				$user = Mage::getSingleton('admin/session')->getData('user');
    				$isAlreadyBlacklisted = Mage::getModel('sitesecurity/blacklist')->load($visitor->getRemoteAddr(), 'remote_addr');
    				if (null != $isAlreadyBlacklisted && !empty($isAlreadyBlacklisted) && $isAlreadyBlacklisted->getStatus() == 1) {
    					// Do nothing, IP address is already blacklisted.
    				} else {
    					$blacklistModel = Mage::getModel('sitesecurity/blacklist');
    					$blacklistModel->setAclCode(Productiveminds_Sitesecurity_Model_Security::ACL_CODE_BLACKLIST_IP);
    					$blacklistModel->setUserId($user->getId());
    					$blacklistModel->setServerAddr($visitor->getServerAddr());
    					$blacklistModel->setRemoteAddr($visitor->getRemoteAddr());
    					$blacklistModel->setBlacklistedFrom(Productiveminds_Sitesecurity_Model_Security::BLACKLISTED_FROM_ATTEMPT);
    					$blacklistModel->setStatus(Productiveminds_Sitesecurity_Model_System_Config_Source_Status::ALLOWED);
    					$blacklistModel->setUpdatedAt(gmdate('Y-m-d H:i:s'));
    					$blacklistModel->save();
    				}
    				unset($isAlreadyBlacklisted);
    			}
    			Mage::getSingleton('adminhtml/session')->addSuccess(
    					Mage::helper('adminhtml')->__(
    							'%d IP address(es) were successully blacklisted', count($sitesecurityIds)
    					)
    			);
    		} catch (Exception $e) {
    			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    		}
    	}
    	$refererUrl = $this->_getRefererUrl();
    	if (empty($refererUrl)) {
    		$this->_redirect('*/*/');
    	}
    	$this->getResponse()->setRedirect($refererUrl);
    	return $this;
    }
    
    public function massblacklistipAction() {
    	$sitesecurityIds = $this->getRequest()->getParam('sitesecuritys');
    	if (!is_array($sitesecurityIds)) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select shome items(s)'));
    	} else {
    		try {
    			foreach ($sitesecurityIds as $sitesecurityId) {
    				$visitor = Mage::getModel('sitesecurity/visitor')->load($sitesecurityId);
    				$user = Mage::getSingleton('admin/session')->getData('user');
    				$isAlreadyBlacklisted = Mage::getModel('sitesecurity/blacklist')->load($visitor->getRemoteAddr(), 'remote_addr');
    				if (null != $isAlreadyBlacklisted && !empty($isAlreadyBlacklisted) && $isAlreadyBlacklisted->getStatus() == 1) {
    					// Do nothing, IP address is already blacklisted.
    				} else {
    					$blacklistModel = Mage::getModel('sitesecurity/blacklist');
    					$blacklistModel->setAclCode(Productiveminds_Sitesecurity_Model_Security::ACL_CODE_BLACKLIST_IP);
    					$blacklistModel->setUserId($user->getId());
    					$blacklistModel->setServerAddr($visitor->getServerAddr());
    					$blacklistModel->setRemoteAddr($visitor->getRemoteAddr());
    					$blacklistModel->setBlacklistedFrom(Productiveminds_Sitesecurity_Model_Security::BLACKLISTED_FROM_VISITOR);
    					$blacklistModel->setStatus(Productiveminds_Sitesecurity_Model_System_Config_Source_Status::ALLOWED);
    					$blacklistModel->setUpdatedAt(gmdate('Y-m-d H:i:s'));
    					$blacklistModel->save();
    				}
    				unset($isAlreadyBlacklisted);
    			}
    			Mage::getSingleton('adminhtml/session')->addSuccess(
    					Mage::helper('adminhtml')->__(
    							'%d IP address(es) were successully blacklisted', count($sitesecurityIds)
    					)
    			);
    		} catch (Exception $e) {
    			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    		}
    	}
    	$refererUrl = $this->_getRefererUrl();
    	if (empty($refererUrl)) {
    		$this->_redirect('*/*/');
    	}
    	$this->getResponse()->setRedirect($refererUrl);
    	return $this;
    }
    
    public function newAction()
    {
        $model = Mage::getModel('sitesecurity/blacklist');
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
        	$model->setData($data);
        }
        Mage::register('sitesecurity_data', $model);
     	$this->loadLayout();
     	$this->_initAction();
  		$this->_title('Adding new Item');
  		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$this->_addContent($this->getLayout()->createBlock('sitesecurity/adminhtml_blacklist_edit'))
    		->_addLeft($this->getLayout()->createBlock('sitesecurity/adminhtml_blacklist_edit_tabs'));
    	$this->renderLayout();
    }
 
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('sitesecurity/blacklist');
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
            $this->_addContent($this->getLayout()->createBlock('sitesecurity/adminhtml_blacklist_edit'))
            ->_addLeft($this->getLayout()->createBlock('sitesecurity/adminhtml_blacklist_edit_tabs'));
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
            $model = Mage::getModel('sitesecurity/blacklist');
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);
 
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $model->setId($id);
                }

                $isAlreadyBlacklisted = Mage::getModel('sitesecurity/blacklist')->load($model->getRemoteAddr(), 'remote_addr');
                if (null != $isAlreadyBlacklisted && !empty($isAlreadyBlacklisted) && $isAlreadyBlacklisted->getStatus() == 1) {
                	Mage::getSingleton('adminhtml/session')->addError(
                			Mage::helper('adminhtml')->__(Mage::getModel('sitesecurity/security')->getLong2ip($model->getRemoteAddr()) . ' is already blacklisted')
                	);
                } else {
                	$userId = $model->getUserId();
                	if(!$userId || empty($userId) || $userId < 1) {
                		$user = Mage::getSingleton('admin/session')->getData('user');
                		$model->setUserId($user->getId());
                		$model->setAclCode(Productiveminds_Sitesecurity_Model_Security::ACL_CODE_BLACKLIST_IP);
                		$model->setBlacklistedFrom(Productiveminds_Sitesecurity_Model_Security::BLACKLISTED_BY_AN_ADMIN);
                	}
                	
                	$ipAddress2long = Mage::getModel('sitesecurity/security')->getIp2long($model->getRemoteAddr());
                	$model->setRemoteAddr($ipAddress2long);
                	$model->save();
                
	                if (!$model->getId()) {
	                    Mage::throwException(Mage::helper('sitesecurity')->__('Error saving item'));
	                }
	 
	                Mage::getSingleton('adminhtml/session')->addSuccess(
	                		Mage::helper('adminhtml')->__(Mage::getModel('sitesecurity/security')->getLong2ip($model->getRemoteAddr()) . ' is successfully blacklisted')
	                	);
	                Mage::getSingleton('adminhtml/session')->setFormData(false);
                }
                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back') || $this->getRequest()->getParam('duplicated')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return $this;
                } else {
                    $this->_redirect('*/*/');
                    return $this;
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
                $model = Mage::getModel('sitesecurity/blacklist');
                $model->setId($id);
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
    				$model = Mage::getModel('sitesecurity/blacklist')
    				->load($sitesecurityId)
    				->setStatus($this->getRequest()->getParam('status'))
    				->setIsMassupdate(true)
    				->save();
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
	                $model = Mage::getModel('sitesecurity/blacklist');
	                $model->setId($sitesecurityId);
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