<?php
class Aitoc_Aitreports_Model_Processor_Direct extends Aitoc_Aitreports_Model_Processor
{
    /**
    * This class is small update for our process system to divide checkout/invoice exports from cron, because cron may block exewcuting profiles on this events. 
    * Most probably this will require refactoring whole process system.
    */
    protected $_configModel = 'aitreports/processor_direct_config';    
    
    /**
    * Direct process can't be blocked by configuration
    * 
    */
    public function isBusy()
    {
        return false;
    }
    
    
}