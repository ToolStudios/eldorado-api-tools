##Eldorado.net API Class
- Provided as an entry point and example for integrating with the eldorado.net API.
- Please direct all billing and account inquiries directly to eldorado.net.
- Disclaimer: These files are provided for example and testing purposes only. You are working with a live API and need to inform eldorado of test orders/etc. Class methods do not validate input and return results as a raw XML object.

####Examples:
```php
// create an API connection with debug mode enabled
$api = new EldoradoAPI(ELDORADO_ACCOUNT_ID, ELDROADO_API_KEY, true);

// get outgoing IP address - used for Eldorado API key
$ip = $api->getIP();

// get discount info for account/key
$discount = $api->discountInformation();

// quantity check
$quantity = $api->quantityCheck('1019');

// order history
$history = $api->orderHistory('website'); // argument can be "website" or "api"

// open orders for account/key
$orders = $api->openOrders();

// check shipping
$shipping = $api->checkShipping('12345');

// Make an order:
// order data
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

// order products
$product1 = new stdClass;
$product1->Sku = '1018';
$product1->Quantity = '1';
$product2 = new stdClass;
$product2->Sku = '1019';
$product2->Quantity = '2';

// products must be in an array
$products = [$product1, $product2];

// shipping code -- see Eldorado API documentation for more information
$shipcode = 'M02';

// place order
$order = $api->placeOrder($order_data, $products, $shipcode);
```
