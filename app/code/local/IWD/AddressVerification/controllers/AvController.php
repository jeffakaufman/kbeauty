<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
class IWD_AddressVerification_AvController extends Mage_Checkout_OnepageController
{
    var $_cur_layout = null;

    public function getVerification()
    {
        return Mage::getSingleton('addressverification/verification');
    }

    public function validationAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        // set results mode (need for javascript logic)
        $this->getVerification()->getCheckout()->setValidationResultsMode(false);

        if ($this->getRequest()->isPost())
        {
            $type = $this->getRequest()->getPost('type','Billing');
            if (Mage::helper('addressverification')->isAddressVerificationEnabled())
            {
                $validation_enabled	= Mage::helper('addressverification')->getEnabledVerification();
                if($validation_enabled)
                {
                    $this->getVerification()->setVerificationLib($validation_enabled);
                    if($type == 'Billing'){
                        $this->getVerification()->getCheckout()->setShippingWasValidated(false);
                        $this->getVerification()->getCheckout()->setShippingValidationResults(false);
                    }else{
                        $this->getVerification()->getCheckout()->setBillingWasValidated(false);
                        $this->getVerification()->getCheckout()->setBillingValidationResults(false);
                    }
                    $method	= "get{$type}Address";
                    $data = $this->getRequest()->getPost(strtolower($type), array());

                    if(!$data){
                        $data = $this->getVerification()->getQuote()->$method()->getData();
                    }
                    if($data){
                        if(!isset($data['country_id']) || !$data['country_id'] || strtolower($data['country_id']) != 'us'){
                            return;
                        }
                        $customerAddressId = $this->getVerification()->getQuote()->$method()->getCustomerAddressId();
                        $allow_not_valid	= Mage::helper('addressverification')->allowNotValidAddress(); // if not valid addresses allowed for checkout
//                        echo '<pre>';
//                        var_dump($this->_checkChangedAddress($data, $type, $customerAddressId, $validation_enabled));
//                        var_dump($type);
//                        var_dump($data);
//                        var_dump($customerAddressId);
//                        echo '</pre>';
                        if($this->_checkChangedAddress($data, $type, $customerAddressId, $validation_enabled))
                        {
                            if($type == 'Billing'){
                                $this->getVerification()->getCheckout()->setBillingWasValidated(false);
                            }else{
                                $this->getVerification()->getCheckout()->setShippingWasValidated(false);
                            }
                        }
                        $method_validated = "get{$type}WasValidated";
                        if($allow_not_valid){
                            $type_was_validated	= $this->getVerification()->getCheckout()->$method_validated();
                        }
                        else{
                            $type_was_validated	= false;
                        }
//                        echo '<pre>';
//                        var_dump($type_was_validated);
//                        echo '</pre>';
//                        die();
                        if(!$type_was_validated)
                        {
                            if (isset($data['email'])) {
                                $data['email'] = trim($data['email']);
                            }
                            // run validation
                            $validate	= $this->getVerification()->validate_address($type,$data);
//                            echo '<pre>';
//                            var_dump($data);
//                            var_dump($validate);
//                            echo '</pre>';
//                            die();
                            $method_set_validated = "set{$type}WasValidated";

                            if($validate)
                                $this->getVerification()->getCheckout()->$method_set_validated(true);
                            else
                                $this->getVerification()->getCheckout()->$method_set_validated(false);
                            // check if exist validation errors
                            if(isset($validate) && is_array($validate)
                                && isset($validate['error']) && !empty($validate['error']))
                            {
                                $this->getVerification()->getCheckout()->setShowValidationResults(strtolower($type));

                                $result = array(
                                    'validation_result' => $this->_getAddressCandidatesHtml()
                                );

                                $this->getVerification()->getCheckout()->setShowValidationResults(false);
                                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                                return;
                            }
                            // clear validation results
                            $method_results = "set{$type}ValidationResults";
                            $this->getVerification()->getCheckout()->$method_results(false);
                        }
                    }
                }
            }
        }
    }

    protected function _getAddressCandidatesHtml()
    {
        $layout	= $this->_getUpdatedLayout();
        return $layout->getBlock('checkout.addresscandidates')->toHtml();
    }

    protected function _getUpdatedLayout()
    {
        $this->_initLayoutMessages('checkout/session');
        if ($this->_cur_layout === null)
        {
            $layout = $this->getLayout();
            $update = $layout->getUpdate();
            $update->load('checkout_onepage_index');

            $layout->generateXml();
            $layout->generateBlocks();
            $this->_cur_layout = $layout;
        }

        return $this->_cur_layout;
    }

    protected function _checkChangedAddress($data, $addr_type = 'Billing', $addr_id = false, $check_city_street = false)
    {
        $method	= "get{$addr_type}Address";
        $address = $this->getVerification()->getQuote()->{$method}();

        if(!$addr_id)
        {
            if(($address->getRegionId()	!= $data['region_id']) || ($address->getPostcode() != $data['postcode']) || ($address->getCountryId() != $data['country_id']))
                return true;

            // if need to compare street and city
            if($check_city_street)
            {
                // check street address
                $street1	= $address->getStreet();
                $street2	= $data['street'];

                if(is_array($street1))
                {
                    if(is_array($street2))
                    {
                        if(trim(strtolower($street1[0])) != trim(strtolower($street2[0])))
                        {
                            return true;
                        }
                        if(isset($street1[1]))
                        {
                            if(isset($street2[1]))
                            {
                                if(trim(strtolower($street1[1])) != trim(strtolower($street2[1])))
                                    return true;
                            }
                            else
                            {
                                if(!empty($street1[1]))
                                    return true;
                            }
                        }
                        else
                        {
                            if(isset($street2[1])){
                                $s21	= trim($street2[1]);
                                if(!empty($s21))
                                    return true;
                            }
                        }
                    }
                    else
                    {
                        if(trim(strtolower($street1[0])) != trim(strtolower($street2)))
                            return true;
                    }
                }
                else
                {
                    if(is_array($street2))
                    {
                        if(trim(strtolower($street1)) != trim(strtolower($street2[0])))
                            return true;
                    }
                    else
                    {
                        if(trim(strtolower($street1)) != trim(strtolower($street2)))
                            return true;
                    }
                }

                // check city
                $add_city	= $address->getCity();
                $add_city	= trim(strtolower($add_city));
                if( $add_city	!= trim(strtolower($data['city'])))
                    return true;
            }

            return false;
        }
        else{
            if($addr_id != $address->getCustomerAddressId())
                return true;
            else
                return false;
        }
    }
}