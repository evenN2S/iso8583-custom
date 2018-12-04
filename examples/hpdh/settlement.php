<?php

require dirname(__FILE__) . '/../../vendor/autoload.php';

use ISO8583\Protocol;
use ISO8583\CustomMessage;

$output = [];

# PACK
$iso = new CustomMessage(new Protocol, [
    'mode' => CustomMessage::MODE_HPDH,
]);

$iso->setMTI('0500');
$iso->setTPDU('6000100000');
$iso->setField(3, '920000');
$iso->setField(11, '000037');
$iso->setField(24, '0010');
$iso->setField(41, '60060060');
$iso->setField(42, '600600600600600');
$iso->setField(60, '000001');
$iso->setField(63, '001000000050000');

$packed = $iso->pack();

# DEBUG
$output[] = "# PACKING `settlement` REQUEST";
$output[] = "- ISO MESSAGE  : " . $packed;
$output[] = "- LENGTH       : " . $iso->getLengthString();
$output[] = "- TPDU         : " . $iso->getTPDU();
$output[] = "- MTI          : " . $iso->getMTI();
$output[] = "- BITMAP       : " . $iso->getBitmapString();
$output[] = "- BITS         : " . json_encode($iso->getFieldsIds());
$output[] = "- BIT[3]       : " . $iso->getField(3);
$output[] = "- BIT[11]      : " . $iso->getField(11);
$output[] = "- BIT[24]      : " . $iso->getField(24);
$output[] = "- BIT[41]      : " . $iso->getField(41);
$output[] = "- BIT[42]      : " . $iso->getField(42);
$output[] = "- BIT[60]      : " . $iso->getField(60);
$output[] = "- BIT[63]      : " . $iso->getField(63);

$output[] = null;

# UNPACK
$iso = new CustomMessage(new Protocol, [
    'mode' => CustomMessage::MODE_HPDH,
]);

$packed = "0046600010000005102038010002C0000292000000003709451011220010393536303036303036303630303630303630303630303630300015303031303030303030303530303030";
$unpacked = $iso->unpack($packed);

# DEBUG
$output[] = "# UNPACKING `settlement` RESPONSE";
$output[] = "- ISO MESSAGE  : " . $packed;
$output[] = "- LENGTH       : " . $iso->getLengthString();
$output[] = "- TPDU         : " . $iso->getTPDU();
$output[] = "- MTI          : " . $iso->getMTI();
$output[] = "- BITMAP       : " . $iso->getBitmapString();
$output[] = "- BITS         : " . json_encode($iso->getFieldsIds());
$output[] = "- BIT[3]       : " . $iso->getField(3);
$output[] = "- BIT[11]      : " . $iso->getField(11);
$output[] = "- BIT[12]      : " . $iso->getField(12);
$output[] = "- BIT[13]      : " . $iso->getField(13);
$output[] = "- BIT[24]      : " . $iso->getField(24);
$output[] = "- BIT[39]      : " . $iso->getField(39);
$output[] = "- BIT[41]      : " . $iso->getField(41);
$output[] = "- BIT[42]      : " . $iso->getField(42);
$output[] = "- BIT[63]      : " . $iso->getField(63);

echo implode(PHP_EOL, $output);