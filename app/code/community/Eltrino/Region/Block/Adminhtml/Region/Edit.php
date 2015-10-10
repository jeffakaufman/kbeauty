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
class Eltrino_Region_Block_Adminhtml_Region_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'eltrino_region';
        $this->_controller = 'adminhtml_region';
        $this->_updateButton('save', 'label', $this->__('Save'));
        $this->_addButton(
            'save_and_edit_button', array(
                'label'   => Mage::helper('eltrino_region')->__('Save and Continue Edit'),
                'onclick' => "regionsControl.saveAndContinueEdit('" . $this->getSaveAndContinueUrl() . "');",
                'class'   => 'save'
            ), 10
        );
    }

    public function getSaveAndContinueUrl()
    {
        return $this->getUrl(
            '*/*/save', array(
                '_current' => true,
                'back'     => 'edit'
            )
        );
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('Region Management Pro');
    }
}