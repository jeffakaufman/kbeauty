<?php

class Valkyrie_PromotionModules_Model_Mysql4_PromotionModules extends Mage_Core_Model_Mysql4_Abstract
{
  protected function _construct()
  {
    $this->_init('promotionmodules/promotionmodules', 'module_id');
  }
}