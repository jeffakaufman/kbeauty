<?php

class Aitoc_Aitreports_Model_Extendedxml extends SimpleXMLElement
{
    /** 
     * Add CDATA text in a node 
     * @param string $cdata_text The CDATA value  to add 
     */ 
    private function addCData($cdata_text) 
    { 
        $node= dom_import_simplexml($this); 
        $no = $node->ownerDocument; 
        $node->appendChild($no->createCDATASection($cdata_text)); 
    } 

   /** 
    * Create a child with CDATA value 
    * @param string $name The name of the child element to add. 
    * @param string $cdata_text The CDATA value of the child element. 
    */ 
    public function addChildCData($name,$cdata_text) 
    { 
        $child = parent::addChild($name); 
        $child->addCData($cdata_text); 
        return $child;
    }
     
    public function addChild($name, $text = null,$namespace='')
    {
        if(is_null($text) || $text == '') {
            $blockNames = array('aitcheckoutfields','creditmemos','creditmemo','comments','comment','items','item','invoices','invoice','fields','gift_message','addresses','address','payments','payment','transactions','transaction','statuseshistory','statushistory','shipments','shipment','trackings','tracking',/*ee*/'rmas','rma','shippinglabels','shippinglabel');        
            if(!in_array($name,$blockNames) && substr($name, 0, 6)!='aitbl_') {
                if(Mage::helper('aitreports')->isEmptyValuesAllowed() == false) {
                    return $this;
                }
            }
        }
        if($this->_isDateField($name)) {
            $text = $this->_convertDate($text);
        }
        if(is_numeric($text) || is_null($text) || $text == '') {
            return parent::addChild($name, $text);
        } else {
            return $this->addChildCData($name, $text);
        }
    }
    
    /**
     * Validate if field is required to be updated for locale date
     *     
     * @param string $key
     * 
     * @return bool
     */
    protected function _isDateField($key) {
        if(in_array($key, array('created_at','updated_at'))) {
            return true;
        }
        return false;
    }

    /**
     * Use magento default functional to convert date to default locale
     *     
     * @param string $date
     * 
     * @return string
     */
    protected function _convertDate($date) {
        $time = strtotime($date);
        $date = Mage::app()->getLocale()->date(
            $time,
            null,
            null,
            true
        );
        return $date->toString('YYYY-MM-dd hh:mm:ss');     
    }

    
}
