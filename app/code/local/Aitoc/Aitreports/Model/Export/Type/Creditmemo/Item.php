<?php

class Aitoc_Aitreports_Model_Export_Type_Creditmemo_Item implements Aitoc_Aitreports_Model_Export_Type_Interface
{
    /**
     * 
     * @param SimpleXMLElement $creditmemoXml
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @param Varien_Object $exportConfig
     */
    public function prepareXml(SimpleXMLElement $creditMemoXml, Mage_Core_Model_Abstract $creditMemo, Varien_Object $exportConfig)
    {
        /* @var $creditMemo Mage_Sales_Model_Order_Creditmemo */

        $creditMemoItemsXml = $creditMemoXml->addChild('items');

        foreach ($creditMemo->getItemsCollection() as $creditMemoItem)
        {
            $creditMemoItemXml = $creditMemoItemsXml->addChild('item');
            
            foreach($creditMemoItem->getData() as $field => $value)
            {
                $creditMemoItemXml->addChild($field, (string)$value);
            }
        }
    }
}
