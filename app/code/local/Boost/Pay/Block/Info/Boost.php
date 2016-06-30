<?php
class Boost_Pay_Block_Info_Boost extends Mage_Payment_Block_Info
{
  protected function _prepareSpecificInformation($transport = null)
  {
    $transport   = parent::_prepareSpecificInformation($transport);
    $data     = array();
    $data[Mage::helper('boost')->__('Stripe Transaction Id')] = $this->getInfo()->getAdditionalInformation('stripe.transactionId');
    $data[Mage::helper('boost')->__('Stripe Customer Id')] = $this->getInfo()->getAdditionalInformation('stripe.customerId');
    $data[Mage::helper('boost')->__('Boost User Transaction')] = $this->getInfo()->getAdditionalInformation('boost.userTransactionId');
    $data[Mage::helper('boost')->__('Boost Internal Transaction Id')] = $this->getInfo()->getAdditionalInformation('boost.transactionId');
    $data[Mage::helper('boost')->__('Hashtag')] = $this->getInfo()->getAdditionalInformation('boost.hashtag');
    $data[Mage::helper('boost')->__('Hashtag Provider')] = $this->getInfo()->getAdditionalInformation('boost.provider');
    return $transport->setData(array_merge($transport->getData(), $data));
  }
}
?>
