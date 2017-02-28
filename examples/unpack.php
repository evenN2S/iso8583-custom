<?php

require 'vendor/autoload.php';

use ISO8583\Protocol;
use ISO8583\Message;

$iso = new Protocol();
$message = new Message($iso, [
    'lengthPrefix' => 5
]);

// Unpacking message
$unpack = $message->unpack('303032393030323030f23e4491a8e0802000000000000000203136353537342a2a2a2a2a2a2a2a33343533303030303030303030303030303031303030313231323134353430383030303030393134353430383132313231373033313231333030303039303230304330303030303030303036303030303230303630303030323033372a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a2a504652333437303030303039323837353937353830303030303030303030333039333733303134373430342054657374204167656e74203220204861746669656c64202020202048654742383236303238303030303030323136317c303030307c504652333437303030303039303135353630313031323634303243313031');

var_dump($message->getBitmap());
var_dump($message->getMti());
var_dump($message->getFields());