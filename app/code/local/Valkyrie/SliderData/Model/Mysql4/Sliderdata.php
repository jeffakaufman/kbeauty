<?php

class Valkyrie_SliderData_Model_Mysql4_SliderData extends Mage_Core_Model_Mysql4_Abstract
{
  protected function _construct()
  {
    $this->_init('sliderdata/sliderdata', 'slide_id');
  }
}