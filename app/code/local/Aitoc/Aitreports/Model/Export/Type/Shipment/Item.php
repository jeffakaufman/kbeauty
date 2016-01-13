<?php

class Aitoc_Aitreports_Model_Export_Type_Shipment_Item implements Aitoc_Aitreports_Model_Export_Type_Interface
{
    /**
     * 
     * @param SimpleXMLElement $shipmentXml
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param Varien_Object $exportConfig
     */
    public function prepareXml(SimpleXMLElement $shipmentXml, Mage_Core_Model_Abstract $shipment, Varien_Object $exportConfig)
    {
        /* @var $shipment Mage_Sales_Model_Order_Shipment */

        $shipmentItemsXml = $shipmentXml->addChild('items');

        foreach ($shipment->getItemsCollection() as $shipmentItem)
        {
            $shipmentItemXml = $shipmentItemsXml->addChild('item');

            foreach($shipmentItem->getData() as $field => $value)
            {
                $shipmentItemXml->addChild($field, (string)$value);
            }
        }
    }
}
