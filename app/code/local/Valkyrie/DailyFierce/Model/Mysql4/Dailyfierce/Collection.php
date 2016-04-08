<?php

class Valkyrie_DailyFierce_Model_Mysql4_DailyFierce_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
  protected function _construct()
  {
    $this->_init('dailyfierce/dailyfierce');
  }
}