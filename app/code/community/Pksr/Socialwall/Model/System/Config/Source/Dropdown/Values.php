<?php

class Pksr_Socialwall_Model_System_Config_Source_Dropdown_Values
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'key1',
                'label' => 'Value 1',
            ),
            array(
                'value' => 'key2',
                'label' => 'Value 2',
            ),
        );
    }
	
	public function getMaxOptionArray()
    {
        return array(
            'days' => Mage::helper('socialwall')->__('days'),
            'limit' => Mage::helper('socialwall')->__('limit'),
        );
	}
	
	public function getBooleanOptionArray()
    {
        return array(
            'true' => Mage::helper('socialwall')->__('true'),
            'false' => Mage::helper('socialwall')->__('false'),
        );
	}
}