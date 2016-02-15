<?php

class Valkyrie_TopPromotion_Block_Adminhtml_TopPromotion_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

  protected function _prepareForm()
  {
//    $imagesDir = 'media/'.Valkyrie_SliderData_Adminhtml_SliderDataController::SLIDER_DATA_IMAGES_DIR.'/';
//var_dump(Mage::getSingleton('adminhtml/session'));
    if (Mage::getSingleton('adminhtml/session')->getTopPromotionData())
    {
      $data = Mage::getSingleton('adminhtml/session')->getTopPromotionData();
      Mage::getSingleton('adminhtml/session')->getTopPromotionData(null);
    }
    elseif (Mage::registry('toppromotion'))
    {
      $data = Mage::registry('toppromotion')->getData();
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

    $fieldset = $form->addFieldset('toppromotion_form', array(
      'legend' =>Mage::helper('toppromotion')->__('Promotion Information')
    ));

    $fieldset->addField('title', 'text', array(
      'label'     => Mage::helper('toppromotion')->__('Title'),
      'class'     => 'required-entry',
      'required'  => true,
      'name'      => 'title',

        'after_element_html' => '<small>Use <strong>{br}</strong> for mobile line breaks</small>',

    ));

/*
    $fieldset->addField('link_href', 'text', array(
      'label'     => Mage::helper('sliderdata')->__('Link Href'),
      'name'      => 'link_href',
    ));
*/

    $fieldset->addField('details_content', 'textarea', array(
        'label'     => Mage::helper('toppromotion')->__('Details'),
        'name'      => 'details_content',
    ));

/*
    $fieldset->addField('active', 'select', array(
      'label'     => Mage::helper('sliderdata')->__('Status'),
      'name'      => 'active',
        'values' => array('0' => 'Inactive', '1' => 'Active'),// - See more at: http://excellencemagentoblog.com/blog/2011/11/02/magento-admin-form-field/#sthash.cgXepzMc.dpuf
//      'class'     => 'validate-digits',
//      'after_element_html' => '<small>Sort order for forms</small>',
    )); //->setIsChecked((bool)$data['active']);
//var_dump($data);

//      $data['active'] = "1";
*/

      $fieldset->addField('active_from', 'date', array(
          'label'     => Mage::helper('toppromotion')->__('Date Start'),
          'name'      => 'active_from',
          'class'     => 'validate-date',
          'after_element_html' => '<small>Select Date</small>',
          'image'     => $this->getSkinUrl('images/grid-cal.gif'),

          'format' => "MMM d, y",
      ));
      $fieldset->addField('active_to', 'date', array(
          'label'     => Mage::helper('toppromotion')->__('Date End'),
          'name'      => 'active_to',
          'class'     => 'validate-date',
          'after_element_html' => '<small>Select Date</small>',
          'image'     => $this->getSkinUrl('images/grid-cal.gif'),

          'format' => "MMM d, y",
      ));


    $form->setValues($data);

    return parent::_prepareForm();
  }
}