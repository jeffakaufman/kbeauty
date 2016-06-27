<?php
class Boost_Pay_Block_Info_Boost extends Mage_Payment_Block_Info
{
  protected function _prepareSpecificInformation($transport = null)
  {
    $transport   = parent::_prepareSpecificInformation($transport);
    $data     = array();

    $data[Mage::helper('boost')->__('ABC')] = 'test';
    //$data[Mage::helper('boost')->__('Boost Transaction Id')] = $this->getInfo()->getAdditionalInformation('boost.transactionId');
    return $transport->setData(array_merge($transport->getData(), $data));
  }
}
?>
