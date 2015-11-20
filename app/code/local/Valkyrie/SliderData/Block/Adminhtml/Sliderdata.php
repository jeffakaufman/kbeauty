<?php

class Valkyrie_SliderData_Block_Adminhtml_SliderData extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  protected $_addButtonLabel = 'Add New Slide';

  public function __construct()
  {
    $this->_controller = 'adminhtml_sliderdata';
    $this->_blockGroup = 'sliderdata';
    $this->_headerText = Mage::helper('sliderdata')->__('SliderData');
    parent::__construct();
  }

}