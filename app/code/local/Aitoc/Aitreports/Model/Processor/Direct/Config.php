<?php
class Aitoc_Aitreports_Model_Processor_Direct_Config extends Aitoc_Aitreports_Model_Processor_Config
{
    protected $_config_path = 'sales/aitreports/direct_iterator';
    
    /**
     * Init resource table and prevend loading default config
     */
    protected function _construct()
    {
        $this->_init('aitreports/processor_direct_config');
        #$this->load($this->_config_path, 'path');
    }
    
    /**
    * Prevent saving this model
    * 
    */
    public function save() {
        return $this;
    }
	
}
