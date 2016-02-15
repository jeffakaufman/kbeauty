<?php

class Valkyrie_TopPromotion_Block_Adminhtml_TopPromotion_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
    parent::__construct();
    $this->setId('toppromotion_grid');
    $this->setDefaultSort('id');
    $this->setDefaultDir('desc');
    $this->setSaveParametersInSession(true);
  }

  protected function _getCollectionClass()
  {
    // This is the model we are using for the grid
    return 'toppromotion/toppromotion_collection';
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
    $this->addColumn('promotion_id', array(
      'header'    => Mage::helper('toppromotion')->__('ID'),
      'align'     => 'right',
      'width'     => '50px',
      'index'     => 'promotion_id',
    ));

    $this->addColumn('title', array(
      'header'    => Mage::helper('toppromotion')->__('Title'),
      'align'     => 'left',
      'index'     => 'title',
    ));

/*
    $this->addColumn('link_href', array(
      'header'    => Mage::helper('sliderdata')->__('Link Href'),
      'align'     => 'left',
      'index'     => 'link_href',
    ));


     $this->addColumn('active', array(
      'header'    => Mage::helper('sliderdata')->__('Status'),
      'align'     => 'left',
      'index'     => 'active',
      'width'     => '30px',

         'type' => 'options',
         'options' => $this->GetStatusCaptions(),

    ));
*/

     $this->addColumn('active_from', array(
      'header'    => Mage::helper('toppromotion')->__('Date Start'),
      'align'     => 'center',
      'index'     => 'active_from',
      'width'     => '30px',
         'type' => 'date',
         'format' => 'MMM d, Y', //Dec 28, 2015
    ));

     $this->addColumn('active_to', array(
      'header'    => Mage::helper('toppromotion')->__('Date End'),
      'align'     => 'center',
      'index'     => 'active_to',
      'width'     => '30px',
         'type' => 'date',
         'format' => "MMM d, y",
    ));
//var_dump(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_LONG));

    return parent::_prepareColumns();
  }

    public function GetStatusCaptions() {
        return  array('0' => 'Inactive', '1' => 'Active');
    }

  public function getRowUrl($row)
  {
    return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
}