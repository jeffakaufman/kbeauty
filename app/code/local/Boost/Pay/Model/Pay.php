<?php
class Boost_Pay_Model_Pay extends Mage_Payment_Model_Method_Abstract
{
  protected $_code = 'boost';
  protected $_canUseInternal = true;
  protected $_canUseCheckout = false;
  protected $_canCapture = true;

}
?>
