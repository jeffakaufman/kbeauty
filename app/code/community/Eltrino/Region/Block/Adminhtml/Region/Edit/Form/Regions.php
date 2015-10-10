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
 * Model for configuration entity
 *
 * @category   Eltrino
 * @package    Eltrino_Region
 */
class Eltrino_Region_Block_Adminhtml_Region_Edit_Form_Regions extends Mage_Adminhtml_Block_Template
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_element;

    public function __construct()
    {
        $this->setTemplate('eltrino/region/regions.phtml');
    }

    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Return json regions for country
     *
     * @return string
     */
    public function getRegionList()
    {
        $code = $this->getRequest()->get('code');
        $collection = Mage::getModel('directory/region')->getCollection()
            ->addCountryFilter($code);

        $json = $jsonData = Mage::helper('core')->jsonEncode($collection->getData());

        return $json;
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }
}
