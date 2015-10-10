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
 * Controller with configuration logic
 *
 * @category   Eltrino
 * @package    Eltrino_Region
 */
class Eltrino_Region_Adminhtml_ConfigurationController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Load country configuration container.
     * Before creating container validate if limit of configurations was reached
     *
     * @return void
     */
    public function loadCountryConfigurationAction()
    {
//        $cfgCount = $this->getRequest()->getParam('cfgCount', 0);
        $cfgCount = 0;

        $cfgLimit = count(Mage::helper('eltrino_region')->getCountriesList());
        $response = array('error' => 0, 'msg' => null, 'html' => null);
        if (intval($cfgCount) < $cfgLimit) {
            $block = $this->getLayout()->createBlock('eltrino_region/adminhtml_system_config_country');
            $response['html'] = $block->toHtml();
        } else {
            $response['error'] = 1;
            $response['msg'] = Mage::helper('eltrino_region')->__(
                'Count of available configurations reached the limit.'
            );
        }
        $this->loadLayout('empty');
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $this->getResponse()->setBody(Zend_Json::encode($response));
    }

    /**
     * Load regions configuration for selected country
     *
     * @return void
     */
    public function loadRegionConfigurationAction()
    {
        $countryId = $this->getRequest()->getParam('countryCode');
        $fieldsetId = $this->getRequest()->get('fieldsetId');

        $containerElements = $this->getLayout()
            ->createBlock('eltrino_region/adminhtml_system_config_fieldset_elements');
        $response = array(
            'common_settings'  => $containerElements->getCommonSettings($countryId),
            'disabled_regions' => $containerElements->getDisabledRegions($fieldsetId, $countryId),
            'steps'            => $containerElements->getSteps($fieldsetId, $countryId)
        );
        $this->loadLayout('empty');
        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $this->getResponse()->setBody(Zend_Json::encode($response));
    }
}
