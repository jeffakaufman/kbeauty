<?php

class Valkyrie_Press_Adminhtml_PressController extends Mage_Adminhtml_Controller_Action
{


//TODO:: Write Correct Actions

    const PRESS_DATA_IMAGES_DIR = 'press_images';

    public function exportAction() {

        $collection = Mage::getModel('press/press')->getCollection();
        $collection->setOrder('sort_order', 'asc');

        $exportObject = new stdClass();
        $exportObject->press = array();

        foreach($collection as $p) {
            /**
             * @var Valkyrie_Press_Model_Press $p
             */
            $d = $p->getData();
            unset($d['press_id']);
            unset($d['sort_order']);

            $exportObject->press[] = (object)$d;
        }

        $exportData = json_encode($exportObject, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES /*| JSON_UNESCAPED_UNICODE*/ );
        return $this->_prepareFileDownload(array('press.json' => $exportData));

//        $exportJson = json_encode($exportObject);

//        echo "<pre>";
//        var_dump(json_encode($exportObject));
//        die("YEAH");
    }

  public function indexAction()
  {
    //  echo "<h1>SLIDERS ADMIN</h1>";return;
    $this->loadLayout();

      $this->_setActiveMenu('press');

      $contentBlock = $this->getLayout()->createBlock('press/adminhtml_press');
      $this->_addContent($contentBlock);

    $this->renderLayout();
  }

  public function newAction()
  {
    $this->_forward('edit');
  }

  public function editAction()
  {
      $id = $this->getRequest()->getParam('id', null);
      $model = Mage::getModel('press/press');
//var_dump($model);die();
    if ($id) {
      $model->load((int) $id);
      if ($model->getId()) {
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if ($data) {
          $model->setData($data)->setId($id);
        }
      } else {
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('press')->__('Item does not exist'));
        $this->_redirect('*/*/');
      }
    }
    Mage::register('press', $model);

    $this->loadLayout();

      $this->_setActiveMenu('press');

      $contentBlock = $this->getLayout()->createBlock('press/adminhtml_press_edit');
      $this->_addContent($contentBlock);


      $this->getLayout()->getBlock('head')
      ->setCanLoadExtJs(true)
      ->setCanLoadTinyMce(true)
      ->addItem('js','tiny_mce/tiny_mce.js')
      ->addItem('js','mage/adminhtml/wysiwyg/tiny_mce/setup.js')
      ->addJs('mage/adminhtml/browser.js')
      ->addJs('prototype/window.js')
      ->addJs('lib/flex.js')
      ->addJs('mage/adminhtml/flexuploader.js')
      ->addItem('js_css','prototype/windows/themes/default.css')
      ->addItem('js_css','prototype/windows/themes/magento.css');
    $this->renderLayout();
  }

  public function saveAction()
  {
    if ($data = $this->getRequest()->getPost())
    {
        /*
        if(isset($_FILES['desktop_image']['name']) && (file_exists($_FILES['desktop_image']['tmp_name']))) {
            try {

                $uploader = new Varien_File_Uploader('desktop_image');

                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything

                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);

                $path = Mage::getBaseDir('media') . DS . self::SLIDER_DATA_IMAGES_DIR. DS . 'desktop';

                $uploader->save($path, $_FILES['desktop_image']['name']);

                $data['desktop_image'] = self::SLIDER_DATA_IMAGES_DIR .'/desktop/'. $_FILES['desktop_image']['name'];

            } catch(Exception $e) {

            }
        } else {

            if(isset($data['desktop_image']['delete']) && $data['desktop_image']['delete'] == 1)
                $data['desktop_image'] = '';
            else
                unset($data['desktop_image']);
        }

        if(isset($_FILES['mobile_image']['name']) && (file_exists($_FILES['mobile_image']['tmp_name']))) {
            try {

                $uploader = new Varien_File_Uploader('mobile_image');

                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything

                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);

                $path = Mage::getBaseDir('media') . DS . self::SLIDER_DATA_IMAGES_DIR. DS . 'mobile';

                $uploader->save($path, $_FILES['mobile_image']['name']);

                $data['mobile_image'] = self::SLIDER_DATA_IMAGES_DIR .'/mobile/'. $_FILES['mobile_image']['name'];

            } catch(Exception $e) {

            }
        } else {

            if(isset($data['mobile_image']['delete']) && $data['mobile_image']['delete'] == 1)
                $data['mobile_image'] = '';
            else
                unset($data['mobile_image']);
        }
*/

      $model = Mage::getModel('press/press');
      $id = $this->getRequest()->getParam('id');
      if ($id) {
        $model->load($id);
      }

      foreach ($data as $key => $value)
      {
          if (is_array($value))
          {
              $data[$key] = implode(',',$this->getRequest()->getParam($key));
          }
      }

      $model->setData($data);

      Mage::getSingleton('adminhtml/session')->setFormData($data);
      try {
        if ($id) {
          $model->setId($id);
        }
        $model->save();

        if (!$model->getId()) {
          Mage::throwException(Mage::helper('press')->__('Error saving item'));
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('press')->__('Press Item was successfully saved.'));
        Mage::getSingleton('adminhtml/session')->setFormData(false);

        // The following line decides if it is a "save" or "save and continue"
        if ($this->getRequest()->getParam('back')) {
          $this->_redirect('*/*/edit', array('id' => $model->getId()));
        } else {
          $this->_redirect('*/*/');
        }

      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        if ($model && $model->getId()) {
          $this->_redirect('*/*/edit', array('id' => $model->getId()));
        } else {
          $this->_redirect('*/*/');
        }
      }

      return;
    }
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('press')->__('No data found to save'));
    $this->_redirect('*/*/');
  }

  public function deleteAction()
  {
    if ($id = $this->getRequest()->getParam('id')) {
      try {
        $model = Mage::getModel('press/press');
        $model->setId($id);
        $model->delete();
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('press')->__('The item has been deleted.'));
        $this->_redirect('*/*/');
        return;
      }
      catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        return;
      }
    }
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the item to delete.'));
    $this->_redirect('*/*/');
  }




    /**
     * Serve files to browser - one file can be served directly, multiple files must be served as a ZIP file.
     */
    protected function _prepareFileDownload($fileArray)
    {
        if (count($fileArray) > 1) {
            // We need to zip multiple files and return a ZIP file to browser
            if (!@class_exists('ZipArchive') && !function_exists('gzopen')) {
                $this->_getSession()->addError(Mage::helper('xtento_orderexport')->__('PHP ZIP extension not found. Please download files manually from the server, or install the ZIP extension, or export just one file with each profile.'));
                return $this->_redirectReferer();
            }
            // ZIP creation
            $zipFile = false;
            if (@class_exists('ZipArchive')) {
                // Try creating it using the PHP ZIP functions
                $zipArchive = new ZipArchive();
                $zipFile = tempnam(sys_get_temp_dir(), 'zip');
                if ($zipArchive->open($zipFile, ZIPARCHIVE::CREATE) !== TRUE) {
                    $this->_getSession()->addError(Mage::helper('xtento_orderexport')->__('Could not open file ' . $zipFile . '. ZIP creation failed.'));
                    return $this->_redirectReferer();
                }
                foreach ($fileArray as $filename => $content) {
                    $zipArchive->addFromString($filename, $content);
                }
                $zipArchive->close();
            } else if (function_exists('gzopen')) {
                // Try creating it using the PclZip class
                require_once(Mage::getModuleDir('', 'Xtento_OrderExport') . DS . 'lib' . DS . 'PclZip.php');
                $zipFile = tempnam(sys_get_temp_dir(), 'zip');
                $zipArchive = new PclZip($zipFile);
                if (!$zipArchive) {
                    $this->_getSession()->addError(Mage::helper('xtento_orderexport')->__('Could not open file ' . $zipFile . '. ZIP creation failed.'));
                    return $this->_redirectReferer();
                }
                foreach ($fileArray as $filename => $content) {
                    $zipArchive->add(array(
                        array(
                            PCLZIP_ATT_FILE_NAME => $filename,
                            PCLZIP_ATT_FILE_CONTENT => $content
                        )
                    ));
                }
            }
            if (!$zipFile) {
                $this->_getSession()->addError(Mage::helper('xtento_orderexport')->__('ZIP file couldn\'t be created.'));
                return $this->_redirectReferer();
            }
            $this->_prepareDownloadResponse("export_" . time() . ".zip", file_get_contents($zipFile));
            @unlink($zipFile);
            return $this;
        } else {
            // Just one file, output to browser
            foreach ($fileArray as $filename => $content) {
                return $this->_prepareDownloadResponse($filename, $content);
            }
        }
    }

}
