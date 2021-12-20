<?php

use \VIISON\AddressSplitter\AddressSplitter;
use \VIISON\AddressSplitter\Exceptions\SplittingException;

$address = SdkRestApi::getParam('address');
$addressSplitter = new AddressSplitter();
$splitAddress = $addressSplitter->splitAddress($address);
$houseNumber = $splitAddress['houseNumberParts']['base'];
if (isset($splitAddress['houseNumberParts']['extension'])) {
    $houseNumber .= " " . $splitAddress['houseNumberParts']['extension'];
}
return [
    "address1" => $splitAddress['streetName'] . " " . $splitAddress['additionToAddress1'],
    "address2" => $houseNumber,
    "address3" => $splitAddress['additionToAddress2'],
];
