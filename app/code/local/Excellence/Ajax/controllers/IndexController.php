<?php

require_once 'Mage/Checkout/controllers/CartController.php';
class Excellence_Ajax_IndexController extends Mage_Checkout_CartController {

    public function updateAction() {


        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();

        //"params":{"productId":"2187","qty":"2"}

        $response = "";

        $updateTotals = false;

// Check for a product id
        if(isset($params['productId']))
        {
            $updateTotals = true;
            // Product ID and Quantity
            $pid = $params['productId'];
            $qnt = $params['qty'];

            if ($qnt != 0) {
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
                $cart->removeItem($pid)->save();
            }
        }

//var_dump(Mage::getSingleton('checkout/cart'));

//var_dump(Mage::helper('checkout/cart')->getCart());

        $simbol= Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
        $totalItemsInCart = Mage::helper('checkout/cart')->getSummaryCount(); //total items in cart
        $totals = Mage::getSingleton('checkout/session')->getQuote()->getTotals(); //Total object

        if(isset($totals['tax'])) {
            $tax = $totals['tax']->getValue();
        }
//$shipping_data = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getData();
//        $cart = Mage::getModel('checkout/cart')->getQuote()->getData();

//var_dump( Mage::getModel('checkout/cart')->getQuote() );



        if(isset($totals['shipping'])) {
            $shipping = $totals['shipping']->getValue();
        }
        $subtotal = $totals["subtotal"]->getValue(); //Subtotal value
        $grandtotal = $totals["grand_total"]->getValue(); //Grandtotal value

        $response .= '<script type="text/javascript">';
        if ($updateTotals) {
            $response .= 'function updateTotals(){';
            $response .= 'jQuery("#itemsubtotal-'. $pid .' .price, .block-cart .item-'. $pid .' .itemTotal .price").html(\'';
            $response .= $simbol.number_format($itemsubtotal,2);
            $response .= "');";
            $response .= 'jQuery("#shopping-cart-totals-table tbody tr.first .price, .block-cart .subtotal .price, #checkout-review-table tr:nth-child(1) td:nth-child(2)").html(\'';
            $response .= $simbol.number_format($subtotal,2);
            $response .= "');";
            if ($tax) {
                $response .= 'var missingTax = true;';
                $response .= 'jQuery(\'#checkout-review-table td\').each(function() {';
                $response .= 'var tdtext = jQuery(this).html();';
                $response .= 'if (tdtext.indexOf(\'Tax\') >= 0) missingTax = false;';
                $response .= '});';
                $response .= 'if (missingTax) { jQuery("<tr><td class=\'a-right\'>Tax</td><td class=\'a-right\'>'.$simbol.number_format($tax,2).'</td></tr>").insertBefore("#checkout-review-table tr:last-child");';
                $response .= '} else { jQuery("#checkout-review-table tr:nth-child(3) .price").html(\'';
                $response .= $simbol.number_format($tax,2);
                $response .= "');";
                $response .= 'jQuery("#shopping-cart-totals-table tbody tr.last .price").html(\'';
                $response .= $simbol.number_format($tax,2);
                $response .= "');";
                $response .= '}';
            }
            $response .= 'jQuery("#shopping-cart-totals-table tfoot tr.first .price, #checkout-review-table tr:last-child .price").html(\'';
            $response .= $simbol.number_format($grandtotal,2);
            $response .= "');";
            if ($totalItemsInCart == 0) {
                $response .= 'jQuery(".block-cart .summary, .block-cart .actions").remove(); jQuery(".block-cart .block-content").html(\'<div class="items"><p class="empty">You have no items in your shopping cart.</p></div>\');';
            }
            //$response .= 'console.log("'.$totalItemsInCart.'")';
            $response .= 'jQuery(".right-off-canvas-toggle .count").text("'.$totalItemsInCart.'")';
            $response .= '}';
        } else if (isset($_POST['tax'])) {
            $tax = Mage::helper('checkout')->getQuote()->getShippingAddress()->getData('tax_amount');
            $response .= 'function addTax(){';
            // if ($shipping) {
            //     $response .= 'var missingShipping = true;';
            //     $response .= 'jQuery(\'#checkout-review-table td\').each(function() {';
            //     $response .= 'var tdtext = jQuery(this).html();';
            //     $response .= 'if (tdtext.indexOf(\'Shipping\') >= 0) missingShipping = false;';
            //     $response .= '});';
            //     $response .= 'jQuery("#shopping-cart-totals-table tbody tr.even .price").html(\'';
            //     $response .= $simbol.number_format($shipping,2);
            //     $response .= "');";
            //     $response .= 'if (missingShipping) { jQuery("<tr><td class=\'a-right\'>Shipping</td><td class=\'a-right\'><span class=\'price\'>'.$simbol.number_format($shipping,2).'</span></td></tr>").insertAfter("#checkout-review-table tr:first-child");';
            //     $response .= $simbol.number_format($shipping,2);
            //     $response .= "');";
            //     $response .= '} else { console.log("test"); jQuery("#checkout-review-table tr:nth-child(3) td:nth-child(2) .price").html(\'';
            //     $response .= $simbol.number_format($shipping,2);
            //     $response .= "');";
            //     $response .= 'jQuery("#checkout-review-table tr:nth-child(2) td:first-child").html(\'Shipping\');';
            //     $response .= '}';
            // }
            if ($tax) {
                $response .= 'var missingTax = true;';
                $response .= 'jQuery(\'#checkout-review-table td\').each(function() {';
                $response .= 'var tdtext = jQuery(this).html();';
                $response .= 'if (tdtext.indexOf(\'Tax\') >= 0) missingTax = false;';
                $response .= '});';
                $response .= 'if (missingTax) jQuery(\'<tr><td class="a-right">Tax</td><td class="a-right"><span class="price">'.$simbol.number_format($tax,2).'</span></td></tr>\').insertBefore(\'#checkout-review-table tr:last-child\');';
                $response .= 'else jQuery(\'#checkout-review-table tr:nth-last-child(2) td:nth-child(2) .price\').html(\'';
                $response .= $simbol.number_format($tax,2);
                $response .= '\');';
            } else {
                $response .= 'jQuery("#checkout-review-table td:contains(\'Tax\')").parent().remove();';
            }
            $response .= '}';
            //$response .= 'console.log("'.$subtotal.'");';
            //$response .= 'console.log("'.$grandtotal.'");';
        }
        $response .= '</script>';


//        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        $this->getResponse()->setBody($response);
    }

    public function addAction()
    {
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        if($params['isAjax'] == 1){
            $response = array();
            try {
                if (isset($params['qty'])) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $params['qty'] = $filter->filter($params['qty']);
                }
 
                $product = $this->_initProduct();
                $related = $this->getRequest()->getParam('related_product');
 
                /**
                 * Check product availability
                 */
                if (!$product) {
                    $response['status'] = 'ERROR';
                    $response['message'] = $this->__('Unable to find Product ID');
                }
 
                $cart->addProduct($product, $params);
                if (!empty($related)) {
                    $cart->addProductsByIds(explode(',', $related));
                }
 
                $cart->save();
 
                $this->_getSession()->setCartWasUpdated(true);
 
                /**
                 * @todo remove wishlist observer processAddToCart
                 */
                Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                );
 
                if (!$cart->getQuote()->getHasError()){
                    $message = $this->__('%s was added to your bag.', Mage::helper('core')->escapeHtml($product->getName()));
                    $response['status'] = 'SUCCESS';
                    $response['message'] = $message;
                    //New Code Here
                    $this->loadLayout();
                    //$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
                    $sidebar_block = $this->getLayout()->getBlock('cart_sidebar');
                    Mage::register('referrer_url', $this->_getRefererUrl());
                    $totalItemsInCart = Mage::helper('checkout/cart')->getSummaryCount();
                    $sidebar = $sidebar_block->toHtml();
                    $response['qty'] = $params['qty'];
                    $response['sidebar'] = $sidebar;
                    $response['totalItemsInCart'] = $totalItemsInCart;
                }
            } catch (Mage_Core_Exception $e) {
                $msg = "";
                if ($this->_getSession()->getUseNotice(true)) {
                    $msg = $e->getMessage();
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        $msg .= $message.'<br/>';
                    }
                }
 
                $response['status'] = 'ERROR';
                $response['message'] = $msg;
            } catch (Exception $e) {
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Cannot add the item to shopping cart.');
                Mage::logException($e);
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }else{
            return parent::addAction();
        }
    }
    
    public function optionsAction(){
        $productId = $this->getRequest()->getParam('product_id');
        // Prepare helper and params
        $viewHelper = Mage::helper('catalog/product_view');
 
        $params = new Varien_Object();
        $params->setCategoryId(false);
        $params->setSpecifyOptions(false);
 
        // Render page
        try {
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch (Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                Mage::logException($e);
                $this->_forward('noRoute');
            }
        }
    }
    
    protected function _getWishlist($wishlistId = null)
    {
        $wishlist = Mage::registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }
        try {
            if (!$wishlistId) {
                $wishlistId = $this->getRequest()->getParam('wishlist_id');
            }
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            $wishlist = Mage::getModel('wishlist/wishlist');
            
            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } else {
                $wishlist->loadByCustomer($customerId, true);
            }

            if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
                $wishlist = null;
                Mage::throwException(
                    Mage::helper('wishlist')->__("Requested wishlist doesn't exist")
                );
            }
            
            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e,
            Mage::helper('wishlist')->__('Cannot create wishlist.')
            );
            return false;
        }
 
        return $wishlist;
    }
    public function addwishAction()
    {
 
        $response = array();
        if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $response['status'] = 'ERROR';
            $response['message'] = $this->__('Wishlist Has Been Disabled By Admin');
        }
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            $response['status'] = 'ERROR';
            $response['message'] = $this->__('Please Login First');
        }
 
        if(empty($response)){
            $session = Mage::getSingleton('customer/session');
            $wishlist = $this->_getWishlist();
            if (!$wishlist) {
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Unable to Create Wishlist');
            }else{
 
                $productId = (int) $this->getRequest()->getParam('product');
                if (!$productId) {
                    $response['status'] = 'ERROR';
                    $response['message'] = $this->__('Product Not Found');
                }else{
 
                    $product = Mage::getModel('catalog/product')->load($productId);
                    if (!$product->getId() || !$product->isVisibleInCatalog()) {
                        $response['status'] = 'ERROR';
                        $response['message'] = $this->__('Cannot specify product.');
                    }else{
 
                        try {
                            $requestParams = $this->getRequest()->getParams();
                            if ($session->getBeforeWishlistRequest()) {
                                $requestParams = $session->getBeforeWishlistRequest();
                                $session->unsBeforeWishlistRequest();
                            }
                            $buyRequest = new Varien_Object($requestParams);
 
                            $result = $wishlist->addNewItem($product, $buyRequest);
                            if (is_string($result)) {
                                Mage::throwException($result);
                            }
                            $wishlist->save();
 
                            Mage::dispatchEvent(
                                'wishlist_add_product',
                            array(
                                'wishlist'  => $wishlist,
                                'product'   => $product,
                                'item'      => $result
                            )
                            );
 
                            
                            $referer = $session->getBeforeWishlistUrl();
                            if ($referer) {
                                $session->setBeforeWishlistUrl(null);
                            } else {
                                $referer = $this->_getRefererUrl();
                            }
                            $session->setAddActionReferer($referer);
                            
                            Mage::helper('wishlist')->calculate();
                            
                            $message = $this->__('%1$s has been added to your wishlist.',
                            $product->getName(), Mage::helper('core')->escapeUrl($referer));
                            
                            $response['status'] = 'SUCCESS';
                            $response['message'] = $message;
 
                            Mage::unregister('wishlist');
 
                            $this->loadLayout();
                            $toplink = $this->getLayout()->getBlock('top.links')->toHtml();
                            $sidebar_block = $this->getLayout()->getBlock('wishlist_sidebar');
                            $sidebar = $sidebar_block->toHtml();
                            $response['toplink'] = $toplink;
                            $response['sidebar'] = $sidebar;
                        }
                        catch (Mage_Core_Exception $e) {
                            $response['status'] = 'ERROR';
                            $response['message'] = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
                        }
                        catch (Exception $e) {
                            mage::log($e->getMessage());
                            $response['status'] = 'ERROR';
                            $response['message'] = $this->__('An error occurred while adding item to wishlist.');
                        }
                    }
                }
            }
 
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }
    public function compareAction(){
        $response = array();
        
        $productId = (int) $this->getRequest()->getParam('product');
        
        if ($productId && (Mage::getSingleton('log/visitor')->getId() || Mage::getSingleton('customer/session')->isLoggedIn())) {
            $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);
 
            if ($product->getId()/* && !$product->isSuper()*/) {
                Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
                $response['status'] = 'SUCCESS';
                $response['message'] = $this->__('The product %s has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()));
                Mage::register('referrer_url', $this->_getRefererUrl());
                Mage::helper('catalog/product_compare')->calculate();
                Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
                $this->loadLayout();
                $sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');
                $sidebar_block->setTemplate('ajaxwishlist/catalog/product/compare/sidebar.phtml');
                $sidebar = $sidebar_block->toHtml();
                $response['sidebar'] = $sidebar;
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }
}
