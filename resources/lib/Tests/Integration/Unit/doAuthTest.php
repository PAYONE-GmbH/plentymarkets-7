<?php

namespace Payone\Tests\Integration\Unit;

use Payone\Tests\Helpers\Config;

/**
 * Class doPreAuthTest
 */
class doAuthTest extends doRequestAbstract
{
    public function __construct()
    {
        parent::__construct();
        $configData = Config::getConfig()['api_context'];

        $uniqueTransactionId = uniqid();
        $this->payloadJson = <<<JSON
{
   "context":{
      "aid": "{$configData['aid']}",
      "mid": "{$configData['mid']}",
      "portalid": "{$configData['portalid']}",
      "key": "{$configData['key']}",
      "mode":"test"
   },
   "basket":{
      "id":"{$uniqueTransactionId}",
      "sessionId":"ff12cae0696f6a8ea0362edca7786824560f7ffe",
      "orderId":null,
      "customerId":null,
      "customerShippingAddressId":null,
      "currency":"EUR",
      "referrerId":0,
      "shippingCountryId":1,
      "methodOfPaymentId":7026,
      "shippingProviderId":101,
      "shippingProfileId":6,
      "itemSum":99,
      "itemSumNet":8319,
      "basketAmount":10399,
      "basketAmountNet":8739,
      "shippingAmount":499,
      "shippingAmountNet":419,
      "paymentAmount":0,
      "couponCode":"",
      "couponDiscount":0,
      "shippingDeleteByCoupon":false,
      "basketRebate":0,
      "maxFsk":0,
      "orderTimestamp":null,
      "createdAt":"2017-04-18T09:43:31+02:00",
      "updatedAt":"2017-04-18T09:43:59+02:00",
      "basketRebateType":0,
      "customerInvoiceAddressId":20,
      "grandTotal":10399,
      "cartId":7544
   },
   "basketItems":[
      {
         "id":63,
         "basketId":7544,
         "sessionId":"ff12cae0696f6a8ea0362edca7786824560f7ffe",
         "orderRowId":null,
         "quantity":1,
         "quantityOriginally":1,
         "itemId":108,
         "unitCombinationId":1,
         "attributeValueSetId":0,
         "rebate":0,
         "vat":19,
         "price":99,
         "givenPrice":0,
         "useGivenPrice":false,
         "inputWidth":null,
         "inputLength":null,
         "inputHeight":null,
         "itemType":null,
         "externalItemId":null,
         "noEditByCustomer":false,
         "costCenterId":0,
         "giftPackageForRowId":0,
         "position":0,
         "size":"",
         "shippingProfileId":0,
         "referrerId":1,
         "deliveryDate":null,
         "categoryId":null,
         "reservationDatetime":"2017-04-18 09:43:50",
         "variationId":1006,
         "bundleVariationId":0,
         "createdAt":"2017-04-18T09:43:50+02:00",
         "updatedAt":"2017-04-18T09:43:50+02:00",
         "tax":"15.81",
         "name":"Barsessel Black Mamba"
      }
   ],
   "shippingAddress":{
      "id":20,
      "name1":"arvatis media GmbH",
      "name2":"Joachim",
      "name3":"Dr\u00fcgg",
      "name4":"namenszusatz",
      "address1":"Kopernikusstra\u00df",
      "address2":"28",
      "address3":" ADRESSZUSATZ1",
      "address4":" ADRESSZUSATZ2",
      "postalCode":"40223",
      "town":"D\u00fcsseldorf",
      "countryId":1,
      "stateId":10,
      "readOnly":false,
      "searchName":"arvatis media GmbH Joachim Druegg namenszusatz",
      "searchAddress":"Kopernikusstrass 28  ADRESSZUSATZ1  ADRESSZUSATZ2",
      "checkedAt":null,
      "createdAt":"2017-04-12T11:26:09+02:00",
      "updatedAt":"2017-04-12T11:26:09+02:00",
      "options":[
         {
            "id":44,
            "addressId":20,
            "typeId":5,
            "value":"heinen@arvatis.com",
            "position":0,
            "createdAt":"2017-04-12T11:26:09+02:00",
            "updatedAt":"2017-04-12T11:26:09+02:00"
         }
      ],
      "city":"D\u00fcsseldorf",
      "postCode":"40223",
      "country":"DE",
      "company":"arvatis media GmbH",
      "firstname":"Joachim",
      "lastname":"Dr\u00fcgg",
      "street":"Kopernikusstra\u00df",
      "lang":"de",
      "houseNumber":"28",
      "addressaddition":""
   },
   "billingAddress":{
      "id":20,
      "name1":"arvatis media GmbH",
      "name2":"Joachim",
      "name3":"Dr\u00fcgg",
      "name4":"namenszusatz",
      "address1":"Kopernikusstra\u00df",
      "address2":"28",
      "address3":" ADRESSZUSATZ1",
      "address4":" ADRESSZUSATZ2",
      "postalCode":"40223",
      "town":"D\u00fcsseldorf",
      "countryId":1,
      "stateId":10,
      "readOnly":false,
      "searchName":"arvatis media GmbH Joachim Druegg namenszusatz",
      "searchAddress":"Kopernikusstrass 28  ADRESSZUSATZ1  ADRESSZUSATZ2",
      "checkedAt":null,
      "createdAt":"2017-04-12T11:26:09+02:00",
      "updatedAt":"2017-04-12T11:26:09+02:00",
      "options":[
         {
            "id":44,
            "addressId":20,
            "typeId":5,
            "value":"heinen@arvatis.com",
            "position":0,
            "createdAt":"2017-04-12T11:26:09+02:00",
            "updatedAt":"2017-04-12T11:26:09+02:00"
         }
      ],
      "city":"D\u00fcsseldorf",
      "postCode":"40223",
      "country":"DE",
      "company":"arvatis media GmbH",
      "firstName":"Joachim",
      "lastName":"Dr\u00fcgg",
      "street":"Kopernikusstra\u00df",
      "lang":"de",
      "houseNumber":"28",
      "addressaddition":""
   },
   "customer":{
      "email":"whitelist-test@payone.com",
      "firstname":"Joachim",
      "lastname":"Dr\u00fcgg",
      "title":"",
      "birthday":"1970-01-01",
      "telephonenumber":"",
      "language":"de",
      "ip":"127.0.0.1",
      "customerId":1,
      "registrationDate":"1970-01-01",
      "group":"default",
      "company":"arvatis media GmbH",
      "gender":""
   },
   "paymentMethod":"PrePayment",
   "referenceId":"8a82944a5b9081a5015ba90e1f213f41",
   "systemInfo":{
      "vendor":"arvatis media GmbH",
      "version":7,
      "type":"Webshop",
      "url":"https:\/\/arvatis.plentymarkets-cloud01.com\/",
      "module":"plentymarkets 7 Payone plugin",
      "module_version":1
   }
}
JSON;
    }

    /**
     * @group online
     * @group capture
     *
     * @return array
     */
    public function testDoAuthSuccessful()
    {
        $response = require getcwd() . '/resources/lib/doAuth.php';

        self::assertTrue($response['success'], 'Response was: ' . print_r($response, true));
        self::assertTrue(isset($response['clearing']), 'Response was: ' . print_r($response, true));

        return $response;
    }

    /**
     * @group online
     * @group capture
     *
     * @return array
     */
    public function testAuthErrorWithDifferentAddress()
    {
        $this->payload['billingAddress']['houseNumber'] = 29;
        $this->setPayLoad($this->payload);

        $response = require getcwd() . '/resources/lib/doAuth.php';

        self::assertTrue($response['success'], 'Response was: ' . print_r($response, true));

        return $response;
    }
}
