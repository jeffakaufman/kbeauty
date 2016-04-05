<?php

class Valkyrie_SliderData_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getSlidesList(){
        $collection = Mage::getModel('sliderdata/sliderdata')->getCollection();
        $collection->setOrder('sort_order', 'asc');
        $collection->addFieldToFilter('active', '1');

        $now = new DateTime("now", new DateTimeZone('America/Los_Angeles'));
        $now->setTimezone(new DateTimeZone('Europe/London'));

        $collection->addFieldToFilter("active_from", array(
            array('null' => true),
            array('lteq' => $now->format("Y-m-d")),
        ));
        $collection->addFieldToFilter("active_to", array(
            array('null' => true),
            array('gteq' => $now->format("Y-m-d")),
        ));
//        $collection
/*
        $collection->addAttributeToFilter(
            array(
                array(
                    'attribute' => 'active_from',
                    'null' => true
                ),
                array(
                    'attribute' => 'active_from',
                    'lteq' => $now->format("Y-m-d"),
                )
            )
        );

        $collection->addAttributeToFilter(
            array(
                array(
                    'attribute' => 'active_to',
                    'null' => true
                ),
                array(
                    'attribute' => 'active_to',
                    'gteq' => $now->format("Y-m-d"),
                )
            )
        );
*/
        return $collection;
    }


}