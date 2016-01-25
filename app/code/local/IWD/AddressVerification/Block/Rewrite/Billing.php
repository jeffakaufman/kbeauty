<?php
/**
 * Created by PhpStorm.
 * User: Àðò¸ì
 * Date: 31.10.2015
 * Time: 14:49
 */
class IWD_AddressVerification_Block_Rewrite_Billing extends Idev_OneStepCheckout_Block_Billing    {

    protected function _toHtml()
    {
        $this->setTemplate('iwdaddressverification/billing.phtml');
        return parent::_toHtml();
    }
}