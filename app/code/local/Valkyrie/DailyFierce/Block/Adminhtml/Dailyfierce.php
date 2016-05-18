<?php

class Valkyrie_DailyFierce_Block_Adminhtml_DailyFierce extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  protected $_addButtonLabel = 'Add New Daily Fierce';

  public function __construct()
  {
    $this->_controller = 'adminhtml_dailyfierce';
    $this->_blockGroup = 'dailyfierce';
    $this->_headerText = Mage::helper('dailyfierce')->__('Daily Fierce');
    parent::__construct();
  }

}