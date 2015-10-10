<?php

class FME_Richsnippets_Model_Getattributescolor extends Mage_Core_Model_Abstract
{
  
    public function toOptionArray()
    {
        //Get all the attributes from eav model
       $attributes1 = Mage::getSingleton('eav/config')
    ->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getAttributeCollection();

// Localize attribute label (if you need it)
//$attributes1->addStoreLabel(Mage::app()->getStore()->getId());

// Loop over all attributes
foreach ($attributes1 as $attr) {
    /* @var $attr Mage_Eav_Model_Entity_Attribute */
    // get the store label value

    
    $label = $attr->getStoreLabel() ? $attr->getStoreLabel() : $attr->getFrontendLabel();
    //Display only those attributes that have website scope and not empty
    if(!empty($label) && $attr->getIsGlobal()=='1'){
    $methods[] = array( 
                'label'   => "{$label}",
                'value' =>  $label);
                 //'value' =>  $attr->getAttributeId());
}
  
    
}
          
    
        return $methods;
        
    }
    
    
}