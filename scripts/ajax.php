<?php //require_once  '/var/www/kbeauty.com/app/Mage.php';
require_once  '/var/www/dev.kardashian-beauty.net/app/Mage.php';

Mage::app();

//var_dump($_SESSION);


//session_id($_COOKIE['frontend']);

// Get session
Mage::getSingleton('core/session', array('name'=>'frontend'));

//session_id($_COOKIE['frontend']);

//var_dump(session_id());
//var_dump(session_name());

//var_dump($_COOKIE);

//var_dump(Mage::getSingleton('core/session'));
//var_dump(Mage::getSingleton('customer/session'));
//var_dump(Mage::getSingleton('frontend/session'));

$updateTotals = false;

// Check for a product id
if(isset($_POST['productId']))
{
    $updateTotals = true;
    // Product ID and Quantity
    $pid = $_POST['productId'];
    $qnt = $_POST['qty'];

    if ($qnt != 0) {
        $cart = Mage::getSingleton('checkout/cart');
        $items = $cart->getItems(); 

        foreach ($items as $item) :

            if($pid == $item->getId()) :
                
                $item->setQty($qnt); 
                $cart->save();
                $itemsubtotal = $item->getPrice() * $qnt;
                
            endif;
        endforeach;
    } else {
        //Mage::getSingleton('checkout/cart')->removeItem($pid)->save();
        Mage::helper('checkout/cart')->getCart()->removeItem($pid)->save();
    }
}

//var_dump(Mage::getSingleton('checkout/cart'));

//var_dump(Mage::helper('checkout/cart')->getCart());

// THE REST IS updatTotalG FUNCTION WHICH IS CALLED AFTER AJAX IS COMPLETED 
// (UPDATE THE TOTALS)
//echo $_SERVER['DOCUMENT_ROOT'];
$simbol= Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
$totalItemsInCart = Mage::helper('checkout/cart')->getSummaryCount(); //total items in cart
//$totalItemsInCart = Mage::getSingleton('checkout/session')->getQuote()->getItemsCount();
$totals = Mage::getSingleton('checkout/session')->getQuote()->getTotals(); //Total object

if(isset($totals['tax'])) {
    $tax = $totals['tax']->getValue(); 
}
//$shipping_data = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getData();
$cart = Mage::getModel('checkout/cart')->getQuote()->getData();

//var_dump( Mage::getModel('checkout/cart')->getQuote() );


foreach($cart as $key => $value)
{
  echo '<div style="padding-left: 5px; font-size: 12px">'.$key.': '. $value . '</div>' . "\n";
}

if(isset($totals['shipping'])) {
    $shipping = $totals['shipping']->getValue(); 
}
$subtotal = $totals["subtotal"]->getValue(); //Subtotal value
$grandtotal = $totals["grand_total"]->getValue(); //Grandtotal value

echo '<script type="text/javascript">';
if ($updateTotals) {
    echo 'function updateTotals(){';
    echo 'jQuery("#itemsubtotal-'. $pid .' .price, .block-cart .item-'. $pid .' .itemTotal .price").html(\'';
        echo $simbol.number_format($itemsubtotal,2);
    echo "');";
    echo 'jQuery("#shopping-cart-totals-table tbody tr.first .price, .block-cart .subtotal .price, #checkout-review-table tr:nth-child(1) td:nth-child(2)").html(\'';
        echo $simbol.number_format($subtotal,2);
    echo "');";
    if ($tax && $shipping) {
        echo 'if (jQuery("#checkout-review-table tr").length == 4) { jQuery("#checkout-review-table tr:nth-child(3) td:nth-child(2)").html(\'';
            echo $simbol.number_format($tax,2);
        echo "');";
        echo '} else { jQuery("<tr><td class=\'a-right\'>Tax</td><td class=\'a-right\'>'.$simbol.number_format($tax,2).'</td></tr>").insertBefore("#checkout-review-table tr:last-child");';
        echo "}";
        echo 'jQuery("#shopping-cart-totals-table tbody tr.last .price").html(\'';
            echo $simbol.number_format($tax,2);
        echo "');";
    } else if ($tax) {
        echo 'if (jQuery("#checkout-review-table tr").length == 2) {';
        echo 'jQuery("<tr><td class=\'a-right\'>Tax</td><td class=\'a-right\'>'.$simbol.number_format($tax,2).'</td></tr>").insertAfter("#checkout-review-table tr:first-child");';
        echo 'jQuery("#shopping-cart-totals-table tbody tr.last .price").html(\'';
            echo $simbol.number_format($tax,2);
        echo "'); }";
    } else if ($shipping) {
        echo 'jQuery("#shopping-cart-totals-table tbody tr.even .price").html(\'';
            echo $simbol.number_format($shipping,2);
        echo "');";
        echo 'if (jQuery("#checkout-review-table tr").length == 4) jQuery("#checkout-review-table tr:nth-child(3) td:nth-child(2) .price").html(\'';
            echo $simbol.number_format($shipping,2);
        echo "');";
        echo 'else jQuery("<tr><td class=\'a-right\'>Shipping</td><td class=\'a-right\'><span class=\'price\'>'.$simbol.number_format($shipping,2).'</span></td></tr>").insertAfter("#checkout-review-table tr:first-child");';
        echo 'jQuery("#checkout-review-table tr:last-child .price").html(\'';
            echo $simbol.number_format($grandtotal,2);
        echo "');";
    }
    echo 'jQuery("#shopping-cart-totals-table tfoot tr.first .price, #checkout-review-table tr:last-child td:nth-child(2)").html(\'';
        echo $simbol.number_format($grandtotal,2);
    echo "');";
    // if ($totalItemsInCart == 0) {
    //     echo 'jQuery(".block-cart .summary, .block-cart .actions").remove(); jQuery(".block-cart .block-content").html(\'<div class="items"><p class="empty">You have no items in your shopping cart.</p></div>\');';
    // }
    //echo 'console.log("'.$totalItemsInCart.'")';
    echo 'jQuery(".right-off-canvas-toggle .count").text("'.$totalItemsInCart.'")';
    echo '}';
} else if (isset($_POST['tax'])) {
    $tax = Mage::helper('checkout')->getQuote()->getShippingAddress()->getData('tax_amount');
    echo 'function addTax(){';
    if ($tax) {
        echo 'jQuery(\'#checkout-review-table td\').each(function() {';
        echo 'var tdtext = jQuery(this).html();';
        echo 'if (tdtext.indexOf(\'Tax\') == -1) var missingTax = true;';
        echo '});';
        echo 'if (missingTax) jQuery(\'<tr><td class="a-right">Tax</td><td class="a-right"><span class="price">'.$simbol.number_format($tax,2).'</span></td></tr>\').insertBefore(\'#checkout-review-table tr:last-child\');';
        echo 'else jQuery(\'#checkout-review-table tr:nth-last-child(2) td:nth-child(2) .price\').html(\'';
        echo $simbol.number_format($tax,2);
        echo '\');';
    } else {
        echo 'jQuery("#checkout-review-table td:contains(\'Tax\')").parent().remove();';
    }
    echo '}';
    //echo 'console.log("'.$subtotal.'");';
    //echo 'console.log("'.$grandtotal.'");';
}
echo '</script>';
?>