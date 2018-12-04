<?php

require dirname(__FILE__) . '/../../vendor/autoload.php';

use ISO8583\Protocol;
use ISO8583\CustomMessage;

$output = [];

# PACK
$iso = new CustomMessage(new Protocol, [
    'mode' => CustomMessage::MODE_HPDH,
]);

$iso->setMTI('0320');
$iso->setTPDU('6000100000');
$iso->setField(2, '5264220612693576');
$iso->setField(3, '000000');
$iso->setField(4, str_pad(1280000, 12, 0, STR_PAD_LEFT));
$iso->setField(11, '000123');
$iso->setField(12, '140347');
$iso->setField(13, '0711');
$iso->setField(22, '0000');
$iso->setField(24, '0010');
$iso->setField(25, '00');
$iso->setField(37, '110718000118');
$iso->setField(41, '60060004');
$iso->setField(42, '600600600600600');
$iso->setField(60, '0200000118110718000118');
$iso->setField(64, hex2bin('0000000000000000'));

$packed = $iso->pack();

# DEBUG
$output[] = "# PACKING `batch_upload` REQUEST";
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
$output[] = "- BIT[12]      : " . $iso->getField(12);
$output[] = "- BIT[13]      : " . $iso->getField(13);
$output[] = "- BIT[22]      : " . $iso->getField(22);
$output[] = "- BIT[24]      : " . $iso->getField(24);
$output[] = "- BIT[25]      : " . $iso->getField(25);
$output[] = "- BIT[37]      : " . $iso->getField(37);
$output[] = "- BIT[41]      : " . $iso->getField(41);
$output[] = "- BIT[42]      : " . $iso->getField(42);
$output[] = "- BIT[60]      : " . $iso->getField(60);
$output[] = "- BIT[64]      : " . strtoupper(bin2hex($iso->getField(64)));

$output[] = null;

# UNPACK
$iso = new CustomMessage(new Protocol, [
    'mode' => CustomMessage::MODE_HPDH,
]);

$packed = "003260001000000330203801000A8000000000000001231403470711001031313037313830303031313830303630303630303034";
$unpacked = $iso->unpack($packed);

# DEBUG
$output[] = "# UNPACKING `batch_upload` RESPONSE";
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
$output[] = "- BIT[37]      : " . $iso->getField(37);
$output[] = "- BIT[39]      : " . $iso->getField(39);
$output[] = "- BIT[41]      : " . $iso->getField(41);

echo implode(PHP_EOL, $output);