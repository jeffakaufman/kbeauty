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
 * Renderer for country configuration field
 *
 * @category   Eltrino
 * @package    Eltrino_Region
 */
class Eltrino_Region_Block_Adminhtml_System_Config_Fieldset_Container extends Mage_Adminhtml_Block_Widget
{
    protected $_template = 'eltrino/region/system/config/fieldset/container.phtml';

    protected $_countryId = null;

    protected $_disabledRegions = array();

    protected $_fieldsetId = null;

    public function setCountryId($countryId)
    {
        $this->_countryId = $countryId;
        return $this;
    }

    public function getCountryId()
    {
        return $this->_countryId;
    }

    public function setDisabledRegions($disabledRegions)
    {
        $this->_disabledRegions = $disabledRegions;
        return $this;
    }

    public function getDisabledRegions()
    {
        return $this->_disabledRegions;
    }

    public function setFieldsetId($fieldsetId)
    {
        $this->_fieldsetId = $fieldsetId;
        return $this;
    }

    public function getFieldsetId()
    {
        return $this->_fieldsetId;
    }

    public function getCountriesElmHtml()
    {
        return $this->getLayout()->createBlock('eltrino_region/adminhtml_system_config_fieldset_elements')
            ->getCountries($this->getCountryId());
    }

    /**
     * Return HTML of common settings element
     *
     * @return string
     */
    public function getCommonSettingsElmHtml()
    {
        $regions = $this->getDisabledRegions();
        $selected = null;
        if ($regions) {
            $commonSettings = Mage::helper('eltrino_region')->getCommonSettingsList($this->getCountryId());
            foreach ($commonSettings as $item) {
                $preselectedRegions = explode(',', $item['value']);
                if (count($regions) != count($preselectedRegions)) {
                    continue;
                }
                foreach ($preselectedRegions as $region) {
                    if (!in_array($region, $regions)) {
                        continue;
                    }
                }
                $selected = $item['value'];
            }
        }

        return $this->getLayout()->createBlock('eltrino_region/adminhtml_system_config_fieldset_elements')
            ->getCommonSettings($this->getCountryId(), $selected);
    }

    public function getDisabledRegionsElmHtml()
    {
        return $this->getLayout()->createBlock('eltrino_region/adminhtml_system_config_fieldset_elements')
            ->getDisabledRegions(
                $this->getFieldsetId(), $this->getCountryId(), $this->getDisabledRegions(), $this->getCommonSettings()
            );
    }

    public function getRemoveButtonHtml()
    {
        return $this->getButtonHtml(
            Mage::helper('eltrino_region')->__('Remove'),
            'this.up(\'div.region-configuration-container\').remove();return false;', 'delete'
        );
    }

    public function getStepsHtml()
    {
        return $this->getLayout()->createBlock('eltrino_region/adminhtml_system_config_fieldset_elements')
            ->getSteps($this->getFieldsetId(), $this->getCountryId());
    }
}
