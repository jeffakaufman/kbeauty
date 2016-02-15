<?php

class Valkyrie_TopPromotion_Block_Adminhtml_TopPromotion extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  protected $_addButtonLabel = 'Add New Promotion';

  public function __construct()
  {
    $this->_controller = 'adminhtml_toppromotion';
    $this->_blockGroup = 'toppromotion';
    $this->_headerText = Mage::helper('toppromotion')->__('Top Promotion');
    parent::__construct();
  }

}