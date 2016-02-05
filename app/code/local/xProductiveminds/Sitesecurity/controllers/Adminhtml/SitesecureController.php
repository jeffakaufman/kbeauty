<?php
 
class Productiveminds_Sitesecurity_Adminhtml_SitesecureController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('productiveminds');
		$this->_title('Denied Access - Site Security');
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
    
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('sitesecurity/sitesecure');
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
    
    public function massDeleteAction() {
    	$sitesecurityIds = $this->getRequest()->getParam('sitesecuritys');
    	if (!is_array($sitesecurityIds)) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select items(s)'));
    	} else {
    		try {
    			foreach ($sitesecurityIds as $sitesecurityId) {
	                $model = Mage::getModel('sitesecurity/sitesecure');
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
    
    public function blockipfromorderAction() {
    	$pms_sitesecurity_ip = $this->getRequest()->getParam('ip');
    	try {
    		$user = Mage::getSingleton('admin/session')->getData('user');
    		$isAlreadyBlocked = Mage::getModel('sitesecurity/sitesecure')->load($pms_sitesecurity_ip, 'remote_addr');
    		if (null != $isAlreadyBlocked && !empty($isAlreadyBlocked) && $isAlreadyBlocked->getStatus() == 1) {
    			Mage::getSingleton('adminhtml/session')->addError(
    					Mage::helper('adminhtml')->__('%d is already blocked', $pms_sitesecurity_ip)
    			);
    		} else {
    			$sitesecureModel = Mage::getModel('sitesecurity/sitesecure');
    			$sitesecureModel->setAclId(Productiveminds_Sitesecurity_Model_Security::ACL_ID_BLOCK_IP);
    			$sitesecureModel->setUserId($user->getId());
    			//$sitesecureModel->setServerAddr($pms_sitesecurity_ip);
    			$sitesecureModel->setRemoteAddr($pms_sitesecurity_ip);
    			$sitesecureModel->setBlockedFrom(Productiveminds_Sitesecurity_Model_Security::BLOCKED_FROM_ORDER);
    			$sitesecureModel->setStatus(Productiveminds_Sitesecurity_Model_System_Config_Source_Status::ENABLED);
    			$sitesecureModel->setUpdatedAt(gmdate('Y-m-d H:i:s'));
    			$sitesecureModel->save();
    			 
    			Mage::getSingleton('adminhtml/session')->addSuccess(
    					Mage::helper('adminhtml')->__('%d is successully blocked', $pms_sitesecurity_ip)
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
    
    public function blockipfromreviewAction() {
    	$sitesecurityIds = $this->getRequest()->getParam('reviews');
    	if (!is_array($sitesecurityIds)) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select shome items(s)'));
    	} else {
    		try {
    			foreach ($sitesecurityIds as $sitesecurityId) {
    				$review = Mage::getModel('review/review')->load($sitesecurityId);
    				$user = Mage::getSingleton('admin/session')->getData('user');
    				$isAlreadyBlocked = Mage::getModel('sitesecurity/sitesecure')->load($review->getPmsSitesecurityIp(), 'remote_addr');
    				if (null != $isAlreadyBlocked && !empty($isAlreadyBlocked) && $isAlreadyBlocked->getStatus() == 1) {
    					// Do nothing, IP address is already blocked.
    				} else {
    					$sitesecureModel = Mage::getModel('sitesecurity/sitesecure');
    					$sitesecureModel->setAclId(Productiveminds_Sitesecurity_Model_Security::ACL_ID_BLOCK_IP);
    					$sitesecureModel->setUserId($user->getId());
    					//$sitesecureModel->setServerAddr($review->getPmsSitesecurityIp());
    					$sitesecureModel->setRemoteAddr($review->getPmsSitesecurityIp());
    					$sitesecureModel->setBlockedFrom(Productiveminds_Sitesecurity_Model_Security::BLOCKED_FROM_REVIEW);
    					$sitesecureModel->setStatus(Productiveminds_Sitesecurity_Model_System_Config_Source_Status::ENABLED);
    					$sitesecureModel->setUpdatedAt(gmdate('Y-m-d H:i:s'));
    					$sitesecureModel->save();
    				}
    				unset($isAlreadyBlocked);
    			}
    			Mage::getSingleton('adminhtml/session')->addSuccess(
    					Mage::helper('adminhtml')->__('%d IP address(es) were successully blocked', count($sitesecurityIds))
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
    
    public function massblockipAction() {
    	$sitesecurityIds = $this->getRequest()->getParam('sitesecuritys');
    	if (!is_array($sitesecurityIds)) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select shome items(s)'));
    	} else {
    		try {
    			foreach ($sitesecurityIds as $sitesecurityId) {
    				$visitor = Mage::getModel('sitesecurity/visitor')->load($sitesecurityId);
    				$user = Mage::getSingleton('admin/session')->getData('user');
    				$isAlreadyBlocked = Mage::getModel('sitesecurity/sitesecure')->load($visitor->getRemoteAddr(), 'remote_addr');
    				if (null != $isAlreadyBlocked && !empty($isAlreadyBlocked) && $isAlreadyBlocked->getStatus() == 1) {
    					// Do nothing, IP address is already blocked.
    				} else {
    					$sitesecureModel = Mage::getModel('sitesecurity/sitesecure');
    					$sitesecureModel->setAclId(Productiveminds_Sitesecurity_Model_Security::ACL_ID_BLOCK_IP);
    					$sitesecureModel->setUserId($user->getId());
    					$sitesecureModel->setServerAddr($visitor->getServerAddr());
    					$sitesecureModel->setRemoteAddr($visitor->getRemoteAddr());
    					$sitesecureModel->setBlockedFrom(Productiveminds_Sitesecurity_Model_Security::BLOCKED_FROM_VISITOR);
    					$sitesecureModel->setStatus(Productiveminds_Sitesecurity_Model_System_Config_Source_Status::ENABLED);
    					$sitesecureModel->setUpdatedAt(gmdate('Y-m-d H:i:s'));
    					$sitesecureModel->save();
    				}
    				unset($isAlreadyBlocked);
    			}
    			Mage::getSingleton('adminhtml/session')->addSuccess(
    					Mage::helper('adminhtml')->__(
    							'%d IP address(es) were successully blocked', count($sitesecurityIds)
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
 
}