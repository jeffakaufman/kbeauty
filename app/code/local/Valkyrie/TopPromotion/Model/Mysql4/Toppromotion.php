<?php

class Valkyrie_TopPromotion_Model_Mysql4_TopPromotion extends Mage_Core_Model_Mysql4_Abstract
{
  protected function _construct()
  {
    $this->_init('toppromotion/toppromotion', 'promotion_id');
  }
}