<?php

class Valkyrie_Press_Block_Adminhtml_Press_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

  protected function _prepareForm()
  {
    $imagesDir = 'media/';//.Valkyrie_Press_Adminhtml_SliderDataController::SLIDER_DATA_IMAGES_DIR.'/';
//var_dump(Mage::getSingleton('adminhtml/session'));
    if (Mage::getSingleton('adminhtml/session')->getPressData())
    {
      $data = Mage::getSingleton('adminhtml/session')->getPressData();
      Mage::getSingleton('adminhtml/session')->getPressData(null);
    }
    elseif (Mage::registry('press'))
    {
      $data = Mage::registry('press')->getData();
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

    $fieldset = $form->addFieldset('press_form', array(
      'legend' =>Mage::helper('press')->__('Press Information')
    ));

    $fieldset->addField('category', 'select', array(
      'label'     => Mage::helper('press')->__('Category'),
      'class'     => 'required-entry',
      'required'  => true,
      'name'      => 'category',

      'values'    => $this->getCategories(),
    ));


      /*
          {
                  "category": "editorial",
                  "product_name": "Contrast Black Top",
                  "product_image_src": "/media/catalog/product/cache/3/image/157x157/9df78eab33525d08d6e5fb8d27136e95/3/f/3f74001_print_flat.jpg",
                  "product_description": "A crew neck shell top gets a modern update with an artsy paint-printed front and a solid contrast back that dips into a high-low hem",
                  "product_link": "/shirts-and-blouses/contrast-back-top-2860",
                  "image_name": "People En Espanol - September 2015",
                  "image_src": "/media/cms1520/press/images/press/editorial/Fifteen_Twenty_People_En_Espanol_September issue_Cover.jpg",
                  "article_link": ""
              },
       */


/*    $fieldset->addField('sub_title', 'text', array(
      'label'     => Mage::helper('sliderdata')->__('Subtitle'),
      'class'     => 'required-entry',
      'required'  => true,
      'name'      => 'sub_title',
    ));

    $fieldset->addField('link_caption', 'text', array(
      'label'     => Mage::helper('sliderdata')->__('Link Caption'),
      'class'     => 'required-entry',
      'required'  => true,
      'name'      => 'link_caption',
    ));*/

    $fieldset->addField('product_name', 'text', array(
      'label'     => Mage::helper('press')->__('Product Name'),
      'name'      => 'product_name',
    ));

    $fieldset->addField('product_image_src', 'text', array(
      'label'     => Mage::helper('press')->__('Product Image Src'),
      'name'      => 'product_image_src',
    ));

    $fieldset->addField('product_description', 'text', array(
      'label'     => Mage::helper('press')->__('Product Description'),
      'name'      => 'product_description',
    ));

      $fieldset->addField('product_link', 'text', array(
          'label'     => Mage::helper('press')->__('Product Link'),
          'name'      => 'product_link',
      ));

//    $fieldset->addField('link_href', 'text', array(
//      'label'     => Mage::helper('press')->__('Link Href'),
//      'name'      => 'link_href',
//    ));

      $fieldset->addField('image_name', 'text', array(
          'label'     => Mage::helper('press')->__('Imgae Name'),
          'name'      => 'image_name',
      ));

      $fieldset->addField('image_src', 'text', array(
          'label'     => Mage::helper('press')->__('Image Src'),
          'name'      => 'image_src',
      ));

/*
      $fieldset->addField('desktop_image', 'image', array(
          'label' => Mage::helper('press')->__('Desktop Image'),
          'name' => 'desktop_image',
          'after_element_html' => '<small>'.$imagesDir.'</small>',
      ));

      $fieldset->addField('mobile_image', 'image', array(
          'label' => Mage::helper('press')->__('Mobile Image'),
          'name' => 'mobile_image',
          'after_element_html' => '<small>'.$imagesDir.'</small>',
      ));

    $fieldset->addField('disclaimers_content', 'textarea', array(
        'label'     => Mage::helper('press')->__('Disclaimers'),
        'name'      => 'disclaimers_content',
    ));
*/


    $fieldset->addField('sort_order', 'text', array(
      'label'     => Mage::helper('press')->__('Sort Order'),
      'name'      => 'sort_order',
      'class'     => 'validate-digits',
      'after_element_html' => '<small>Sort order for forms</small>',
    ));



    $form->setValues($data);

    return parent::_prepareForm();
  }


    private function getCategories() {
        $data = array();

        $data[] = array(
            'label' => 'Editorial',
            'value' => 'editorial',
        );

        $data[] = array(
            'label' => 'Blogger Style',
            'value' => 'blogger',
        );

        $data[] = array(
            'label' => 'Celebrity',
            'value' => 'celebrity',
        );

        return $data;
    }
}

/*
	{
			"category": "editorial",
			"product_name": "Contrast Black Top",
			"product_image_src": "/media/catalog/product/cache/3/image/157x157/9df78eab33525d08d6e5fb8d27136e95/3/f/3f74001_print_flat.jpg",
			"product_description": "A crew neck shell top gets a modern update with an artsy paint-printed front and a solid contrast back that dips into a high-low hem",
			"product_link": "/shirts-and-blouses/contrast-back-top-2860",
			"image_name": "People En Espanol - September 2015",
			"image_src": "/media/cms1520/press/images/press/editorial/Fifteen_Twenty_People_En_Espanol_September issue_Cover.jpg",
			"article_link": ""
		},
 */