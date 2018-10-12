<?php

require 'vendor/autoload.php';

use ISO8583\Protocol;
use ISO8583\Message;

$iso = new Protocol();
$message = new Message($iso, [
    'lengthPrefix' => 4
]);

// Unpacking message
$unpack = $message->unpack('02880200F23E4491A8E08020040000000000000016557420000000210000000000000000100009142259180001552359180914020109140000020000000000000600000206000002345574200000002100=02017650000000000PCZ25700015502117986000000000020553LOCATION2              WELWYN GARDEN01GB8260280000456445|0000|PCZ257000155301');

var_dump($message->getLengthString());
var_dump($message->getMti());
var_dump($message->getBitmapString());
var_dump($message->getFields());