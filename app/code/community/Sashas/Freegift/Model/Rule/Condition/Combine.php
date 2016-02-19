<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Freegift
 * @copyright   Copyright (c) 2013 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Sashas_Freegift_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
 
    public function __construct()
    {
        parent::__construct();
        $this->setType('freegift/rule_condition_combine');
        
    }
    
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            1 => Mage::helper('rule')->__('TRUE'),
         //   0 => Mage::helper('rule')->__('FALSE'),
        ));
        return $this;
    }
      
    public function getNewChildSelectOptions()
    {
    	$avaiable_cart_attributes=array('base_subtotal','total_qty');
    	$not_avaiable_product_attributes=array('activation_information','custom_design_from','custom_design_to','gift_message_available','custom_design',
    		'custom_layout_update','options_container','enable_googlecheckout','meta_keyword','meta_title','page_layout','price_view');
        $productCondition = Mage::getModel('catalogrule/rule_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($productAttributes as $code=>$label) {
        	if (!in_array($code,$not_avaiable_product_attributes))
            $attributes[] = array('value'=>'catalogrule/rule_condition_product|'.$code, 'label'=>$label);
        }
        
        $addressCondition = Mage::getModel('freegift/rule_condition_address');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributes_address = array();
       
        foreach ($addressAttributes as $code=>$label) {
        	if (in_array($code,$avaiable_cart_attributes))
        	$attributes_address[] = array('value'=>'freegift/rule_condition_address|'.$code, 'label'=>$label);
        }
        
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
        		/*removed due to error with combination*/
        /*    array('value'=>'freegift/rule_condition_combine', 'label'=>Mage::helper('freegift')->__('Conditions Combination')),*/
            array('label'=>Mage::helper('freegift')->__('Product Attribute'), 'value'=>$attributes),
        	array('label'=>Mage::helper('freegift')->__('Cart Attribute'), 'value'=>$attributes_address),
        ));
        return $conditions;
    }

   
    
    
    
}
