<?php

class Valkyrie_Press_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getFullList(){
        $collection = Mage::getModel('press/press')->getCollection();
//        $collection->setOrder('sort_order', 'asc');

        return $collection;
    }


}