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
class Eltrino_Region_Block_Adminhtml_Widget_Button extends Mage_Adminhtml_Block_Widget_Button
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $html .= $this->getCheckboxHtml();
        return $html;
    }

    /**
     * Renders Use Default/Use Website checkbox as html code
     *
     * @return string
     */
    protected function getCheckboxHtml()
    {
        $website = $this->getRequest()->getParam('website');
        if (!$website) {
            return '';
        }

        $store = $this->getRequest()->getParam('store');
        if ($store) {
            $label = 'Use Website';
            $scope = 'stores';
            $scopeId = Mage::app()->getStore($store)->getId();
        } else {
            $label = 'Use Default';
            $scope = 'websites';
            $scopeId = Mage::app()->getWebsite($website)->getId();
        }

        $checked = '';
        /* @var $helper Eltrino_Region_Helper_Data */
        $helper = Mage::helper('eltrino_region');
        if ($helper->getConfigDataUseDefault($scope, $scopeId)) {
            $checked = "checked='checked'";
        }

        $html
            = '<input id="region_use_default" name="region_use_default" type="checkbox" value="1" class="checkbox config-inherit regionInherit" ';
        $html .= $checked . ' onclick="toggleValueElements(this, \'region_configuration\')">';
        $html .= '<label for="design_theme_locale_inherit" class="inherit paddingRight3" title="">'
            . $label . ' <span class="scope">[STORE VIEW]</span></label>';
        if ($checked) {
            $html .= "<script>document.observe('dom:loaded', function() { toggleValueElements($('region_use_default'), 'region_configuration') });</script>";
        }
        return $html;
    }
}
