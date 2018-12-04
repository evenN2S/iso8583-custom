<?php

require dirname(__FILE__) . '/../../vendor/autoload.php';

use ISO8583\Protocol;
use ISO8583\CustomMessage;

$output = [];

# PACK
$iso = new CustomMessage(new Protocol, [
    'mode' => CustomMessage::MODE_HPDH,
]);

$iso->setMTI('0400');
$iso->setTPDU('6000090000');
$iso->setField(2, '4863995550001423');
$iso->setField(3, '000000');
$iso->setField(4, str_pad(150000, 12, 0, STR_PAD_LEFT));
$iso->setField(11, '000063');
$iso->setField(14, '2007');
$iso->setField(22, '0051');
$iso->setField(24, '0009');
$iso->setField(25, '00');
$iso->setField(41, '98709803');
$iso->setField(42, '987013001300001');
$iso->setField(62, '000002');
$iso->setField(64, hex2bin('0000000000000000'));

$packed = $iso->pack();

# DEBUG
$output[] = "# PACKING `sales_reversal` REQUEST";
$output[] = "- ISO MESSAGE  : " . $packed;
$output[] = "- LENGTH       : " . $iso->getLengthString();
$output[] = "- TPDU         : " . $iso->getTPDU();
$output[] = "- MTI          : " . $iso->getMTI();
$output[] = "- BITMAP       : " . $iso->getBitmapString();
$output[] = "- BITS         : " . json_encode($iso->getFieldsIds());
$output[] = "- BIT[2]       : " . $iso->getField(2);
$output[] = "- BIT[3]       : " . $iso->getField(3);
$output[] = "- BIT[4]       : " . $iso->getField(4);
$output[] = "- BIT[11]      : " . $iso->getField(11);
$output[] = "- BIT[14]      : " . $iso->getField(14);
$output[] = "- BIT[22]      : " . $iso->getField(22);
$output[] = "- BIT[24]      : " . $iso->getField(24);
$output[] = "- BIT[25]      : " . $iso->getField(25);
$output[] = "- BIT[41]      : " . $iso->getField(41);
$output[] = "- BIT[42]      : " . $iso->getField(42);
$output[] = "- BIT[62]      : " . $iso->getField(62);
$output[] = "- BIT[64]      : " . strtoupper(bin2hex($iso->getField(64)));

$output[] = null;

# UNPACK
$iso = new CustomMessage(new Protocol, [
    'mode' => CustomMessage::MODE_HPDH,
]);

$packed = "003860000900000410303801000A8000000000000000001500000000631024571128000932383131313830303030363330303938373039383033";
$unpacked = $iso->unpack($packed);

# DEBUG
$output[] = "# UNPACKING `sales_reversal` RESPONSE";
$output[] = "- ISO MESSAGE  : " . $packed;
$output[] = "- LENGTH       : " . $iso->getLengthString();
$output[] = "- TPDU         : " . $iso->getTPDU();
$output[] = "- MTI          : " . $iso->getMTI();
$output[] = "- BITMAP       : " . $iso->getBitmapString();
$output[] = "- BITS         : " . json_encode($iso->getFieldsIds());
$output[] = "- BIT[3]       : " . $iso->getField(3);
$output[] = "- BIT[4]       : " . $iso->getField(4);
$output[] = "- BIT[11]      : " . $iso->getField(11);
$output[] = "- BIT[12]      : " . $iso->getField(12);
$output[] = "- BIT[13]      : " . $iso->getField(13);
$output[] = "- BIT[24]      : " . $iso->getField(24);
$output[] = "- BIT[37]      : " . $iso->getField(37);
$output[] = "- BIT[39]      : " . $iso->getField(39);
$output[] = "- BIT[41]      : " . $iso->getField(41);

echo implode(PHP_EOL, $output);