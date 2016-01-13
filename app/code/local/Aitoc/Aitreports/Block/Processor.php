<?php 
class Aitoc_Aitreports_Block_Processor extends Mage_Adminhtml_Block_Template
{
    /**
     * @var Aitoc_Aitreports_Model_Processor_Config
     */
    protected $_config;
    
    /**
     * @var Aitoc_Aitreports_Model_Processor
     */
    protected $_processor;
    
    /**
     * @var Aitoc_Aitreports_Helper_Processor
     */
    protected $_helper;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitreports/processor.phtml');
        
        $this->_config    = Mage::getSingleton('aitreports/processor_config');
        $this->_processor = Mage::getSingleton('aitreports/processor');
        $this->_helper    = Mage::helper('aitreports/processor');
    }
    
    public function getPercent()
    {
        $options = $this->_config->get('options');
        $percent = $this->_helper->calculatePercent($options);
        return $percent;
    }
    
    public function haveActiveProcess()
    {
        return (bool)$this->_config->haveActiveProcess();
    }
    
    public function getProcessName()
    {
        return $this->_helper->getProcessName($this->_config->get('process'));
    }
    
    public function isAjax()
    {
        return (bool)Mage::app()->getRequest()->getParam('isAjax');
    }
}