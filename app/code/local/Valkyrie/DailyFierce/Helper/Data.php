<?php

class Valkyrie_DailyFierce_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getCurrentFierce() {
        $collection = Mage::getModel('dailyfierce/dailyfierce')->getCollection();

        $now = new DateTime("now");

        $collection->addFieldToFilter("active_from", array(
            array('null' => true),
            array('lteq' => $now->format("Y-m-d")),
        ));
        $collection->addFieldToFilter("active_to", array(
            array('null' => true),
            array('gteq' => $now->format("Y-m-d")),
        ));

        $currentFierce = false;
        foreach($collection as $f) {
            $currentFierce = $f;
            break;
        }

        return $currentFierce;
    }

}