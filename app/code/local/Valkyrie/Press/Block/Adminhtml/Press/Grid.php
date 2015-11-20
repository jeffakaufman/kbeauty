<?php

class Valkyrie_Press_Block_Adminhtml_Press_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
    parent::__construct();
    $this->setId('press_grid');
    $this->setDefaultSort('id');
    $this->setDefaultDir('desc');
    $this->setSaveParametersInSession(true);
  }

  protected function _getCollectionClass()
  {
    // This is the model we are using for the grid
    return 'press/press_collection';
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
    $this->addColumn('press_id', array(
      'header'    => Mage::helper('press')->__('ID'),
      'align'     => 'right',
      'width'     => '50px',
      'index'     => 'press_id',
    ));

    $this->addColumn('category', array(
      'header'    => Mage::helper('press')->__('Category'),
      'align'     => 'left',
      'index'     => 'category',
        'type' => 'options',
        'options' => $this->getCategories(),
    ));

    $this->addColumn('product_name', array(
      'header'    => Mage::helper('press')->__('Product'),
      'align'     => 'left',
      'index'     => 'product_name',
    ));

/*    $this->addColumn('link_caption', array(
      'header'    => Mage::helper('sliderdata')->__('Link Caption'),
      'align'     => 'left',
      'index'     => 'link_caption',
    ));*/

    $this->addColumn('image_name', array(
      'header'    => Mage::helper('press')->__('Image Name'),
      'align'     => 'left',
      'index'     => 'image_name',
    ));

      /*
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

*/
     $this->addColumn('sort_order', array(
      'header'    => Mage::helper('press')->__('Sort Order'),
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


    private function getCategories() {
        $data = array();

        $data['editorial'] = 'Editorial';
        $data['blogger'] = 'Blogger Style';
        $data['celebrity'] = 'Celebrity';

        return $data;
    }

}