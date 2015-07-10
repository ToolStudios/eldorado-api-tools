<?php
// runtime settings
error_reporting(E_ALL);
ini_set("display_errors", 1);

// fill in config-sample.php and rename to config.php before use
require_once('config.php');
// eldorado api class
require_once('EldoradoAPI.php');
// use URLUtils if your hosting provider doesn't allow the use of
// file_get_contents (this is only used for the getIP method)
////require_once('class.URLUtils.php');

// create an API connection
$api = new EldoradoAPI(ELDORADO_ACCOUNT_ID, ELDROADO_API_KEY);

// WARNING: These calls are going to the LIVE Eldorado API server!
// If you place an order it will be fulfilled unless you let them know you are testing the API!!

// get outgoing IP address - used for Eldorado API key
$ip = $api->getIP();
echo "Your Outgoing IP Address:\n";
var_dump($ip);

// get discount info for account/key
$discount = $api->discountInformation();
echo "Discount Information:\n";
var_dump($discount);

// quantity check for $product_sku
$product_sku = '1018';
$quantity = $api->quantityCheck($product_sku);
echo "Quantity for $product_sku:\n";
var_dump($quantity);

// order history for account/key
$history = $api->orderHistory('website'); // argument can be "website" or "api"
echo "Order History:\n";
var_dump($history);

// open orders for account/key
$orders = $api->openOrders();
echo "Open Orders:\n";
var_dump($orders);

// check shipping for $order_id
$order_id = '12345';
$shipping = $api->checkShipping($order_id);
echo "Shipping Status for $order_id:\n";
var_dump($shipping);


////// TEST ORDER

// sample order data
$order_data = new stdClass;
$order_data->Name = 'Test Customer'; // required - customer name
$order_data->AddressLine1 = '123 Address St.'; // required - shipping address line 1
$order_data->AddressLine2 = ''; // optional - shipping address line 2
$order_data->City = 'Broomfield'; // required - city
$order_data->StateCode = 'CO'; // required - 2 or 3 digit state/province code
$order_data->ZipCode = '12345'; // required - zip/postal code (numbers only, up to 10 digits)
$order_data->CountryCode = 'US'; // required - country code (see API documentation appendix)
$order_data->PhoneNumber = '3034445555'; // required - phone number (numbers only)
$order_data->SourceOrderNumber = '123'; // required - unique order PO number (numbers only)
$order_data->SpecialInstructions = ''; // optional - special instructions

// sample order products
$product1 = new stdClass;
$product1->Sku = '1018';
$product1->Quantity = '1';
$product2 = new stdClass;
$product2->Sku = '1019';
$product2->Quantity = '2';

// shipping code (see API documentation appendix)
$ship_code = 'M02';

// uncomment the following lines to make a test order
// this is a LIVE function, please inform eldorado before making test orders!
/*
$order = $api->placeOrder($order_data, [$product1, $product2], $ship_code);
echo "Placing Test Order:\n";
var_dump($order);
*/

echo "Tests Complete.\n";
