<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Freegift
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Sashas_Freegift_Block_Adminhtml_Freegift_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('freegift_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('freegift')->__('Freegift Rule'));
    }
}
