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
 * Fieldset for configuration fields
 *
 * @category   Eltrino
 * @package    Eltrino_Region
 */
class Eltrino_Region_Block_Adminhtml_System_Config_Fieldset
    extends Mage_Adminhtml_Block_Template
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'eltrino/region/system/config/fieldset.phtml';

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setChild(
            'add_button', $this->getLayout()->createBlock('eltrino_region/adminhtml_widget_button')->setData(
                array(
                    'label'   => Mage::helper('eltrino_region')->__('New Region Configuration'),
                    'onclick' => "newCountryConfigurationAction(); return false;"
                )
            )
        );
        /** @var  $head Mage_Adminhtml_Block_Page_Head */
        $head = $this->getLayout()->getBlock('head');
        $head->addCss('eltrino/region.css');
        return $this;
    }

    /**
     * Prepare containers of saved regions configuration
     *
     * @return array
     */
    public function getCountriesDisabledRegions()
    {
        $containers = array();
        /* @var $helper Eltrino_Region_Helper_Data */
        $helper = Mage::helper('eltrino_region');
        $scope = $helper->getActualScopeFromRequest();
        $data = Mage::getSingleton('eltrino_region/entity')->fetchCountriesDisabledRegions(
            $scope['scope_id'], $scope['scope']
        );
        foreach ($data as $fieldsetId => $regions) {
            $countryId = key($regions);
            $container = $this->getLayout()->createBlock('eltrino_region/adminhtml_system_config_country')
                ->setCountryId($countryId)
                ->setDisabledRegions($regions[$countryId])
                ->setFieldsetId($fieldsetId);
            $containers[] = $container;
        }
        return $containers;
        /** array of @see Eltrino_Region_Block_Adminhtml_System_Config_Country objects */
    }

    public function getNextFieldsetId()
    {
        $maxFieldsetId = Mage::getResourceModel('eltrino_region/entity')->getMaxFieldsetId();
        $maxFieldsetId = (int)$maxFieldsetId;
        return ++$maxFieldsetId;
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }
}
