<?php
class Aitoc_Aitreports_Adminhtml_Aitreports_IndexController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('reports/aitreports')
            ->_addBreadcrumb(Mage::helper('aitreports')->__('Smart Reports Export'), Mage::helper('aitreports')->__('Smart Reports Export'));
        Mage::getSingleton('adminhtml/session')->setData("order_exp_from", null);
        return $this;
    }

    /**
     * Show export configuration here
     *
     */
    public function indexAction()
    {
        if(Mage::helper('core')->isModuleEnabled('Aitoc_Common')) {
            Mage::getSingleton('aitoc_common/cron')->validateCronDate()->showError();
        }

        $this->_initAction();
        $profileId    = (int)$this->getRequest()->getParam('profile');
        Mage::register('current_profile_id', $profileId);

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('aitreports/export_edit'))
             #->_addLeft($this->getLayout()->createBlock('adminhtml/store_switcher')->setTemplate('store/switcher.phtml'))
             ->_addLeft($this->getLayout()->createBlock('aitreports/export_edit_tabs'));

        $this->renderLayout();
    }

    /**
     * Perform export here and redirect to index
     *
     */
    public function exportAction()
    {
        echo 'exportAction';
    }

    public function saveAction()
    {
        $post         = $this->getRequest()->getPost();
        $storeId      = (int)$this->getRequest()->getParam('store');
        $saveErrors   = false;
        $session      = Mage::getSingleton('adminhtml/session');
        /* @var $session Mage_Adminhtml_Model_Session */
        $helper       = Mage::helper('aitreports');
        /* @var $helper Aitoc_Aitreports_Helper_Data */

        $redirectParams = array();

        $profile   = $this->getRequest()->getParam('profile');
        $profile_id = 0;
        if(is_array($profile) && isset($profile['id'])) {
            $profile_id = (int)$profile['id'];
            if($profile_id > 0) {
                $redirectParams['profile'] = $profile_id;
                Mage::register('current_profile_id', $profile_id);
            }
        }

        if (!$this->getRequest()->isPost())
        {
            $this->_redirect('*/*/', $redirectParams);
        }

        try
        {
            if (isset($_FILES['parse_xsl_file']['name']) && file_exists($_FILES['parse_xsl_file']['tmp_name']))
            {
                $filename = Mage::getModel('aitreports/export')->handleUpload('parse_xsl_file');
                $path     = Mage::helper('aitreports')->getTmpPath() . $filename;
                Mage::getModel('aitreports/export')->validateXsl($filename);
                if (!$xsl = file_get_contents($path))
                {
                    throw new Exception(Mage::helper('aitreports')->__('File %s not found', $path));
                }

                Mage::getSingleton('aitreports/config')->saveXsl($xsl);
                unlink($path);
                $post['parse_xsl_file']['name'] = $filename;
            } else {
                if($profile_id==0 || Mage::getSingleton('aitreports/config')->getExportProfile($profile_id)->getXsl()=='') {
                    throw new Exception(Mage::helper('aitreports')->__('File were not uploaded', $path));
                } else {
                    //xsl file is uploaded and saved in profile, should be ok
                }
            }
        }
        catch (Exception $e)
        {
            $saveErrors = true;
            $session->addError($helper->__('Xsl file was not loaded: ').$e->getMessage());
        }

        if(isset($post['filter']))
        {
            if(isset($post['filter']['date_from'])){
                $post['filter']['date_from'] = Mage::helper('aitreports/date')->convertDateToCorrectFormat($post['filter']['date_from']);
            }

            if(isset($post['filter']['date_to'])){
                $post['filter']['date_to'] = Mage::helper('aitreports/date')->convertDateToCorrectFormat($post['filter']['date_to']);
            }

            foreach($post['filter'] as $key=>$value) {
                $post['filter'][$key] = trim($value);
            }

            if (!empty($post['filter']['order_id_from']))
            {
                $order = Mage::getModel('sales/order')->loadByIncrementId($post['filter']['order_id_from']);

                if (!$order->getId())
                {
                    $saveErrors = true;
                    $session->addError($helper->__('Order # From field contains unknown order %s. Please, correct Order # or leave empty.', $post['filter']['order_id_from']));
                }
            }

            if (!empty($post['filter']['order_id_to']))
            {
                $order = Mage::getModel('sales/order')->loadByIncrementId($post['filter']['order_id_to']);

                if (!$order->getId())
                {
                    $saveErrors = true;
                    $session->addError($helper->__('Order # To field contains unknown order %s. Please, correct Order # or leave empty.', $post['filter']['order_id_to']));
                }
            }
        }
        if(isset($post['file']))
        {
            if($post['file']['type'] == 'file')
            {
                $file      = new Varien_Io_File();
                $localPath = isset($post['file']['path']) ? $post['file']['path'] : Mage::getBaseDir();
                if (!is_dir($localPath))
                {
                    $localPath = Mage::getBaseDir().DS.trim($localPath, ' /');
                }

                if (!is_dir($localPath) && !$file->mkdir($localPath))
                {
                    $session->addError($helper->__('Local export directory %s does not exist or does not have read permissions', $localPath));
                    $saveErrors = true;
                }
                elseif(!$file->isWriteable($localPath))
                {
                    $session->addError($helper->__('Local export directory %s does not exist or does not have read permissions', $localPath));
                    $saveErrors = true;
                }
            }
            elseif($post['file']['type'] == 'ftp')
            {
                try
                {
                    $result = true;
                    $ftp    = new Varien_Io_Ftp();
                    $connectParams = array(
                        'host'     => trim($post['ftp']['host']),
                        'user'     => trim($post['ftp']['user']),
                        'password' => trim($post['ftp']['password']),
                        'passive'  => trim($post['ftp']['passive']),
                        );
					if(strpos($connectParams['host'],':'))
					{
						list($connectParams['host'],$connectParams['port']) = explode(':',$connectParams['host']);
					}
                    try
                    {
                        $result &= $ftp->open($connectParams);
                        $result &= $ftp->cd('/' . trim($post['ftp']['path'], ' /') . '/');
                    }
                    catch (Exception $e)
                    {
                        throw new Exception($helper->__('FTP error: %s', $e->getMessage()));
                    }

                    if($result == 0)
                    {
                        throw new Exception($helper->__('FTP error: invalid ftp folder %s', trim($post['ftp']['path'], ' /')));
                    }

                    $result &= $ftp->write('temp.txt', 'ftpTest');
                    if ($result == 0)
                    {
                        throw new Exception($helper->__('FTP export folder %s does not have write permissions', trim($post['ftp']['path'], ' /')));
                    }

                    $ftp->rm('temp.txt');

                    $ftp->close();
                }
                catch(Exception $e)
                {
                    $session->addError($e->getMessage());
                    $saveErrors = true;
                }
            }
        }

        if ($saveErrors)
        {
            if ('export' == $this->getRequest()->getParam('redirect'))
            {
                $session->addError($helper->__('Orders were not exported.'));
            }

            return $this->_redirect('*/*/');
        }

        $profile_id = Mage::getSingleton('aitreports/config')
            ->saveExportConfig($post, $storeId, $profile_id)
            ->loadProfileId($profile_id);

        if($profile_id != 0 && isset($post['clear_queue'])) {
            Mage::getresourceModel('aitreports/export_order')->truncateByProfileId( $profile_id );
        }

        if ('export' != $this->getRequest()->getParam('redirect'))
        {
            $session->addSuccess($helper->__('Configuration has been saved'));
        }
        else
        {
            if($profile_id == 0) {
                $session->addError($helper->__('Profile is not saved. Can\'t initialize export process'));
                return $this->_redirect('*/*/');
            }

            try
            {
                $processor = Mage::getSingleton('aitreports/processor');

                if(!$processor->getProcess())
                {
                    // Get current export config
                    $globalConfig = Mage::getSingleton('aitreports/config')->getExportConfig($profile_id);

                    $globalConfig['auto']['bind_order_id'] = false;

                    $exportModel = Mage::getModel('aitreports/export')
                        ->setConfig($globalConfig)
                        ->setStoreId( $storeId )
                        ->setProfileId( $profile_id )
                        ->save();

                    $options = array(
                        'id' => $exportModel->getId()
                    );

                    $processor->setProcess('export::initExport', $options)->save();
                    Mage::getSingleton('adminhtml/session')->addNotice($helper->__('Export process started'));
                    $redirectParams['active_tab'] = 'processor';
                    return $this->_redirect('*/*/', $redirectParams);
                }
                else
                {
                    Mage::getSingleton('adminhtml/session')->addError($helper->__('Sorry, another export is already running. You can stop it at the \'Processor\' tab.'));
                }
            }
            catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        return $this->_redirect('*/*/', $redirectParams);
    }


    public function cancelAction()
    {
        $config = Mage::getSingleton('aitreports/processor_config');
        $config->resetExport();
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('aitreports')->__('Export process has been canceled'));
        return $this->_redirect('*/*/');

    }

    public function downloadAction()
    {
        $exportId   = $this->getRequest()->getParam('id');
        $export     = Mage::getModel('aitreports/export')->load($exportId);
        $fileConfig = $export->getConfig()->getFile();
        if (trim($fileConfig['path']) == "")
        {
            $fileConfig['path'] = Mage::getBaseDir('var').'/aitreports';
        }

        $filename   = trim($fileConfig['path']).'/'.$export->getFilename();

        if (!file_exists($filename))
        {
            $filename   = Mage::getBaseDir('var').DS.'smartreports'.DS.'report_'.$export->getId().'.html';
            $fileConfig = trim($fileConfig['path']).'/'.$export->getFilename();
            try
            {
                rename($filename, $fileConfig);
                $filename = $fileConfig;
            }
            catch(Exception $e) {
                //on widnows environments bufer may be empty here
            }
        }
        if ($export->getId() && file_exists($filename))
        {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.$export->getFilename());
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: '.filesize($filename));
            try {
                ob_clean();
            } catch(Exception $e) {
                //on widnows environments bufer may be empty here
            }
            flush();
            readfile($filename);

            exit;
        }

    }

    public function historyGridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('aitreports/export_edit_tab_history')->toHtml());
    }

    /** View Export log AJAX navigation
     */
    public function viewLogGridAction()
    {
        $exportId      = $this->getRequest()->getParam('id');
        $currentExport = Mage::getModel('aitreports/export')->load($exportId);

        if (!$currentExport->getId())
        {
            return $this->_redirect('*/*/index');
        }

        Mage::register('current_export', $currentExport);

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('aitreports/export_log_grid')->toHtml());
    }

    public function viewLogAction()
    {
        $exportId      = $this->getRequest()->getParam('id');
        $currentExport = Mage::getModel('aitreports/export')->load($exportId);

        if (!$currentExport->getId())
        {
            return $this->_redirect('*/*/index');
        }

        Mage::register('current_export', $currentExport);

        $this->_initAction();

        $this->_addContent($this->getLayout()->createBlock('aitreports/export_log'));

        $this->renderLayout();
    }

    public function massDeleteAction()
    {
        $exportIds = $this->getRequest()->getParam('export_ids');
        if(!is_array($exportIds))
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('aitreports')->__('Please select item(s)'));
        }
        else
        {
            try
            {
                foreach ($exportIds as $exportId)
                {
                    Mage::getModel('aitreports/export')->load($exportId)->delete();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('aitreports')->__('Total of %d record(s) were successfully deleted', count($exportIds)));
            }
            catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index', array('active_tab' => 'history'));
    }

    public function deleteAction()
    {
        $exportId = $this->getRequest()->getParam('id');
        try
        {
            Mage::getModel('aitreports/export')->load($exportId)->delete();

            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('aitreports')->__('Item has been deleted successfully'));
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/index', array('active_tab' => 'history'));
    }

    public function deleteProfileAction()
    {
        $profileId = $this->getRequest()->getParam('profile');
        try
        {
            Mage::getModel('aitreports/profile')->load($profileId)->delete();

            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('aitreports')->__('Profile has been deleted successfully'));
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/index');
    }
}
