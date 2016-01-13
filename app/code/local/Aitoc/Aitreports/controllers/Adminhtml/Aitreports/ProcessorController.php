<?php
class Aitoc_Aitreports_Adminhtml_Aitreports_ProcessorController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('reports/aitreports')
			->_addBreadcrumb(Mage::helper('aitreports')->__('Smart Reports Processor'), Mage::helper('aitreports')->__('Smart Reports Processor'));
		
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
	
		return $this;
	}
	
    public function indexAction()
    {
        /*
        $processor = Mage::getSingleton('aitreports/processor');
        $processor->setProcess('export::makeExport');
        $this->_forward('run');
        */
        /*
    	$this->_initAction();
    	$this->renderLayout();
    	*/
        $this->_forward('run');
    }
    
    public function runAction()
    {
        $processor = Mage::getSingleton('aitreports/processor');
        if($processor->isAjax())
        {
            $processor->run();
            $response = Mage::getSingleton('aitreports/processor_response');
            $config = Mage::getSingleton('aitreports/processor_config');
            
            $block = $this->getLayout()->createBlock('aitreports/processor')->toHtml();
            
            $result = array(
                'block'            => $block,
                'continueProcess'  => (bool)$processor->getProcess(),
                'messages'         => $response ->getMessages(),
                'limit'            => $config->get('limit', 0),
                'redirect'         => $response->getRedirect(),
            );
            
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }
}
