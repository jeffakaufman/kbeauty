<?php

class Valkyrie_SliderData_Block_Adminhtml_SliderData_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
    parent::__construct();
    $this->setId('sliderdata_grid');
    $this->setDefaultSort('id');
    $this->setDefaultDir('desc');
    $this->setSaveParametersInSession(true);
  }

  protected function _getCollectionClass()
  {
    // This is the model we are using for the grid
    return 'sliderdata/sliderdata_collection';
  }

  protected function _prepareCollection()
  {
    $collection = Mage::getResourceModel($this->_getCollectionClass());
//echo "<pre>";var_dump($collection);die();
    $this->setCollection($collection);
    return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
    $this->addColumn('slide_id', array(
      'header'    => Mage::helper('sliderdata')->__('ID'),
      'align'     => 'right',
      'width'     => '50px',
      'index'     => 'slide_id',
    ));

    $this->addColumn('title', array(
      'header'    => Mage::helper('sliderdata')->__('Title'),
      'align'     => 'left',
      'index'     => 'title',
    ));

/*    $this->addColumn('sub_title', array(
      'header'    => Mage::helper('sliderdata')->__('Subtitle'),
      'align'     => 'left',
      'index'     => 'sub_title',
    ));*/

/*    $this->addColumn('link_caption', array(
      'header'    => Mage::helper('sliderdata')->__('Link Caption'),
      'align'     => 'left',
      'index'     => 'link_caption',
    ));*/

    $this->addColumn('link_href', array(
      'header'    => Mage::helper('sliderdata')->__('Link Href'),
      'align'     => 'left',
      'index'     => 'link_href',
    ));

      $this->addColumn('desktop_image', array(
          'header'    => Mage::helper('sliderdata')->__('Desktop Image'),
          'align'     => 'left',
          'index'     => 'desktop_image',
      ));

      $this->addColumn('mobile_image', array(
          'header'    => Mage::helper('sliderdata')->__('Mobile Image'),
          'align'     => 'left',
          'index'     => 'mobile_image',
      ));


     $this->addColumn('sort_order', array(
      'header'    => Mage::helper('sliderdata')->__('Sort Order'),
      'align'     => 'center',
      'index'     => 'sort_order',
      'width'     => '30px',
    ));


    return parent::_prepareColumns();
  }

  public function getRowUrl($row)
  {
    return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
}