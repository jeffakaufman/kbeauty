<?php

class Aitoc_Aitreports_Model_Export extends Mage_Core_Model_Abstract
{

    /**
     * @var Varien_Object
     */
    private $_config;
    private $_exportTmpPath;

    /**
     * @var Aitoc_Aitreports_Model_Processor_Response
     */
    protected $_response;
    protected $_prfxPath;

    /**
     * @var Aitoc_Aitreports_Model_Processor_Config
     */
    protected $_processorConfig;

    /**
     * @var array
     */
    protected $_orderAttributes = array('date_from','date_to','order_id_from','order_id_to');

    public function _construct() 
    {
        parent::_construct();

        $this->_init('aitreports/export');
    }

    protected function _initIteration($options)
    {
        if(!isset($options['id']))
        {
            $options['id']=$this->getId();
        }
        $this->load($options['id']);
        $this->_response        = Mage::getSingleton('aitreports/processor_response');
        $this->_processorConfig = Mage::getSingleton('aitreports/processor_config');
        $this->_prfxPath        = Mage::helper('aitreports')->getTmpPath().'report_'.$this->getId();
    }

    /**
     * Generate an xml attribute string from config
     *
     * @param string $key
     *
     * @return string
     */
    protected function _applyAttribute($key)
    {
        $attribute = '';
        $filter = $this->getConfig()->getFilter();
        if(isset($filter[$key]) && $filter[$key] != '') {
            $attribute = ' '.$key.'="'.$filter[$key].'"';
        }
        return $attribute;
    }

    /**
     * Generate a string that will contain order attributes for xml
     *
     * @return string
     */
    protected function _applyAttributes()
    {
        $attribute_text = '';
        foreach($this->_orderAttributes as $key) {
            $attribute_text .= $this->_applyAttribute($key);
        }
        return $attribute_text;
    }

    protected function _prepareXmlExportFile()
    {
        $path = $this->_prfxPath . '.xml';
        $xml = Mage::getModel('aitreports/file_xml', $path);
        if (!file_exists($path)) {
            $xml->write('<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<orders'.$this->_applyAttributes().'>');
        }
    }

    protected function _prepareHeadersXmlExportFile()
    {
        $headersXmlPath = $this->_prfxPath.'_headers.xml';
        $headersXml = new Aitoc_Aitreports_Model_Extendedxml('<?xml version="1.0" encoding="UTF-8"?><order />');
        $headersXml->asXML($headersXmlPath);
    }

    public function initExport($options)
    {
        $this->_initIteration($options);
        //reset option
        $this->_processorConfig->updateOption('from', 0);
        // Calls to create and filter order items
        $orders = $this->getOrders();

        if (0 == count($orders->getItems()))
        {
            // stop iterator with appropriate message
            $this->_response->addMessage('notice', Mage::helper('aitreports')->__('No orders found using current settings. Please change settings in "Order Filters" menu and try again.'))
                ->setSwitch();
            $this->delete();
            return;
        }
        // prepare new export xml file
        $this->_prepareXmlExportFile();

        $newOptions = array(
            'id'       => $this->getId(),
            'from'     => 0,
            'limit'    => 25,
            'entities' => $this->getOrdersCount(),
        );

        // switching processor to export iterations
        $this->_response->setSwitch('export::makeExport', $newOptions);
    }

    public function beforeExport()
    {
        if(!Mage::app()->getStore()->isAdmin())
        {
            return;
        }
        $config = Mage::getSingleton('aitreports/processor_config');
        $session = Mage::getSingleton('adminhtml/session');
        $order_exp_from = $session->getData("order_exp_from");
        if(!is_null($order_exp_from) && $config->getOption('from')>0)
        {
            if($order_exp_from == $config->getOption('from'))
            {
                $session->setData("order_exp_from", NULL);
                $config->resetExport();
                throw new Exception(Mage::helper('aitreports')->__('Export process has been stopped. cycling getting orders'));
            }
        }
        $from = $config->getOption('from');
        $session->setData("order_exp_from", $from);
    }

    /**
     * Works with loaded object
     */
    public function makeExport($options = array())
    {
        $this->_initIteration($options);

        $config = $this->getConfig();

        // Calls to create and filter order items
        if(!isset($options['limit']))
        {
            $options['limit']=25;
        }
        $orders = $this->getOrders($this->_processorConfig->getOption('from'), $options['limit']);
        /* @var $orders Aitoc_Aitreports_Model_Mysql4_Export_Order_Collection */
        if ($this->getOrdersCount() > 0)
        {
            $this->beforeExport();
            $xmlPath = $this->_prfxPath.'.xml';
            $xml = Mage::getModel('aitreports/file_xml', $xmlPath);

            // Fill document in with orders
            $orderExport = Mage::getModel('aitreports/export_type_order');
            $customerOrders = Mage::getModel('aitreports/export_customers');
            $from = $this->_processorConfig->getOption('from');
            if (!$from) {
                $from = isset($options['from']) ? $options['from'] : 0;
            }
            foreach ($orders as $order2) {
                $orderXml = new Aitoc_Aitreports_Model_Extendedxml('<order/>');
                $order = Mage::getModel('sales/order')->load($order2->getOrderId());

                //appying some additional data
                $customerOrders->checkEmail($order);
                $orderExport->prepareXml($orderXml, $order, $config);
                foreach($orderXml->items->item as $row)
                {
                    if($row->qty_refunded>0) {
                        (float)$row->refund_sum_for_xls= (float)$row->base_price - (float)$row->base_discount_amount + (float)$row->base_tax_amount;
                    }
                    else
                    {
                        $row->refund_sum_for_xls = 0;
                    }
                }
                $xml->write($orderXml);
                $from++;
                $this->_processorConfig->updateOption('from', $from);
            }
        }
        if ($this->getOrdersCount() < $options['limit']) {
            $this->_response->setSwitch('export::finalizeXmlExport', array('id' => $this->getId()));
        }
    }

    public function finalizeXmlExport($options)
    {
        $this->_initIteration($options);

        $xmlPath = $this->_prfxPath.'.xml';
        $xml = Mage::getModel('aitreports/file_xml', $xmlPath);

        $xml->write('</orders>');

        if($this->getProfileId())
        {
            $profile = Mage::getSingleton('aitreports/config')->getExportProfile($this->getProfileId());
            if($profile->getXsl()) {
                try
                {
                    $this->applyXsl($xmlPath, $profile);
                }
                catch(Exception $e)
                {
                    $this->_response->addMessage('notice', Mage::helper('aitreports')->__('Xsl modification was not done: ') . $e->getMessage());
                }
            }
        }
        $this->_response->setSwitch('export::finishExport', array('id' => $this->getId()));
    }

    public function finishExport($options)
    {
        $this->_initIteration($options);

        $config = $this->getConfig();

        $path = $this->getTmpFilePath();

        // Transfer ready file depending on config
        switch ($config['file']['type'])
        {
            case 'file':
                $this->fileCopy($path);
                break;

            case 'ftp':
                if ($this->ftpUpload($path))
                {
                    $this->setIsFtpUpload(1)->save();
                }
                break;

            case 'email':
                if ($this->emailSend($path))
                {
                    $this->setIsEmail(1)->save();
                }
                break;

        }
        $this->_processorConfig->updateOption('from', null);
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('aitreports')->__('Export is successfully complete.'));
        $this->_response->setRedirect(Mage::helper('adminhtml')->getUrl('adminhtml/aitreports_index/viewLog', array('id' => $this->getId())));
        $this->_response->setSwitch();
    }

    public function fileCopy($filePath)
    {
        $file   = new Varien_Io_File();
        $config = $this->getConfig();
        if(isset($config['file']['path'])) {
            $localPath = Mage::getBaseDir() . DS . trim($config['file']['path'], ' \\/');
        } else {
            $localPath = Mage::getBaseDir();
        }
        if (!is_dir($localPath))
        {
            $localPath = Mage::getBaseDir().DS.trim($localPath, ' /');
        }

        if (!is_dir($localPath) && !$file->mkdir($localPath))
        {
            throw new Exception(Mage::helper('aitreports')->__('Local export directory %s does not exist or does not have read permissions', $localPath));
        }

        $userDefinedExportPath = $localPath.DS.$this->getFilename();
        $result                = $file->cp($filePath, $userDefinedExportPath);
        if(!$result)
        {
            throw new Exception(Mage::helper('aitreports')->__('File %s hasn\'t been copied from temporaty folder to %s', $filePath, $userDefinedExportPath));
        }
    }


    public function ftpUpload($filePath)
    {
        $config = $this->getConfig();
        $result = true;
        $ftp    = new Varien_Io_Ftp();
        $connectParams = array(
            'host'     => trim($config['ftp']['host']),
            'user'     => trim($config['ftp']['user']),
            'password' => trim($config['ftp']['password']),
            'passive'  => trim($config['ftp']['passive']),
            );

        if(strpos($connectParams['host'],':'))
        {
            list($connectParams['host'],$connectParams['port']) = explode(':',$connectParams['host']);
        }

        try
        {
            $result &= $ftp->open($connectParams);
            $result &= $ftp->cd('/' . trim($config['ftp']['path'], ' /') . '/');
        }
        catch (Exception $e)
        {
            throw new Exception(Mage::helper('aitreports')->__('FTP error: %s', $e->getMessage()));
        }

        if($result == 0)
        {
            throw new Exception(Mage::helper('aitreports')->__('FTP error: invalid ftp folder %s', trim($config['ftp']['path'], ' /')));
        }

        $result &= $ftp->write($this->getFilename(), $filePath);
        if ($result == 0)
        {
            throw new Exception(Mage::helper('aitreports')->__('FTP creation error: cannot write file %s to its folder', $this->getFilename()));
        }

        $ftp->close();

        return true;
    }

    public function emailSend($filePath)
    {
        $config    = $this->getConfig()->getEmail();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        try
        {
            /* var $emailModel Mage_Core_Model_Email_Template */
            $emailModel = Mage::getModel('core/email_template');
            if (!file_exists($filePath))
            {
                return ;
            }
            $fileContents = file_get_contents($filePath); /*(Here put the filename with full path of file, which have to be send)*/

            $emailModel->getMail()->createAttachment($fileContents, 'text/html')->filename = $this->getFilename();

            $emailModel->sendTransactional(
                $config['template'],
                $config['sender'],
                $config['sendto'],
                $config['sendto'],
                array('order_numbers' => $this->_getOrderNumbers())
                );
            $translate->setTranslateInline(true);
        }
        catch (Exception $e)
        {
            $translate->setTranslateInline(true);
            throw new Exception('Email sending error: '.$e->getMessage());
        }

        return true;
    }

    protected function _getOrderNumbers()
    {
        $orderIds     = array();
        $orderNumbers = array();

        foreach($this->getOrders() as $order)
        {
            $orderIds[] = $order->getOrderId();
        }

        $orderCollection = Mage::getModel('sales/order')->getCollection();
        /* @var $orderCollection Mage_Sales_Model_Mysql4_Order_Collection */

        $orderCollection
            ->addFieldToFilter('entity_id', array('in' => $orderIds))
            ->getSelect()->setPart(Zend_Db_Select::COLUMNS, array())->columns(array('entity_id', 'increment_id'));

        foreach ($orderCollection as $order)
        {
            $orderNumbers[] = $order->getIncrementId();
        }

        return join(', ', $orderNumbers);
    }

    /**
     * @return Aitoc_Aitreports_Model_Mysql4_Export_Order_Collection
     */
    public function getOrders($from = 0, $limit = 0)
    {
        $order           = Mage::getModel('aitreports/export_order');
        $orderCollection = $order->getCollection();

        // export init only
        if (0 == $this->getOrdersCount())
        {
            $order->assignOrders($this);
        }

        $orderCollection->addFieldToFilter('export_id', $this->getId());

        // for iterative processes only
        if($limit)
        {
            $orderCollection->getSelect()->limit($limit, $from);
        }

        $orderCollection->load();
        $size = count($orderCollection->getItems());
        $this->setOrdersCount($size)->save();

        return $orderCollection;
    }

    /** Gets export config as an object
     *
     * @return Varien_Object
     */
    public function getConfig()
    {
        if (null === $this->_config)
        {
            $this->_config = new Varien_Object(unserialize($this->getSerializedConfig()));
        }

        return $this->_config;
    }

    /** Handles file uploading
     *
     * @return filename
     */
    public function handleUpload($field)
    {
        if(isset($_FILES[$field]['name']) and (file_exists($_FILES[$field]['tmp_name'])))
        {
            $uploader = new Varien_File_Uploader($field);
            $uploader->setAllowedExtensions(array('xsl'));
            $uploader->setAllowRenameFiles(false);
            $uploader->setFilesDispersion(false);
            $path = Mage::helper('aitreports')->getTmpPath();
            $uploader->save($path, $_FILES[$field]['name']);
            return $_FILES[$field]['name'];
        }
    }

    public function validateXsl($filename)
    {
        $xp = new XSLTProcessor();

        // create a DOM document and load the XSL stylesheet
        $xsl = new DOMDocument();
        $path = Mage::helper('aitreports')->getTmpPath() . $filename;

        if (!$xsl->load($path))
        {
            throw new Exception(Mage::helper('aitreports')->__('Invalid xsl format or file %s has incorrect name', $path));
        }

        // import the XSL styelsheet into the XSLT process
        $xp->importStylesheet($xsl);

        // create a DOM document and load the XML data
        $xmlDoc = new DomDocument();
        $xmlDoc->loadXML('<orders />');

        // transform the XML into HTML using the XSL file
        if (!$xp->transformToXML($xmlDoc))
        {
            throw new Exception('XSL transformation failed'.print_r(libxml_get_last_error(), 1));
        }
    }

    public function applyXsl($path, $profile)
    {
        $xslString = $profile->getXsl();

        //validate the beginning of the xsl string
        if($startPos = strpos($xslString, '<'))
            $xslString = substr($xslString, $startPos);

        $xp = new XsltProcessor();

        // create a DOM document and load the XSL stylesheet
        $xsl = new DOMDocument();
        if (!$xsl->loadXml($xslString))
        {
            throw new Exception(Mage::helper('aitreports')->__('Invalid xsl format'));
        }

        // import the XSL styelsheet into the XSLT process
        $xp->importStylesheet($xsl);

        // create a DOM document and load the XML data
        $xmlDoc = new DomDocument();
        $xmlDoc->load($path);

        //if($modifiedXml = new SimpleXMLElement($xp->transformToXML($xmlDoc)))
        if($modifiedCode = $xp->transformToXML($xmlDoc))
        {
            unlink($path);
            file_put_contents($path, $modifiedCode);
        }
        else
        {
            throw new Exception('XSL transformation failed');
        }
    }

    /**
     *
     * @param array $config
     * @return Aitoc_Aitreports_Model_Export
     */
    public function setConfig(array $config)
    {
        $this->setSerializedConfig(serialize($config));
        if(!$this->getId()) {
            $this->save();
        }
        $filename  = !empty($config['file']['filename']) ? $config['file']['filename'] : 'report';
        $filename .= '_'.$this->getId().'_'.date('YmdHis', Mage::getModel('core/date')->timestamp()).'.html';

        $this->setFilename($filename)
             ->setDt(now());

        return $this;
    }

    public function getTmpFilePath()
    {
        //$parse = $this->getConfig()->getParse();
        $type  = 'xml';//isset($parse['type']) ? $parse['type'] : 'xml';

        return $this->_prfxPath.'.'.$type;
    }

    public function prepareOrderCollection(Varien_Data_Collection_Db $collection)
    {
    	if(version_compare(Mage::getVersion(),'1.4.1.1','ge'))
        {
        	$this->getResource()->prepareOrderCollection($this, $collection);
        }

        return $this;
    }

    protected function _beforeDelete()
    {
        $this->_exportTmpPath = $this->getTmpFilePath();

        return parent::_beforeDelete();
    }

    protected function _afterDelete()
    {
        if (file_exists($this->_exportTmpPath))
        {
            unlink($this->_exportTmpPath);
        }

        return parent::_afterDelete();
    }

    public function getDbTime()
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT now()';

        return $currentTime = $readConnection->fetchOne($query);
    }
}