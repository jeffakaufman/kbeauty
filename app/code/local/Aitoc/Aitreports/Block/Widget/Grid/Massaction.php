<?php
class Aitoc_Aitreports_Block_Widget_Grid_Massaction extends Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitreports/13/massaction.phtml');
        $this->setErrorText(Mage::helper('catalog')->jsQuoteEscape(Mage::helper('catalog')->__('Please select  items')));
    }
}
