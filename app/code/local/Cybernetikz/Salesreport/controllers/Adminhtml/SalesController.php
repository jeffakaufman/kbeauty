<?php
/**
*	Author		: 	Cybernetikz
*	Author Email:   info@cybernetikz.com
*	Blog		: 	http://blog.cybernetikz.com
*	Website		: 	http://www.cybernetikz.com
*/

class Cybernetikz_Salesreport_Adminhtml_SalesController extends Mage_Adminhtml_Controller_Action
{
public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }
    
	public function reportmanageAction()
    {
        $this->loadLayout()->renderLayout();
    }
	
	public function exportCsvAction()
    {
		if ($data = $this->getRequest()->getPost()) {


			$csv = "";
			$headArr = array("created_at", "orderID", "invoiceID", "sku", "itemPrice", "itemOriginalPrice", "quantity",
				"firstname", "lastname",
//				"billingAddress", "billingCity",
				"billingState", "billingZip", "billingCountry",
				"billingEmail",
//				"billingPhone",
				"subtotal", "shippingAmount", "taxAmount", "cost", "orderStatus",
				"creditCardType", "paymentMethod", "shippingCreatedAt", "shippingState", "shippingZip", "orderDiscount", "S&amp;H",
				"Tax", "Discount", "Subtotal", "Total",
				"Product", "InvoiceTotal",
			);

			$csv .= implode(',', $headArr)."\r\n";

			$orserstatus = "";
			$reportaddress = Mage::helper('salesreport')->getReportAddress();
			$addtess_title = ($reportaddress=="billing")?"Billing":"Shipping";
			$orders_csv_row ="Period,Order Id,Item Name,Qty,Unit Price,Row Total,$addtess_title Name,Email,Street Address,City,State,Postcode,Country";
			$orders_csv_row.="\n";
			
			$filter_type = $_REQUEST['filter_type'];
			
			$from = $_REQUEST['from'];
			$to = $_REQUEST['to'];
			
			$from_date = date('Y-m-d' . ' 00:00:00', strtotime($from));
			$to_date = date('Y-m-d' . ' 23:59:59', strtotime($to));

			$tmpObj = new DateTime($from_date);
			$tmpObj->setTimezone(new DateTimeZone("America/Los_Angeles"));
			$from_date = $tmpObj->format("Y-m-d H:i:s");

			$tmpObj = new DateTime($to_date);
			$tmpObj->setTimezone(new DateTimeZone("America/Los_Angeles"));
			$to_date = $tmpObj->format("Y-m-d H:i:s");

			unset($tmpObj);


			$filter_model  = ($filter_type == 'shipping_date')
            ? 'sales/order_shipment_collection'
            : 'sales/order_collection';
			
			if($_REQUEST['show_order_statuses']>0){
				$orserstatus = $_REQUEST['order_statuses'];
				$_orderCollections = Mage::getResourceModel($filter_model);
					$_orderCollections->addAttributeToSelect('*');
					$_orderCollections->addFieldToFilter('created_at', array('from'=>$from_date, 'to'=>$to_date));
					if($filter_type == 'order_date'){
						$_orderCollections->addFieldToFilter('status', $orserstatus);
					}  
					$_orderCollections->setOrder('created_at', 'desc');              
					$_orderCollections->load();
			}else{
				$_orderCollections = Mage::getResourceModel($filter_model)
					->addAttributeToSelect('*')
					->addFieldToFilter('created_at', array('from'=>$from_date, 'to'=>$to_date))
					->setOrder('created_at', 'desc')
					->load();
			}
								
			$i=0;
			foreach($_orderCollections as $key=>$single_order) {				
				if(($filter_type == 'shipping_date')){
					$_orderId = $single_order->getOrderId();
				}else{
					$_orderId = $single_order->getId();
				}
				
				$myOrder = Mage::getModel('sales/order');
				$myOrder->load($_orderId);


				$createdAt = new DateTime($single_order->getCreatedAt());
				$orderIncrementId = $single_order->getIncrementId();


				$invoiceId = "";

				if($myOrder->hasInvoices()) {
					foreach ($myOrder->getInvoiceCollection() as $inv) {
						$invoiceId = $inv->getIncrementId();
						break;
					}
				}

				$shippingCreatedAt = "";
				foreach($myOrder->getShipmentsCollection() as $shipment){
					/** @var $shipment Mage_Sales_Model_Order_Shipment */
					$shippingCreatedAt = $shipment->getCreatedAt();
					break;
				}

				if($shippingCreatedAt) {
					$shippingCreatedAt = date("m/d/Y", strtotime($shippingCreatedAt));
				}

				//Some Random Fields
//				if($reportaddress=="billing"){
									
					$country_id = utf8_decode($myOrder->getBillingAddress()->getCountryId());
					$country = Mage::getModel('directory/country')->load($country_id)->getName();
					
					$name = utf8_decode($myOrder->getBillingAddress()->getFirstname()." ".$myOrder->getBillingAddress()->getLastname());
					
					$billingaddress = $myOrder->getBillingAddress()->getStreet();
					$address = "";
					$address[] = utf8_decode($billingaddress[0]);
					if($billingaddress[1]){
						$address[] = utf8_decode($billingaddress[1]);
					}
					$address = implode(", ",$address);
					
					$city = utf8_decode($myOrder->getBillingAddress()->getCity());
					
					$region = utf8_decode($myOrder->getBillingAddress()->getRegion());
					
					$postcode = utf8_decode($myOrder->getBillingAddress()->getPostcode());

					$phone = utf8_decode($myOrder->getBillingAddress()->getTelephone());

/*
				}else{
					
					$country_id = utf8_decode($myOrder->getShippingAddress()->getCountryId());
					$country = Mage::getModel('directory/country')->load($country_id)->getName();
					
					$name = utf8_decode($myOrder->getShippingAddress()->getFirstname()." ".$myOrder->getShippingAddress()->getLastname());
					
					$shippingaddress = $myOrder->getShippingAddress()->getStreet();
					$address = "";
					$address[] = utf8_decode($shippingaddress[0]);
					if($shippingaddress[1]){
						$address[] = utf8_decode($shippingaddress[1]);
					}
					$address = implode(", ",$address);
					
					$city = utf8_decode($myOrder->getShippingAddress()->getCity());
					

					$postcode = utf8_decode($myOrder->getShippingAddress()->getPostcode());
				}

*/

				$shippingState = utf8_decode($myOrder->getShippingAddress()->getRegion());
				$shippingZip = utf8_decode($myOrder->getShippingAddress()->getPostcode());


				$myOrder->loadByIncrementId($myOrder->getIncrementId());
				
				$store = Mage::app()->getStore();
				$items = $myOrder->getItemsCollection();
				$ic=1;
				$countitems=0;

				$customer_email = "";
				if($custoer_id = $myOrder->getCustomerId()){
					$customer = Mage::getModel('customer/customer')->load($custoer_id);
					$customer_email = $customer->getEmail();
					$customer_phone = $customer->getPhone();
				}

				if(empty($customer_email)){
					$customer_email=$myOrder->getCustomerEmail();
				}


				$payment = $myOrder->getPayment();
				$paymentAdditionalData = $payment->getAdditionalInformation();

				$paymentType = $paymentAdditionalData['method'];
				switch($paymentType) {
					case 'CC':
						$paymentType = "Credit Card";
						break;

					default:
						//Do Nothing
						break;
				}

//				var_dump(get_class_methods($payment));
//die();
				$item_line="";


				$orderDiscount = $single_order->getDiscountAmount();

				$defaultOrderDiscount = $orderDiscount;

				$itemsCount = 0;
				foreach ($items as $itemId => $item) {
					if($item->getParentItemId()) {
						continue;
					}

					if($item->getQtyToInvoice()!=0):
						$itemOrderQty = $item->getQtyToInvoice();
					else:
						$itemOrderQty = round($item->getQtyOrdered());
					endif;

					$_productId = $item->getProductId();
					$productModel = Mage::getModel('catalog/product');
					$productModel->load($_productId);

					$currentProductPrice = $productModel->getFinalPrice();
//var_dump($productModel->getData());

					$itemPrice = $item->getPrice();

					$originalPrice = $currentProductPrice;

					$productDiscount = round($originalPrice - $itemPrice, 2) * $itemOrderQty;

					$orderDiscount += $productDiscount;

					++$itemsCount;
				}

				$itemIdx = 0;
				foreach ($items as $itemId => $item){

					if($item->getParentItemId()) {
						continue;
					}

					if($item->getQtyToInvoice()!=0):
						$itemOrderQty = $item->getQtyToInvoice();
					else:
						$itemOrderQty = round($item->getQtyOrdered());
					endif;

					$itemPrice = $item->getPrice();

					$_productId = $item->getProductId();
					$productModel = Mage::getModel('catalog/product');
					$productModel->load($_productId);
					$currentProductPrice = $productModel->getFinalPrice();
					$originalPrice = $currentProductPrice;
//					$originalPrice = $item->getOriginalPrice();
					$productDiscount = round($originalPrice - $itemPrice, 2) * $itemOrderQty;

//var_dump($currentProductPrice);

/*
					$originalprice = $item->getOriginalPrice();
					$subtotal = ($item->getOriginalPrice()*$itemorderqty);
					$discountamount=0;
					if(round($item->getDiscountAmount())!=0){
						$discountamount=$item->getDiscountAmount();
						$subtotal=($subtotal-$discountamount);
					}
					$subtotal = number_format($subtotal,2);
					$eachitemdiscountamount = ($discountamount/$itemorderqty);
					$discountamount = number_format($eachitemdiscountamount,2);
					$taxpercent = $item->getTaxPercent();
					$eachitemvat = $vatamount_eachproduct/$itemorderqty;
					$totalvatdisamount = $eachitemvat+$eachitemdiscountamount;
					$net_price = round($originalprice-($totalvatdisamount),2);
*/


					$rowArr = array();
					$rowArr[] = $createdAt->format("m/d/Y H:i"); //created_at 		[0]		[0]
					$rowArr[] = $orderIncrementId; //orderID						[1]		[1]
					$rowArr[] = $invoiceId; //invoiceID								[2]		[2]

					$rowArr[] = $item->getSku();//sku								[3]		[3]
					$rowArr[] = $itemPrice;//itemPrice								[4]		[4]
					$rowArr[] = $originalPrice;//itemOriginalPrice					[5]		[5]

					$rowArr[] = $itemOrderQty; //quantity							[6]		[6]
					$rowArr[] = $single_order->getCustomerFirstname(); //firstname	[7]		[7]
					$rowArr[] = $single_order->getCustomerLastname(); //lastname	[9]		[8]

//					$rowArr[] = $address; //billingAddress							[10]
//					$rowArr[] = $city; //billingCity								[11]
					$rowArr[] = $region; //billingState								[12]	[9]
					$rowArr[] = $postcode; //billingZip								[13]	[10]
					$rowArr[] = $country; //billingCountry							[14]	[11]
					$rowArr[] = $customer_email; //billingEmail						[15]	[12]
//					$rowArr[] = $phone; //billingPhone								[16]

					$rowArr[] = $single_order->getSubtotal(); //subtotal			[17]	[13]
					$rowArr[] = $single_order->getShippingAmount(); //shippingAmount[18]	[14]
					$rowArr[] = $single_order->getTaxAmount(); //taxAmount			[19]	[15]
					$rowArr[] = $single_order->getBaseTotalInvoicedCost(); //cost	[20]	[16]
					$rowArr[] = $single_order->getStatus(); //orderStatus			[21]	[17]

					$rowArr[] = $payment->getCcType(); //creditCardType				[22]	[18]
					$rowArr[] = $paymentType; //paymentMethod						[23]	[19]

					$rowArr[] = $shippingCreatedAt; //shippingCreatedAt				[24]	[20]
					$rowArr[] = $shippingState; //shippingState						[25]	[21]
					$rowArr[] = $shippingZip; //shippingZip							[25]	[22]

					$rowArr[] = -$orderDiscount; //orderDiscount						[26]	[23]

					$rowArr[] = (($itemsCount - 1) == $itemIdx ? $rowArr[14] : 0); // S&amp;H		[27]	[24]
					$rowArr[] = (($itemsCount - 1) == $itemIdx ? $rowArr[15] : 0); // Tax			[28]	[25]
					$rowArr[] = -($productDiscount + (($itemsCount - 1) == $itemIdx ? $defaultOrderDiscount : 0)); // Discount		[29]	[26]
					$rowArr[] = $rowArr[6] * $rowArr[4]; // Subtotal								[30]	[27]

					$rowArr[] = $rowArr[24] + $rowArr[25] + $rowArr[26] + $rowArr[27]; // Total		[31]	[28]

					$rowArr[] = $originalPrice * $itemOrderQty; // Product									[29]
					$rowArr[] = $rowArr[29] + $rowArr[26] + $rowArr[25] + $rowArr[24]; // Invoice Total		[30]

//					continue;




					/*
                    $headArr = array(
                        "", "Discount", "Subtotal", "Total", );
    */


/*
					$datarow =  array(date("d/m/Y",strtotime($myOrder->getCreatedAt())), $myOrder->getIncrementId(), utf8_decode($item->getName()), $itemorderqty, utf8_decode($net_price),$subtotal,$name,$customer_email,$address,$city,$region,$postcode,$country);
								
					$line = "";
					$comma = "";
					foreach($datarow as $titlename) {
						$line .= $comma . str_replace(array(','),array(""), $titlename);
						$comma = ",";
					}

					$line .= "\n";
					
					$orders_csv_row .=$line;
*/
					$csv .= "\"".implode("\",\"", $rowArr)."\"\r\n";

					++$itemIdx;
									
				}
			}
			
			$reportname = Mage::helper('salesreport')->getReportName();
			$fileName   = $reportname.'.csv';
//			$this->_sendUploadResponse($fileName, $orders_csv_row);
			$this->_sendUploadResponse($fileName, $csv);
		}
    }
	
	protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
	
	
}