<?php

class Aitoc_Aitreports_Model_Export_Type_Shipment_Comment implements Aitoc_Aitreports_Model_Export_Type_Interface
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

        $shipmentCommentsXml = $shipmentXml->addChild('comments');

        foreach ($shipment->getCommentsCollection() as $shipmentComment)
        {
            $shipmentCommentXml = $shipmentCommentsXml->addChild('comment');

            foreach($shipmentComment->getData() as $field => $value)
            {
                $shipmentCommentXml->addChild($field, (string)$value);
            }
        }
    }
}
