<?php

class Productiveminds_Sitesecurity_Model_System_Config_Source_Status
{
    const ALLOWED = 1;
    const DISALLOWED = 2;
    
    const ALLOWED_TEXT = 'Allowed';
    const DISALLOWED_TEXT = 'Disallowed';
    
    
    const ENABLED = 1;
    const DISABLED = 2;
    
    const ENABLED_TEXT = 'Active';
    const DISABLED_TEXT = 'Inactive';
    
    static public function toOptionArray() {
    	return self::getOptionArray();
    }
    static public function getOptionArray() {
    	return array(
    			self::ALLOWED => self::ALLOWED_TEXT,
    			self::DISALLOWED => self::DISALLOWED_TEXT
    	);
    }
    
    static public function toOptionEnableArray() {
    	return self::getOptionEnableArray();
    }
    static public function getOptionEnableArray() {
    	return array(
    			self::ENABLED => self::ENABLED_TEXT,
    			self::DISABLED => self::DISABLED_TEXT
    	);
    }
}
