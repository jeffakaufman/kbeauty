<?php
class Productiveminds_Core_Model_System_Config_Source_Typeofissue {
	
    public function toOptionArray()
    {
        $types = array(
            array(
                'value' => false,
                'label' => "Choose a type"
            ),
            array(
                'value' => "NewFeature",
                'label' => "Request a New or Custom Feature"
            )
        );

        $extensions = ( array )Mage::getConfig()->getNode('modules')->children();
        $promindsExtensions = array();

        foreach ($extensions as $key => $value)
            if (strpos($key, 'Productiveminds_', 0) !== false && $key != 'Productiveminds_Core')
                $promindsExtensions [] = array(
                    'value' => $key,
                    'label' => $key
                );
        $types [] = array(
            'value' => $promindsExtensions,
            'label' => 'Productiveminds Extensions'
        );

        return $types;
    }
}