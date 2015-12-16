<?php

class Productiveminds_Sitesecurity_Model_System_Config_Source_Statusfield
{
    
    static public function toOptionArray() {
    	return self::getOptionArray();
    }
    
    static public function getOptionArray() {
    	return array(
    			array(
    					'value'     => Productiveminds_Sitesecurity_Model_System_Config_Source_Status::ALLOWED,
    					'label'     => Productiveminds_Sitesecurity_Model_System_Config_Source_Status::ALLOWED_TEXT
    			),
    			array(
    					'value'     => Productiveminds_Sitesecurity_Model_System_Config_Source_Status::DISALLOWED,
    					'label'     => Productiveminds_Sitesecurity_Model_System_Config_Source_Status::DISALLOWED_TEXT
    			)
    	);
    }
    
    
    static public function toOptionEnableArray() {
    	return self::getOptionEnableArray();
    }
    
    static public function getOptionEnableArray() {
    	return array(
    			array(
    					'value'     => Productiveminds_Sitesecurity_Model_System_Config_Source_Status::ENABLED,
    					'label'     => Productiveminds_Sitesecurity_Model_System_Config_Source_Status::ENABLED_TEXT
    			),
    			array(
    					'value'     => Productiveminds_Sitesecurity_Model_System_Config_Source_Status::DISABLED,
    					'label'     => Productiveminds_Sitesecurity_Model_System_Config_Source_Status::DISABLED_TEXT
    			)
    	);
    }
}
