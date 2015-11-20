<?php

/**
 * Product:       Xtento_StockImport (2.2.8)
 * ID:            WoetuzBqimD1uDNOwepRNUAFKdmy9BrgG2qHWNW+DsA=
 * Packaged:      2015-03-18T17:20:17+00:00
 * Last Modified: 2014-06-15T15:36:45+02:00
 * File:          app/code/local/Xtento/StockImport/controllers/Adminhtml/Stockimport/ProfileController.php
 * Copyright:     Copyright (c) 2015 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Adminhtml_StockImport_ProfileController extends Xtento_StockImport_Controller_Abstract
{
    public function indexAction()
    {
        if (!Xtento_StockImport_Model_System_Config_Source_Order_Status::isEnabled() || !Mage::helper('xtento_stockimport')->getModuleEnabled()) {
            return $this->_redirect('*/stockimport_index/disabled');
        }
        $this->_initAction()->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $model = Mage::getModel('xtento_stockimport/profile');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_stockimport')->__('This profile no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            if (!$model->getEntity()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_stockimport')->__('No import entity has been set for this profile.'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            // Default values
            #$model->setSaveFilesLocalCopy(1);
        }
        #var_dump($model->getData()); die();

        $this->_title($model->getId() ? $model->getName() : Mage::helper('xtento_stockimport')->__('New Profile'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        } else {
            // Handle certain fields
            $fields = array('store_ids', 'customer_groups', 'event_observers', 'import_filter_status', 'import_filter_product_type');
            foreach ($fields as $field) {
                $value = $model->getData($field);
                if (!is_array($value)) {
                    $model->setData($field, explode(',', $value));
                }
            }
        }

        Mage::unregister('stock_import_profile');
        Mage::register('stock_import_profile', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('xtento_stockimport')->__('Edit Profile') : Mage::helper('xtento_stockimport')->__('New Profile'), $id ? Mage::helper('xtento_stockimport')->__('Edit Profile') : Mage::helper('xtento_stockimport')->__('New Profile'))
            ->_addContent($this->getLayout()->createBlock('xtento_stockimport/adminhtml_profile_edit')->setData('action', $this->getUrl('*/*/save')))
            ->_addLeft($this->getLayout()->createBlock('xtento_stockimport/adminhtml_profile_edit_tabs'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(1);
        $this->getLayout()->getBlock('head')->setCanLoadRulesJs(1);

        $this->renderLayout();

        if (Mage::getSingleton('adminhtml/session')->getProfileDuplicated()) {
            Mage::getSingleton('adminhtml/session')->setProfileDuplicated(0);
        }
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getModel('xtento_stockimport/profile');
            $model->setData($postData);
            if ($model->getId()) {
                $profile = Mage::getModel('xtento_stockimport/profile')->load($model->getId());
                Mage::unregister('stock_import_profile');
                Mage::register('stock_import_profile', $profile);
            }
            $model->setLastModification(now());

            if (!$model->getId()) {
                $model->setEnabled(1);
            }

            // Prepare mapping
            if (isset($postData['mapping'])) {
                $mapping = $postData['mapping'];
                if (is_array($mapping)) {
                    if (!isset($mapping['__save_data']) && isset($mapping['__type'])) {
                        // save_data was not set by our Javascript.. let's better load the fail-safe database configuration instead of risking losing the mapping
                        unset($postData['mapping']);
                    } else {
                        unset($mapping['__empty']);
                        unset($mapping['__type']);
                        unset($mapping['__save_data']);
                        foreach ($mapping as $id => $data) {
                            if (!isset($data['field'])) {
                                unset($mapping[$id]);
                                continue;
                            }
                            if ($data['field'] == '') {
                                unset($mapping[$id]);
                            }
                        }
                        $postData['mapping'] = $mapping;
                    }
                }
            }

            $skippedFields = array('form_key', 'page', 'limit', 'log_id');
            $configurationToSave = array();
            $tableFields = Mage::getSingleton('core/resource')->getConnection('core_read')->describeTable(Mage::getSingleton('core/resource')->getTableName('xtento_stockimport_profile'));
            foreach ($postData as $confKey => $confValue) {
                if (!isset($tableFields[$confKey]) && !in_array($confKey, $skippedFields) && !preg_match('/col_/', $confKey)) {
                    if (is_array($confValue) && isset($confValue['from'])  && isset($confValue['to'])) {
                        continue;
                    }
                    $configurationToSave[$confKey] = $confValue;
                }
            }
            $model->setConfiguration($configurationToSave);

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->setFormData(false);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xtento_stockimport')->__('The import profile has been saved.'));
                if ($this->getRequest()->getParam('continue')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), 'active_tab' => $this->getRequest()->getParam('active_tab')));
                } else {
                    $this->_redirect('*/*');
                }
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_stockimport')->__('An error occurred while saving this import profile: ' . $e->getMessage()));
            }

            Mage::getSingleton('adminhtml/session')->setFormData($postData);
            $this->_redirectReferer();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_stockimport')->__('Could not find any data to save in the POST request. POST request too long maybe?'));
            $this->_redirect('*/*');
        }
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $model = Mage::getModel('xtento_stockimport/profile');
        $model->load($id);

        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_stockimport')->__('This profile does not exist anymore.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $model->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xtento_stockimport')->__('Profile has been successfully deleted.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

    public function duplicateAction()
    {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_stockimport')->__('Please select a profile to duplicate.'));
            return $this->_redirect('*/*');
        }

        try {
            $model = Mage::getModel('xtento_stockimport/profile');
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_stockimport')->__('This profile does not exist anymore.'));
                return $this->_redirect('*/*');
            }

            $profile = clone $model;
            $profile->setEnabled(0);
            $profile->setId(null);
            $profile->setLastModification(now());
            $profile->setLastExecution(null);
            $profile->save();

            Mage::getSingleton('adminhtml/session')->setProfileDuplicated(1);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xtento_stockimport')->__('The profile has been duplicated. Please make sure to enable it.'));
            $this->_redirect('*/*/edit', array('id' => $profile->getId()));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*');
        }
    }

    public function sourceAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function sourceGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function logGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function historyGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('profile');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_stockimport')->__('Please select profiles to delete.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            foreach ($ids as $id) {
                $model = Mage::getModel('xtento_stockimport/profile');
                $model->load($id);
                $model->delete();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($ids)));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

    public function massUpdateStatusAction()
    {
        $ids = $this->getRequest()->getParam('profile');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xtento_stockimport')->__('Please select profiles to modify.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            foreach ($ids as $id) {
                $model = Mage::getModel('xtento_stockimport/profile');
                $model->load($id);
                $model->setEnabled($this->getRequest()->getParam('enabled'));
                $model->save();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully updated', count($ids)));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/stockimport')
            ->_title(Mage::helper('xtento_stockimport')->__('Stock Import'))->_title(Mage::helper('xtento_stockimport')->__('Profiles'));
        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/stockimport/profile');
    }
}