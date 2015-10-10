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
class Eltrino_Region_Block_Adminhtml_System_Config_Country extends Mage_Adminhtml_Block_Widget_Accordion
{
    protected function _toHtml()
    {
        $country = Mage::getModel('directory/country')->load($this->getCountryId());
        $countryName = '';
        if ($country && $country->getId()) {
            $countryName = ' (' . $country->getName() . ')';
        }
        $container = $this->getLayout()->createBlock('eltrino_region/adminhtml_system_config_fieldset_container')
            ->setCountryId($this->getCountryId())
            ->setDisabledRegions($this->getDisabledRegions())
            ->setFieldsetId($this->getFieldsetId());
        $this->addItem(
            'region_configuration', array(
                'title'   => Mage::helper('eltrino_region')->__('Region Configuration%s', $countryName),
                'content' => $container->toHtml(),
                'open'    => true,
            )
        );
        return parent::_toHtml();
    }
}
