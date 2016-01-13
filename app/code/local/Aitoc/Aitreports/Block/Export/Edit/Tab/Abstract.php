<?php

class Aitoc_Aitreports_Block_Export_Edit_Tab_Abstract extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_config = null;

    public function getConfig()
    {
        if(is_null($this->_config)) {
            $this->_config =  Mage::getSingleton('aitreports/config')->getExportConfig($this->getProfileId());
        }
        return $this->_config;
    }
    
    public function getProfileId() {
        return Mage::registry('current_profile_id');
    }
    
    public function getProfile()
    {
        return Mage::getSingleton('aitreports/config')->getExportProfile($this->getProfileId());
    }
    
    public function getChecked($url, $value = 1, $selectHtml = 'checked="1"')
    {
        $config = $this->getConfig();
        
        $keys = explode("/", $url);
        
        foreach($keys as $key)
        {
            if(isset($config[$key]))
            {
                $config = $config[$key];
            }
        }
        
        if($config == $value)
        {
            return $selectHtml;
        }
        return '';
    }
    
    public function getSelected($url, $value)
    {
        return $this->getChecked($url, $value, 'selected="selected"');
    }

    public function getValue($url, $default='')
    {
        $config = $this->getConfig();
        $keys   = explode("/", $url);
        $value  = '';

        foreach($keys as $key)
        {
            if(isset($config[$key]))
            {
                $config = $config[$key];
                $value = $config;
            }
        }
        
        if (is_array($value))
        { 
            return $default;
        }

        return $this->htmlEscape(strlen($value) > 0 ? $value : $default);
    }

}
