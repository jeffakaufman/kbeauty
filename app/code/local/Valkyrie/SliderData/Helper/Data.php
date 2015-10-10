<?php

class Valkyrie_SliderData_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getSlidesList(){
        $collection = Mage::getModel('sliderdata/sliderdata')->getCollection();
        $collection->setOrder('sort_order', 'asc');

        return $collection;
    }


}