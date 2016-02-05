<?php

class Valkyrie_SliderData_Adminhtml_SliderDataController extends Mage_Adminhtml_Controller_Action
{
  const SLIDER_DATA_IMAGES_DIR = 'sliderdata_images';

  public function indexAction()
  {
    //  echo "<h1>SLIDERS ADMIN</h1>";return;
    $this->loadLayout();

      $this->_setActiveMenu('slider_data');

      $contentBlock = $this->getLayout()->createBlock('sliderdata/adminhtml_sliderdata');
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
      $model = Mage::getModel('sliderdata/sliderdata');
//var_dump($model);die();
    if ($id) {
      $model->load((int) $id);
      if ($model->getId()) {
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if ($data) {
          $model->setData($data)->setId($id);
        }
      } else {
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sliderdata')->__('Slide does not exist'));
        $this->_redirect('*/*/');
      }
    }
    Mage::register('sliderdata', $model);

    $this->loadLayout();

      $this->_setActiveMenu('slider_data');

      $contentBlock = $this->getLayout()->createBlock('sliderdata/adminhtml_sliderdata_edit');
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

                $path = Mage::getBaseDir('media') . DS . self::SLIDER_DATA_IMAGES_DIR. DS . 'desktop';

                $uploader->save($path, $_FILES['desktop_image']['name']);

                $data['desktop_image'] = self::SLIDER_DATA_IMAGES_DIR .'/desktop/'. $_FILES['desktop_image']['name'];
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
//var_dump($data);
        if(!$data['active']) {
            $data['active'] = '0';
        } else {
            $data['active'] = '1';
        }

//die();
      $model = Mage::getModel('sliderdata/sliderdata');
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
          Mage::throwException(Mage::helper('sliderdata')->__('Error saving slide'));
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sliderdata')->__('Slide was successfully saved.'));
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
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sliderdata')->__('No data found to save'));
    $this->_redirect('*/*/');
  }

  public function deleteAction()
  {
    if ($id = $this->getRequest()->getParam('id')) {
      try {
        $model = Mage::getModel('sliderdata/sliderdata');
        $model->setId($id);
        $model->delete();
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sliderdata')->__('The slide has been deleted.'));
        $this->_redirect('*/*/');
        return;
      }
      catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        return;
      }
    }
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the slide to delete.'));
    $this->_redirect('*/*/');
  }

}
