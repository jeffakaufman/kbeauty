<?php

class Valkyrie_Press_Block_Adminhtml_Press extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  protected $_addButtonLabel = 'Add New Item';

  public function __construct()
  {
    $this->_controller = 'adminhtml_press';
    $this->_blockGroup = 'press';
    $this->_headerText = Mage::helper('press')->__('Press');

    $this->_addButton('export_button', array(
        'label' => Mage::helper('press')->__('Export To JSON'),
        'onclick' => 'setLocation(\'' . $this->getUrl('*/adminhtml_press/export') . '\')',
        'class' => 'go',
    ), 0);


//    $this->_addButton('import', array(
//        'label' => Mage::helper('adminhtml')->__('Import'),
//    ));

//    $this->add
    parent::__construct();
  }

}