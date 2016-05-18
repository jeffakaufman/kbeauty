<?php

class Valkyrie_PromotionModules_Block_Adminhtml_PromotionModules_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

  protected function _prepareForm()
  {
    $imagesDir = 'media/'.Valkyrie_PromotionModules_Adminhtml_PromotionModulesController::PROMOTION_MODULES_IMAGES_DIR.'/';
//var_dump(Mage::getSingleton('adminhtml/session'));
    if (Mage::getSingleton('adminhtml/session')->getPromotionModulesData())
    {
      $data = Mage::getSingleton('adminhtml/session')->getPromotionModulesData();
      Mage::getSingleton('adminhtml/session')->getPromotionModulesData(null);
    }
    elseif (Mage::registry('promotionmodules'))
    {
      $data = Mage::registry('promotionmodules')->getData();
    }
    else
    {
      $data = array();
    }

    $form = new Varien_Data_Form(array(
      'id' => 'edit_form',
      'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
      'method' => 'post',
      'enctype' => 'multipart/form-data',
    ));

    $form->setUseContainer(true);

    $this->setForm($form);


    $fieldset = $form->addFieldset('promotionmodules_form', array(
      'legend' =>Mage::helper('promotionmodules')->__('Module Information')
    ));

    $fieldset->addField('title', 'text', array(
      'label'     => Mage::helper('promotionmodules')->__('Super Header'),
      'class'     => 'required-entry',
      'required'  => true,
      'name'      => 'title',
    ));

    $fieldset->addField('sub_title', 'text', array(
      'label'     => Mage::helper('promotionmodules')->__('Header'),
      'class'     => 'required-entry',
      'required'  => true,
      'name'      => 'sub_title',
    ));

    $fieldset->addField('link_caption', 'text', array(
      'label'     => Mage::helper('promotionmodules')->__('CTA'),
      'class'     => 'required-entry',
      'required'  => true,
      'name'      => 'link_caption',
    ));

    $fieldset->addField('link_href', 'text', array(
      'label'     => Mage::helper('promotionmodules')->__('CTA Link Href'),
      'name'      => 'link_href',
    ));

      $fieldset->addField('desktop_image', 'image', array(
          'label' => Mage::helper('promotionmodules')->__('Image'),
          'name' => 'desktop_image',
          'after_element_html' => '<small>'.$imagesDir.'</small>',
      ));
/*
      $fieldset->addField('mobile_image', 'image', array(
          'label' => Mage::helper('promotionmodules')->__('Mobile Image'),
          'name' => 'mobile_image',
          'after_element_html' => '<small>'.$imagesDir.'</small>',
      ));
*/
    $fieldset->addField('details', 'textarea', array(
        'label'     => Mage::helper('promotionmodules')->__('Copy'),
        'name'      => 'details',
    ));



    $fieldset->addField('sort_order', 'text', array(
      'label'     => Mage::helper('promotionmodules')->__('Sort Order'),
      'name'      => 'sort_order',
      'class'     => 'validate-digits',
      'after_element_html' => '<small>Sort order for forms</small>',
    ));

    $fieldset->addField('active', 'select', array(
      'label'     => Mage::helper('promotionmodules')->__('Status'),
      'name'      => 'active',
        'values' => array('0' => 'Inactive', '1' => 'Active'),// - See more at: http://excellencemagentoblog.com/blog/2011/11/02/magento-admin-form-field/#sthash.cgXepzMc.dpuf
//      'class'     => 'validate-digits',
//      'after_element_html' => '<small>Sort order for forms</small>',
    )); //->setIsChecked((bool)$data['active']);
//var_dump($data);

//      $data['active'] = "1";


      $fieldset->addField('active_from', 'date', array(
          'label'     => Mage::helper('promotionmodules')->__('Date Start'),
          'name'      => 'active_from',
          'class'     => 'validate-date',
          'after_element_html' => '<small>Select Date</small>',
          'image'     => $this->getSkinUrl('images/grid-cal.gif'),

          'format' => "MMM d, y",
      ));
      $fieldset->addField('active_to', 'date', array(
          'label'     => Mage::helper('promotionmodules')->__('Date End'),
          'name'      => 'active_to',
          'class'     => 'validate-date',
          'after_element_html' => '<small>Select Date</small>',
          'image'     => $this->getSkinUrl('images/grid-cal.gif'),

          'format' => "MMM d, y",
      ));

    $fieldset->addField('text_color', 'text', array(
        'label'     => Mage::helper('promotionmodules')->__('Text Color'),
        'name'      => 'text_color',
//        'class'     => 'validate-digits',
        'class'     => 'color {required:false, adjust:false, hash:true}',
        'after_element_html' => '<small>Color Of The Text</small>',
    ));

    $fieldset->addField('bg_color', 'text', array(
        'label'     => Mage::helper('promotionmodules')->__('BG Color'),
        'name'      => 'bg_color',
//        'class'     => 'validate-digits',
        'class'     => 'color {required:false, adjust:false, hash:true}',
        'after_element_html' => '<small>Color Of The Background</small>',
    ));


    $form->setValues($data);

    return parent::_prepareForm();
  }
}