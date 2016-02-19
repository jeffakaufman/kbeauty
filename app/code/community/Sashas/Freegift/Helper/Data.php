<?php
/**
 * @author		Sashas
 * @category    Sashas
 * @package     Sashas_Freegift
 * @copyright   Copyright (c) 2015 Sashas IT Support Inc. (http://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Sashas_Freegift_Helper_Data extends Mage_Core_Helper_Abstract {
	
 
    /**
     * @param Sashas_Freegift_Model_Resource_Rule_Apply_Collection $collection
     * @param array $added_products_ids
     */
    public function checkFreeGiftRules(Sashas_Freegift_Model_Resource_Rule_Apply_Collection $collection){
        
        $added_products_ids=array();        
        /* Check cart Rule and add Products */
        foreach ($collection as $item) {
            Mage::log('RULE ID#: '.$item->getRuleId(), null, 'freegift.log');
            $cart=unserialize($item->getCartSerialized());
            	
            if (count($cart['rules'])>0) {
                $all=$cart['all'];
                $true=$cart['true'];
                $other_conditions=$cart['other']; //if matched not only by subtotal/qty;
                //$rule_validated=$other_conditions;	// Will be 1 in case of ANY
                $rule_validated=0;
                foreach ($cart['rules'] as $cart_rule) {
        
                    $operator=$cart_rule['operator'];
                    $value=$cart_rule['value'];
                    $check_param='';
                    if ($cart_rule['attribute']=='base_subtotal') {
                        $check_param=Mage::helper('checkout/cart')->getQuote()->getSubtotal();                        
                        Mage::log('Free Gift Validate Subtotal', null, 'freegift.log');
                        Mage::log('Subtotal: '.$check_param, null, 'freegift.log');         
                    } elseif ($cart_rule['attribute']=='total_qty') {
                        Mage::log('Free Gift Validate Cart Qty', null, 'freegift.log');
                        $check_param = Mage::helper('checkout/cart')->getQuote()->getItemsCount();
                        Mage::log('Cart Qty: '.$check_param, null, 'freegift.log');
                    }else{
                        $check_param=$cart_rule['attribute'];
                        Mage::log('Free Gift Validate Attribute: '.$check_param, null, 'freegift.log');
                    }
                    /* Validate Rules */
                    Mage::log('Condition: '.$check_param.' '.$operator.' '.$value.' All True: '.$true, null, 'freegift.log');
                    if ( ($this->validate($check_param, $operator, $value) && $true) || (!$this->validate($check_param, $operator, $value) && !$true) )	{
                        $rule_validated++;
                        Mage::log('Conditions validated: '.$rule_validated, null, 'freegift.log');
                    } else {
                        $rule_validated--;
                        Mage::log('Conditions validated: '.$rule_validated, null, 'freegift.log');
                    }
                }
                Mage::log('TOTAL Validated Condition: '.$rule_validated, null, 'freegift.log');
        
                //$rule_validated equal to cart items if ALL (one more in case of choosed sku) | ANY - more than zero
                if ( ($all=='all' && $rule_validated>=count($cart['rules']) )|| ($all=='any' && $rule_validated >0 ) ) {
                    /*OPTIMIZE*/
                    $gift_product_ids=$item->getGiftProductIds();
                    $gift_product_ids=explode(',',$gift_product_ids);
                    	
                    // check qty & add to cart
                    foreach ($gift_product_ids as $gift_product_id) {
                        if (in_array($gift_product_id,$added_products_ids))
                            continue; //skip if already added
                        array_push($added_products_ids,$gift_product_id);
                    }
                    /*Added according range items*/                    	
                } 
        
            }else {
                //in case without cart condition
                Mage::log('Free Gift Rule Without Conditions ', null, 'freegift.log');
                $gift_product_ids=$item->getGiftProductIds();
                $gift_product_ids=explode(',',$gift_product_ids);
                // check qty & add to cart
                foreach ($gift_product_ids as $gift_product_id) {
                    if (in_array($gift_product_id,$added_products_ids))
                        continue; //skip if already added
                    array_push($added_products_ids,$gift_product_id);
                }
        
            }
        } /*endforeach*/
        
        return $added_products_ids;
    }
    
    
    /**
     * @param string $param
     * @param string $cond
     * @param string $val
     * @return number
     */
    public function validate($param,$cond,$val) {
        
        if ($cond=='==' && $param==$val) {
            return 1;
        }elseif($cond=='>' && $param>$val) {
            return 1;
        }elseif($cond=='<' && $param < $val) {
            return 1;
        }elseif($cond=='<=' && $param <= $val) {
            return 1;
        }elseif($cond=='>=' && $param >= $val) {
            return 1;
        }
        	
        return 0;
    }
}