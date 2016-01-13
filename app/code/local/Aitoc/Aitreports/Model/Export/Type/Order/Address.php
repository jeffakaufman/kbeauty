<?php

class Aitoc_Aitreports_Model_Export_Type_Order_Address implements Aitoc_Aitreports_Model_Export_Type_Interface
{
    /**
     *
     * @param SimpleXMLElement $orderXml
     * @param Mage_Sales_Model_Order $order
     * @param Varien_Object $exportConfig
     */
    public function prepareXml(SimpleXMLElement $orderXml, Mage_Core_Model_Abstract $order, Varien_Object $exportConfig)
    {
        /* @var $order Mage_Sales_Model_Order */

        if (empty($exportConfig['entity_type']['order_address']))
        {
            return false;
        }

        // Filter invoices by order Id
        $orderAddressCollection = $order->getAddressesCollection();

        $addressesXml = $orderXml->addChild('addresses');

        foreach ($orderAddressCollection as $address)
        {
            $addressXml = $addressesXml->addChild('address');

            foreach($address->getData() as $field => $value)
            {
                if(($field == 'email') AND (empty($value)))
                {
                    $addressXml->addChild($field, $order->getCustomerEmail());
                }
                else
                {
                    $addressXml->addChild($field, (string)$value);
                }
            }

            if(version_compare(Mage::getVersion(),'1.4.1.0','<'))
            {
                $addressXml->addChild('email', $order->getCustomerEmail());
            }
        }
    }
}
