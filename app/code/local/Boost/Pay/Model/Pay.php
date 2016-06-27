<?php
class Boost_Pay_Model_Pay extends Mage_Payment_Model_Method_Abstract
{
  protected $_code = 'boost';
  protected $_canUseInternal = true;
  protected $_canUseCheckout = false;
  protected $_canCapture = true;


   public function capture( Varien_Object $payment, $amount )
   {
      $this->_log( sprintf( 'capture(%s %s, %s)', get_class( $payment ), $payment->getId(), $amount ) );


      if( $payment->hasInvoice() && $payment->getInvoice() instanceof Mage_Sales_Model_Order_Invoice ) {
              $invoice        = $payment->getInvoice();
      }
      else {
              $invoice        = Mage::registry('current_invoice');
      }

      error_log(json_encode($invoice));
      return $this;
   }

}
?>
