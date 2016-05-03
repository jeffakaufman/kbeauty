<?php

class Valkyrie_PromotionModules_Adminhtml_PromotionModulesController extends Mage_Adminhtml_Controller_Action
{
  const PROMOTION_MODULES_IMAGES_DIR = 'promotionmodules_images';

  public function indexAction()
  {
    //  echo "<h1>PROMOTION MODULES ADMIN</h1>";return;
    $this->loadLayout();

      $this->_setActiveMenu('promotion_modules');

      $contentBlock = $this->getLayout()->createBlock('promotionmodules/adminhtml_promotionmodules');
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
      $model = Mage::getModel('promotionmodules/promotionmodules');
//var_dump($model);die();
    if ($id) {
      $model->load((int) $id);
      if ($model->getId()) {
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if ($data) {
          $model->setData($data)->setId($id);
        }
      } else {
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('promotionmodules')->__('Module does not exist'));
        $this->_redirect('*/*/');
      }
    }
    Mage::register('promotionmodules', $model);

    $this->loadLayout();

      $this->_setActiveMenu('promotion_modules');

      $contentBlock = $this->getLayout()->createBlock('promotionmodules/adminhtml_promotionmodules_edit');
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

//echo "<pre>";var_dump($_FILES);die();
    if ($data = $this->getRequest()->getPost())
    {
        if(isset($_FILES['desktop_image']['name']) && (file_exists($_FILES['desktop_image']['tmp_name']))) {
            try {

                $uploader = new Varien_File_Uploader('desktop_image');

                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything

                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);

                $path = Mage::getBaseDir('media') . DS . self::PROMOTION_MODULES_IMAGES_DIR. DS . 'desktop';

                $uploader->save($path, $_FILES['desktop_image']['name']);

                $data['desktop_image'] = self::PROMOTION_MODULES_IMAGES_DIR .'/desktop/'. $_FILES['desktop_image']['name'];
//var_dump($data);
            } catch(Exception $e) {

//echo "<pre>";
//var_dump($path);
//                var_dump($e);
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

                $path = Mage::getBaseDir('media') . DS . self::PROMOTION_MODULES_IMAGES_DIR. DS . 'mobile';

                $uploader->save($path, $_FILES['mobile_image']['name']);

                $data['mobile_image'] = self::PROMOTION_MODULES_IMAGES_DIR .'/mobile/'. $_FILES['mobile_image']['name'];

            } catch(Exception $e) {

            }
        } else {

            if(isset($data['mobile_image']['delete']) && $data['mobile_image']['delete'] == 1)
                $data['mobile_image'] = '';
            else
                unset($data['mobile_image']);
        }
//var_dump($data);
        if(!$data['active']) {
            $data['active'] = '0';
        } else {
            $data['active'] = '1';
        }

//die();
      $model = Mage::getModel('promotionmodules/promotionmodules');
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
//var_dump($data);die();


      Mage::getSingleton('adminhtml/session')->setFormData($data);
      try {
        if ($id) {
          $model->setId($id);
        }
        $model->save();

        if (!$model->getId()) {
          Mage::throwException(Mage::helper('promotionmodules')->__('Error saving module'));
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('promotionmodules')->__('Module was successfully saved.'));
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
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('promotionmodules')->__('No data found to save'));
    $this->_redirect('*/*/');
  }

  public function deleteAction()
  {
    if ($id = $this->getRequest()->getParam('id')) {
      try {
        $model = Mage::getModel('promotionmodules/promotionmodules');
        $model->setId($id);
        $model->delete();
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('promotionmodules')->__('The module has been deleted.'));
        $this->_redirect('*/*/');
        return;
      }
      catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        return;
      }
    }
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the module to delete.'));
    $this->_redirect('*/*/');
  }

}