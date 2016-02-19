<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Freegift
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)

 */

class Sashas_Freegift_Block_Adminhtml_Freegift extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    { 
        $this->_addButton('apply_rules', array(
            'label'     => Mage::helper('freegift')->__('Apply Gift Rules'),
            'onclick'   => "location.href='".$this->getUrl('*/*/applyGifts')."'",
            'class'     => '',
        ));

        $this->_controller = 'adminhtml_freegift';
        $this->_blockGroup = 'freegift';
        $this->_headerText = Mage::helper('freegift')->__('Free Gift Rules');
        $this->_addButtonLabel = Mage::helper('freegift')->__('Add New Gift Rule');
        parent::__construct();

    }
}
