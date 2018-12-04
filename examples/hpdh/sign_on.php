<?php

require dirname(__FILE__) . '/../../vendor/autoload.php';

use ISO8583\Protocol;
use ISO8583\CustomMessage;

$output = [];

# PACK
$iso = new CustomMessage(new Protocol, [
    'mode' => CustomMessage::MODE_HPDH,
]);

$iso->setMTI('0800');
$iso->setTPDU('6000000000');
$iso->setField(7, "1204092457");
$iso->setField(11, '000001');
$iso->setField(70, '0001');

$packed = $iso->pack();

# DEBUG
$output[] = "# PACKING `sign_on` REQUEST";
$output[] = "- ISO MESSAGE  : " . $packed;
$output[] = "- LENGTH       : " . $iso->getLengthString();
$output[] = "- TPDU         : " . $iso->getTPDU();
$output[] = "- MTI          : " . $iso->getMTI();
$output[] = "- BITMAP       : " . $iso->getBitmapString();
$output[] = "- BITS         : " . json_encode($iso->getFieldsIds());
$output[] = "- BIT[7]       : " . $iso->getField(7);
$output[] = "- BIT[11]      : " . $iso->getField(11);
$output[] = "- BIT[70]      : " . $iso->getField(70);
$output[] = null;

# UNPACK
$iso = new CustomMessage(new Protocol, [
    'mode' => CustomMessage::MODE_HPDH,
]);

$packed = "00236000000000081082200000020000000400000000000000120409245700000130300001";
$unpacked = $iso->unpack($packed);

# DEBUG
$output[] = "# UNPACKING `sign_on` RESPONSE";
$output[] = "- ISO MESSAGE  : " . $packed;
$output[] = "- LENGTH       : " . $iso->getLengthString();
$output[] = "- TPDU         : " . $iso->getTPDU();
$output[] = "- MTI          : " . $iso->getMTI();
$output[] = "- BITMAP       : " . $iso->getBitmapString();
$output[] = "- BITS         : " . json_encode($iso->getFieldsIds());
$output[] = "- BIT[7]       : " . $iso->getField(7);
$output[] = "- BIT[11]      : " . $iso->getField(11);
$output[] = "- BIT[39]      : " . $iso->getField(39);
$output[] = "- BIT[70]      : " . $iso->getField(70);

echo implode(PHP_EOL, $output);