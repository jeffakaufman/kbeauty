<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Callforprice
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */
class Sashas_Freegift_Block_Adminhtml_Actions implements Varien_Data_Form_Element_Renderer_Interface
{
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		if ($element->getRule() && $element->getRule()->getActions()) {
			$wrapper='<ul class="rule-param-children" id="actions__1__children"><li>';
			return $wrapper.$element->getRule()->getActions()->asHtmlRecursive()."</li></ul>";
		}
		return '';
	}
}
