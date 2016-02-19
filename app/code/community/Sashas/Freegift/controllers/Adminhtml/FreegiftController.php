<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Freegift
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Sashas_Freegift_Adminhtml_FreegiftController extends Mage_Adminhtml_Controller_Action {
	
	protected function _construct()
	{
		// Define module dependent translate
		$this->setUsedModuleName('Sashas_Freegift');
	}
	
	protected function _initAction()
	{
		$this->loadLayout()->_setActiveMenu('promo/freegift') ->_addBreadcrumb(
                Mage::helper('freegift')->__('Freegift'),
                Mage::helper('freegift')->__('Freegift')
            );		 
		return $this;
	}
	/**
	 * Check for is allowed
	 *
	 * @return boolean
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('promo/freegift');
	}
	
 
	
	public function indexAction()
	{		 
		$this->_title($this->__('Promotions'))->_title($this->__('Free Gift Rules'));		 			
		$this->_initAction();	 		 
		$content_block=$this->getLayout()->createBlock('freegift/adminhtml_freegift');
		$this->getLayout()->getBlock('content')->append($content_block);
		$this->renderLayout();
	}
	
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	public function editAction()
	{
		$this->_title($this->__('Promotions'))->_title($this->__('Free Gift Rules'));
	
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('freegift/rule');
	
		if ($id) {
			$model->load($id);
			if (! $model->getRuleId()) {
				Mage::getSingleton('adminhtml/session')->addError(
						Mage::helper('freegift')->__('This gift rule no longer exists.')
				);
				$this->_redirect('*/*');
				return;
			}
		}
	
		$this->_title($model->getRuleId() ? $model->getName() : $this->__('New Gift Rule'));
	
		// set entered data if was error when we do save
		$data = Mage::getSingleton('adminhtml/session')->getPageData(true);
		if (!empty($data)) {
			$model->addData($data);
		}
		$model->getConditions()->setJsFormObject('rule_conditions_fieldset');
	
		Mage::register('current_freegift_rule', $model);
		 
		$this->_initAction()->getLayout()->getBlock('freegift_edit')
		->setData('action', $this->getUrl('*/freegift/save'));
		 
		$breadcrumb = $id
		? Mage::helper('freegift')->__('Edit Gift Rule')
		: Mage::helper('freegift')->__('New Gift Rule');
		$this->_addBreadcrumb($breadcrumb, $breadcrumb)->renderLayout();
	
	}
	
	public function saveAction()
	{
		if ($this->getRequest()->getPost()) {
			try {
				$model = Mage::getModel('freegift/rule');
				Mage::dispatchEvent(
						'adminhtml_controller_freegift_prepare_save',
						array('request' => $this->getRequest())
				);
				$data = $this->getRequest()->getPost();
				//$data = $this->_filterDates($data, array('from_date', 'to_date'));
				if ($id = $this->getRequest()->getParam('rule_id')) {
					$model->load($id);
					if ($id != $model->getId()) {
						Mage::throwException(Mage::helper('freegift')->__('Wrong rule specified.'));
					}
				}
	
				$validateResult = $model->validateData(new Varien_Object($data));
				if ($validateResult !== true) {
					foreach($validateResult as $errorMessage) {
						$this->_getSession()->addError($errorMessage);
					}
					$this->_getSession()->setPageData($data);
					$this->_redirect('*/*/edit', array('id'=>$model->getId()));
					return;
				}
	
				$data['conditions'] = $data['rule']['conditions'];
				$data['actions'] = $data['rule']['actions'];						
				unset($data['rule']);
				
				if (!empty($data['auto_apply'])) {
					$autoApply = true;
					unset($data['auto_apply']);
				} else {
					$autoApply = false;
				}				
				
				$model->loadPost($data);	
				Mage::getSingleton('adminhtml/session')->setPageData($model->getData());	
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('freegift')->__('The gift rule has been saved.'));
				Mage::getSingleton('adminhtml/session')->setPageData(false);
				if ($autoApply) {
					$this->getRequest()->setParam('rule_id', $model->getId());
					$this->_forward('applyRule');
				} else {
					if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('id' => $model->getId()));
						return;
					}
					$this->_redirect('*/*/');	
				}			
				return;
			} catch (Mage_Core_Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			} catch (Exception $e) {
				$this->_getSession()->addError(
						Mage::helper('freegift')->__('An error occurred while saving the freegift rule data. Please review the log and try again.')
				);
				Mage::logException($e);
				Mage::getSingleton('adminhtml/session')->setPageData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
				return;
			}
		}
		$this->_redirect('*/*/');
	}
	
	
	/**
	 * Apply gift rule 
	 */
	public function applyRuleAction()
	{
		$errorMessage = Mage::helper('freegift')->__('Unable to apply gift rules.');
		$rule_id=$this->getRequest()->getParam('rule_id');
		try {			 
			Mage::getModel('freegift/rule')->load($rule_id)->apply();		 
			$this->_getSession()->addSuccess(Mage::helper('freegift')->__('The gift rules have been applied.'));
		} catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($errorMessage . ' ' . $e->getMessage());
		} catch (Exception $e) {
			$this->_getSession()->addError($errorMessage);
		}	 
		$this->_redirect('*/*');
	}
	
	
	/**
	 * Apply all active gift rules
	 */
	public function applyGiftsAction()
	{
		$errorMessage = Mage::helper('freegift')->__('Unable to apply gift rules.');
		try {
			Mage::getModel('freegift/rule')->applyAll();					 
			$this->_getSession()->addSuccess(Mage::helper('freegift')->__('The gift rules have been applied.'));
		} catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($errorMessage . ' ' . $e->getMessage());
		} catch (Exception $e) {
			$this->_getSession()->addError($errorMessage);
		}
		$this->_redirect('*/*');
	} 
	
	
	public function deleteAction(){
		$rule_id=$this->getRequest()->getParam('id');
		Mage::getModel('freegift/rule_apply')->DeleteByRuleId($rule_id);
		Mage::getModel('freegift/rule')->load($rule_id)->delete();
		$this->_redirect('*/*');
	}
}