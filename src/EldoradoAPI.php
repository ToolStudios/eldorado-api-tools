<?php
// Eldorado.net API
// written by Rafiq Premji in 2014
// https://github.com/PrajnaAvidya
class EldoradoAPI
{
  // Eldorado account ID/API key
  protected $account_id;
  protected $api_key;

  // debug status - enable for verbose mode
  public $debug = false;

  // API endpoints - must match method names
  protected $endpoints = array(
    'getIP'=>'https://eldoradopartner.com/get_ip/',
    'quantityCheck'=>'https://eldoradopartner.com/quantitycheck/',
    'checkShipping'=>'https://eldoradopartner.com/shipping_updates/',
    'discountInformation'=>'https://eldoradopartner.com/discounts/',
    'orderHistory'=>'https://eldoradopartner.com/order_history/',
    'openOrders'=>'https://eldoradopartner.com/open_orders/',
    'placeOrder'=>'https://eldoradopartner.com/order/',
  );

  // constructor. enable debug to see verbose output.
  public function __construct($account_id, $api_key, $debug=false)
  {
    // set account info
    $this->account_id = $account_id;
    $this->api_key = $api_key;

    // set debug status
    $this->debug = $debug;
  }

  // retrieve your actual outgoing IP address from eldorado
  // this can vary from your incoming/reported IP depending on your hosting company
  // this is the IP you want to use for an API key from Eldorado
  public function getIP()
  {
    // return static API call result
    return file_get_contents($this->endpoints[__FUNCTION__]);
    // NOTE: if your hosting provider doesn't allow file_get_contents, remove
    // the line above and uncomment the line below
    //// return URLUtils::get($this->endpoints[__FUNCTION__]);
  }

  // check quantity for $product_sku
  public function quantityCheck($product_sku)
  {
    // build xml
    $xmlcall =
      '<key>'.$this->api_key.'</key>'.
      '<item>'.$product_sku.'</item>';

    // make api call and return result
    return $this->etc_call($xmlcall, $this->endpoints[__FUNCTION__]);
  }

  // check shipping status for $order_id
  public function checkShipping($order_id)
  {
    // build xml
    $xmlcall =
      '<key>'.$this->api_key.'</key>'.
      '<XML_Orders>'.
        '<Order>'.
          '<Order_id>'.$order_id.'</Order_id>'.
          '<Order_customer>'.$this->account_id.'</Order_customer>'.
        '</Order>'.
      '</XML_Orders>';

    // make api call and return result
    return $this->etc_call($xmlcall, $this->endpoints[__FUNCTION__]);
  }

  // discount information associated with account/key
  public function discountInformation()
  {
    // build xml
    $xmlcall =
      '<key>'.$this->api_key.'</key>'.
      '<accountId>'.$this->account_id.'</accountId>';

    // make api call and return result
    return $this->etc_call($xmlcall, $this->endpoints[__FUNCTION__]);
  }

  // order history associated with account/key
  // $type can be api or website and the call returns orders placed using the specified method
  public function orderHistory($type='api')
  {
    // build xml
    $xmlcall =
      '<key>'.$this->api_key.'</key>'.
      '<AccountId>'.$this->account_id.'</AccountId>'.
      '<type>'.$type.'</type>';

    // make api call and return result
    return $this->etc_call($xmlcall, $this->endpoints[__FUNCTION__]);
  }

  // open orders associated with account/key
  public function openOrders()
  {
    // build xml
    $xmlcall =
      '<key>'.$this->api_key.'</key>'.
      '<AccountId>'.$this->account_id.'</AccountId>';

    // make api call and return result
    return $this->etc_call($xmlcall, $this->endpoints[__FUNCTION__]);
  }

  // place order using $order_data (stdobject) for $products (array of stdobjects)
  // this function is LIVE - notify Eldorado before making test orders or they will be fulfilled!
  public function placeOrder($order_data, $products, $ship_code='M02')
  {
    // clean up zip code/phone number
    $zip_code = preg_replace("/[^0-9]/","",$order_data->ZipCode);
    $phone_number = preg_replace("/[^0-9]/","",$order_data->PhoneNumber);
    
    // check for special instructions
    if (isset($order_data->SpecialInstructions)) {
      $instructions = $order_data->SpecialInstructions;
    } else {
      $instructions = '';
    }
    
    // check for address line2
    if (isset($order_data->AddressLine2)) {
      $address2 = $order_data->AddressLine2;
    } else {
      $address2 = '';
    }

    // format XML call
    $xmlcall =
      '<key>'.$this->api_key.'</key>'.
      '<AccountId>'.$this->account_id.'</AccountId>'.
      '<Name>'.$order_data->Name.'</Name>'.
      '<AddressLine1>'.$order_data->AddressLine1.'</AddressLine1>'.
      '<AddressLine2>'.$address2.'</AddressLine2>'.
      '<City>'.$order_data->City.'</City>'.
      '<StateCode>'.$order_data->StateCode.'</StateCode>'.
      '<ZipCode>'.$zip_code.'</ZipCode>'.
      '<CountryCode>'.$order_data->CountryCode.'</CountryCode>'.
      '<PhoneNumber>'.$phone_number.'</PhoneNumber>'.
      '<EnteredByCode>API</EnteredByCode>'.
      '<SourceCode>API</SourceCode>'.
      '<CustPONumber>'.$order_data->SourceOrderNumber.'</CustPONumber>'.
      '<ShipVia>'.strtoupper($ship_code).'</ShipVia>'.
      '<SpecialInstructions>'.$instructions.'</SpecialInstructions>'.
      '<SourceOrderNumber>'.$order_data->SourceOrderNumber.'</SourceOrderNumber>';

    // add products to XML
    $product_xml = '';
    foreach ($products as $product)
    {
      $product_xml .= '<Product><Code>'.$product->Sku.'</Code><Quantity>'.$product->Quantity.'</Quantity></Product>';
    }
    $xmlcall .= '<Products>'.$product_xml.'</Products>';

    // make api call and return result
    return $this->etc_call($xmlcall, $this->endpoints[__FUNCTION__]);
  }

  // make a curl post call with $xml to $url and then convert the result to an object
  protected function etc_call($xml, $url)
  {
    // initialize curl
    if (!$ch = curl_init())
    {
      die("Could not initialize cURL session.");
    }

    // set curl options
    curl_setopt($ch, CURLOPT_URL,            $url);
    curl_setopt($ch, CURLOPT_POST,            1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT,        4);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

    // execute
    $result = curl_exec($ch);

    // show debug info (if enabled)
    if ($this->debug)
    {
      var_dump($result);
    }

    // return result as object
    return simplexml_load_string($result);
  }
}
