<?php

class Valkyrie_PromotionModules_Block_Adminhtml_PromotionModules extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  protected $_addButtonLabel = 'Add New Module';

  public function __construct()
  {
    $this->_controller = 'adminhtml_promotionmodules';
    $this->_blockGroup = 'promotionmodules';
    $this->_headerText = Mage::helper('promotionmodules')->__('PromotionModules');
    parent::__construct();
  }

}