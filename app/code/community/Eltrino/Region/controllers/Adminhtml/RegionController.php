<?php
/**
 * Remove or Change Displayed States and Regions
 *
 * LICENSE
 *
 * This source file is subject to the Eltrino LLC EULA
 * that is bundled with this package in the file LICENSE_EULA.txt.
 * It is also available through the world-wide-web at this URL:
 * http://eltrino.com/license-eula.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@eltrino.com so we can send you a copy immediately.
 *
 * @category    Eltrino
 * @package     Eltrino_Region
 * @copyright   Copyright (c) 2014 Eltrino LLC. (http://eltrino.com)
 * @license     http://eltrino.com/license-eula.txt  Eltrino LLC EULA
 */

/**
 * Resource model for configuration entity
 *
 * @category   Eltrino
 * @package    Eltrino_Region
 */
class Eltrino_Region_Adminhtml_RegionController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Default controller
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->renderLayout();
    }

    /**
     * Edit country regions
     *
     * @return void
     */
    public function editAction()
    {
        $code = $this->getRequest()->getParam('code');
        $countryName = Mage::app()->getLocale()->getCountryTranslation($code);

        $this->loadLayout()
            ->_setActiveMenu('system/eltrino_region_management')
            ->_title($this->__('Region Management'))
            ->_title($countryName)
            ->renderLayout();
    }

    /**
     * Display region grid block
     *
     * @return void
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('eltrino_region/adminhtml_region_grid')->toHtml()
        );
    }

    /**
     * Save country regions
     *
     * @return void
     */
    public function saveAction()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        $regions = $this->getRequest()->getPost('regions', false);
        $countryId = $this->getRequest()->getPost('country_id', false);
        if ($regions && $countryId) {
            $locale = Mage::app()->getLocale()->getLocaleCode();
            try {
                Mage::getResourceModel('eltrino_region/region')->saveRegions($countryId, $regions, $locale);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving.'));
                $this->_saveAndContinueEdit();
                return;
            }
        }

        if ($redirectBack) {
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The regions have been saved.'));
            $this->_saveAndContinueEdit();
        } else {
            $this->_redirect('*/*/');
        }
    }

    protected function _saveAndContinueEdit()
    {
        $this->_redirect(
            '*/*/edit', array(
                '_current' => true
            )
        );
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/eltrino_region_management');
    }
}