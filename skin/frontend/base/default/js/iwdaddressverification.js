;
//define jquery
var $j_av = false;
if(typeof($j_av) == 'undefined' || $j_av == undefined || !$j_av){
    if(typeof($ji) != 'undefined' && $ji != undefined && $ji)
        $j_av = $ji; // from iwd_all 2.x
    else{
        if(typeof(jQuery) != 'undefined' && jQuery != undefined && jQuery)
            $j_av = jQuery.noConflict();
    }
}

IWD_AV = {
    init: function(){
        IWD.OPC.Plugin.event('responseSaveOrder', IWD_AV.validationResponseSaveOrder);
    },

    validationResponseSaveOrder: function(response){

        if (typeof(response.address_validation) != "undefined"){
            $j_av('#checkout-address-validation-load').empty().html(response.address_validation);
            IWD.OPC.Checkout.hideLoader();
            IWD.OPC.Checkout.unlockPlaceOrder();
            return;
        }
    }
};

var is_opc_checkout = false; // IWD One Page Checkout
var is_osc_checkout = false; // AheadWorks One Step Checkout
var is_idev_checkout = false; // Idev One Step Checkout
var idev_checkout_address_type = 'Billing';

if($j_av)
{
    $j_av(window).load(function(){
        if(typeof(OneStepCheckoutLoginPopup) != 'undefined' && OneStepCheckoutLoginPopup != undefined)
        {
            setTimeout(function(){
                already_placing_order = true;
                if(typeof(triggers) != 'undefined' && triggers){
                    triggers.each(function(item){
                        var belement = $('billing:'+item);
                        if(belement){
                            belement.observe('change', function(e){
                                idev_checkout_address_type = 'Billing';
                            });
                        }

                        var selement = $('shipping:'+item);
                        if(selement){
                            selement.observe('change', function(e){
                                idev_checkout_address_type = 'Shipping';
                            });
                        }
                    });
                }
            },1000);
        }
    });
    $j_av(document).ready(function()
    {
        // detect if IWD OPC exists
        if(typeof(IWD) != 'undefined' && IWD != undefined)
        {
            if(typeof(IWD.OPC) != 'undefined' && IWD.OPC != undefined && IWD.OPC != '' && IWD.OPC)
            {
                is_opc_checkout = true;
            }
        }
        // detect if AW OSC exists
        if(typeof(AWOnestepcheckoutCore) != 'undefined' && AWOnestepcheckoutCore != undefined)
        {
            is_osc_checkout = true;
        }
        if(typeof(OneStepCheckoutLoginPopup) != 'undefined' && OneStepCheckoutLoginPopup != undefined)
        {
            is_idev_checkout = true;


        }
        if(is_opc_checkout)
        {
            IWD_AV.init();
        }
/////// for ahead works
        if(is_osc_checkout)
        {
            AWOnestepcheckoutCore.updater.map['saveAddress'].push('address_validation');
            AWOnestepcheckoutCore.updater._registerBlockNameForUpdate('address_validation', '#checkout-address-validation-load');

            $$('#checkout-address-validation-load').first().addActionBlocksToQueueAfterFn = function(response) {
                var me = AWOnestepcheckoutCore.updater;
                var blockName = 'address_validation';
                AWOnestepcheckoutCore.removeLoaderFromBlock(me.blocks[blockName], me.loaderConfig);
            }

            $$('#checkout-address-validation-load').first().removeActionBlocksFromQueueAfterFn = function(response) {
                var msg = response.messages || response.message;
                if (typeof(msg) == 'object') {
                    msg = msg.join("\n");
                }
                if (msg) {
                    alert(msg);
                }
            }
        }
        if(is_idev_checkout){
            $('onestepcheckout-place-order').observe('click', function(e)   {
                var items = exclude_unchecked_checkboxes($$('input[name^=billing]').concat($$('select[name^=billing]')));
                var names = items.pluck('name');
                var values = items.pluck('value');
                var parameters = {type:idev_checkout_address_type};
                var street_count = 0;
                for(var x=0; x < names.length; x++)    {
                    if(names[x] != 'payment[method]')    {

                        var current_name = names[x];

                        if(names[x] == 'billing[street][]')    {
                            current_name = 'billing[street][' + street_count + ']';
                            street_count = street_count + 1;
                        }

                        parameters[current_name] = values[x];
                    }
                }
                var use_for_shipping = $('billing:use_for_shipping_yes');

                if(use_for_shipping && use_for_shipping.getValue() != '1')    {
                    var items = $$('input[name^=shipping]').concat($$('select[name^=shipping]'));
                    var shipping_names = items.pluck('name');
                    var shipping_values = items.pluck('value');
                    var shipping_parameters = {};
                    var street_count = 0;

                    for(var x=0; x < shipping_names.length; x++)    {
                        if(shipping_names[x] != 'shipping_method')    {
                            var current_name = shipping_names[x];
                            if(shipping_names[x] == 'shipping[street][]')    {
                                current_name = 'shipping[street][' + street_count + ']';
                                street_count = street_count + 1;
                            }

                            parameters[current_name] = shipping_values[x];
                        }
                    }
                }
                iwd_address_validation(parameters);
            });
        }
    });
}

function iwd_address_validation(parameters){
    if(!parameters){
        parameters = {type:idev_checkout_address_type};
    }
    new Ajax.Request(av_base_url+'iwd_addressverification/av/validation', {
        method: 'post',
        parameters: parameters,
        onSuccess: function(transport){
            already_placing_order = false;
            if(transport.status == 200)    {
                var data = transport.responseText.evalJSON();
                if(data && data.validation_result){
                    $j_av('#checkout-address-validation-load').empty().html(data.validation_result);
                }
            }
        }
    });
}

function continue_verification()
{
    already_placing_order = true;
    var va_bill_exist = false;
    var va_ship_exist = false;

    var va_bill_id = -1;
    $$('.va_bill_id').each(function(el){
        va_bill_exist = true; // check if exist any radiobox
        if (el.checked)
            va_bill_id = el.value; // get checked value
    });

    var va_ship_id = -1;
    $$('.va_ship_id').each(function(el){
        va_ship_exist = true; // check if exist any radiobox
        if (el.checked)
            va_ship_id = el.value; // get checked value
    });

    // check if user made all choices
    if(va_bill_exist && va_bill_id == -1)
    {
        alert('Please, make your choice');
        return false;
    }

    if(va_ship_exist && va_ship_id == -1)
    {
        alert('Please, make your choice');
        return false;
    }

    close_verification();
    if(va_bill_id != -1 && va_bill_id != 'use_original_address')
    {
        copy_valid_address('billing', va_bill_id);

        // open new address form
        if(is_opc_checkout){
            // for IWD OPC
            IWD.OPC.saveOrderStatus = false;

            var obj =$('billing-address-select');
            if(obj != null && obj != undefined && typeof(obj) != 'undefined')
            {
                if(obj.value != '') // customer has address, need to open new form
                {
                    iwdopc_new_address('billing', true);
                }
            }
        }

        if(is_osc_checkout){
            // for AW OSC
            if (awOSCAddress.billing.changeAddressSelect) {
                var cur_addr = awOSCAddress.billing.changeAddressSelect.value;
                if (cur_addr != '') // customer has address, need to open new form
                {
                    awOSCAddress.billing.changeAddressSelect.value = '';
                    awOSCAddress.showNewAddressContainer(awOSCAddress.billing.newAddressContainer);
                }
            }
        }
        if(is_osc_checkout){
            if($j_av('#billing-address-select'))
                $j_av('#billing-address-select').val('');
        }
        if(!is_opc_checkout && !is_osc_checkout && !is_idev_checkout){
            billing.newAddress(true);
        }

    }

    if(va_ship_id != -1  && va_ship_id != 'use_original_address')
    {
        copy_valid_address('shipping', va_ship_id);

        // open new address form
        if(is_opc_checkout){
            // for IWD OPC
            IWD.OPC.saveOrderStatus = false;

            var obj =$('shipping-address-select');
            if(obj != null && obj != undefined && typeof(obj) != 'undefined')
            {
                if(obj.value != '') // customer has address, need to open new form
                {
                    iwdopc_new_address('shipping', true);
                }
            }
        }

        if(is_osc_checkout){
            // for AW OSC
            if (awOSCAddress.shipping.changeAddressSelect) {
                var cur_addr = awOSCAddress.shipping.changeAddressSelect.value;
                if (cur_addr != '') // customer has address, need to open new form
                {
                    awOSCAddress.shipping.changeAddressSelect.value = '';
                    awOSCAddress.showNewAddressContainer(awOSCAddress.shipping.newAddressContainer);
                }
            }
        }
        if(is_osc_checkout){
            if($j_av('#shipping-address-select'))
                $j_av('#shipping-address-select').val('');
        }
        if(!is_opc_checkout && !is_osc_checkout && !is_idev_checkout)
            shipping.newAddress(true);
    }

    // for IWD OPC
    if(is_opc_checkout)
    {
        var continue_iwdopc = false;

        // update
        var ship_updated = false;
        if(va_bill_id != -1 && va_bill_id != 'use_original_address')
        {
            if ($('shipping:same_as_billing') && $('shipping:same_as_billing').checked)
                IWD.OPC.Billing.setBillingForShipping(true);

            ship_updated = true;
            IWD.OPC.Billing.validateForm();
        }
        else if(va_bill_id == 'use_original_address')
        {
            continue_iwdopc = 'billing';
        }

        if(!ship_updated)
        {
            if(va_ship_id != -1 && va_ship_id != 'use_original_address')
            {
                continue_iwdopc = false;

                IWD.OPC.Shipping.validateForm();
            }
            else if(va_ship_id == 'use_original_address')
            {
                continue_iwdopc = 'shipping';
            }
        }

        // if choosed original address, need some special logic
        if(continue_iwdopc != '')
        {
            if(IWD.OPC.saveOrderStatus == true)
                IWD.OPC.saveOrder();
            else
            {
                if(continue_iwdopc == 'billing')
                    IWD.OPC.Billing.validateForm();
                if(continue_iwdopc == 'shipping')
                    IWD.OPC.Shipping.validateForm();
            }
        }

        return true;
    }

    // for AW OSC
    if(is_osc_checkout)
    {
        var continue_awosc = false;
        var address_choosen = false;

        // update
        var ship_updated = false;
        if(va_bill_id != -1  && va_bill_id != 'use_original_address')
        {
            address_choosen = true;

            if ($('shipping:same_as_billing') && $('shipping:same_as_billing').checked)
            {
                awOSCAddress.hideShippingAddressContainer();
            }

            ship_updated = true;
        }
        else if(va_bill_id == 'use_original_address')
        {
            address_choosen = true;
            continue_awosc = 'both';
        }

        if(!ship_updated)
        {
            if(va_ship_id != -1 && va_ship_id != 'use_original_address')
            {
                address_choosen = true;
                continue_awosc = false;
            }
            else if(va_ship_id == 'use_original_address')
            {
                address_choosen = true;
                if(continue_awosc != 'both')
                    continue_awosc = 'shipping';
            }
        }

        if(address_choosen){
            if($('iwdav_results_mode').value == 'save_order' && continue_awosc == 'both')
                awOSCForm.placeOrder(); // if user click 'place order' and chooses both address as entered before, so save order
            else
                awOSCAddress.onAddressChanged(null);
        }
    }
    if(va_bill_id == 'use_original_address' || va_ship_id == 'use_original_address'){
        already_placing_order = false;
    }
}

function iwdopc_new_address(type, new_address)
{
    if (new_address){
        var selectElement = $j_av('#'+type+'-address-select');
        if (selectElement) {
            selectElement.val('');
        }
        $j_av('#'+type+'-new-address-form').show();
    } else {
        $j_av('#'+type+'-new-address-form').hide();
    }
}

function close_verification()
{
    $("iwdPopupOverlay").hide();
    $('address-verification-results').hide();
}

function copy_valid_address(type, candidate_id)
{
    already_placing_order = false;
    if(candidate_id != '')
        candidate_id = '_'+candidate_id;

    $(type+':street1').value	= $('va_'+type+'_street'+candidate_id).value;
    if($(type+':street2'))
        $(type+':street2').value	= '';
    if($(type+':street3'))
        $(type+':street3').value	= '';
    $(type+':city').value		= $('va_'+type+'_city'+candidate_id).value;
    $(type+':region_id').value	= $('va_'+type+'_region'+candidate_id).value;
    $(type+':postcode').value	= $('va_'+type+'_postcode'+candidate_id).value;
    if(is_idev_checkout && av_idev_ajax_billing){
        get_save_billing_function(av_idev_bill_save_url,av_idev_methods_separate_save_url,av_idev_update_payment_on_shipping,true)();
    }
}
