<?php

require dirname(__FILE__) . '/../../vendor/autoload.php';

use ISO8583\Protocol;
use ISO8583\CustomMessage;
use phpseclib\Crypt\DES;

$output = [];

$keys = [
    'ZMK' => '10213A4B5C6D7E8F',
    'ZPKClear' => '0101010101010101',
    'ZPK' => null,
    'KCV' => null,
];

$cipher = new DES(DES::MODE_ECB);
$cipher->paddable = false;

# GENERATE ZPK
$cipher->setKey(hex2bin($keys['ZMK']));
$keys['ZPK'] = bin2hex($cipher->encrypt(hex2bin($keys['ZPKClear'])));

# GENERATE KCV
$cipher->setKey(hex2bin($keys['ZPKClear']));
$keys['KCV'] = str_pad(substr(bin2hex($cipher->encrypt(hex2bin('0000000000000000'))), 0, 6), 16, '0', STR_PAD_RIGHT);

$keys = array_map('strtoupper', $keys);

$bit48 = str_pad($keys['ZPK'] . $keys['KCV'], 64, '0', STR_PAD_RIGHT);

# PACK
$iso = new CustomMessage(new Protocol, [
    'mode' => CustomMessage::MODE_HPDH,
]);

$iso->setMTI('0800');
$iso->setTPDU('6000000000');
$iso->setField(7, "1204092457");
$iso->setField(11, '000001');
$iso->setField(15, "1204");
$iso->setField(48, $bit48);
$iso->setField(70, '0101');

$packed = $iso->pack();

# DEBUG
$output[] = "# PACKING `key_exchange` REQUEST";
$output[] = "- ISO MESSAGE  : " . $packed;
$output[] = "- LENGTH       : " . $iso->getLengthString();
$output[] = "- TPDU         : " . $iso->getTPDU();
$output[] = "- MTI          : " . $iso->getMTI();
$output[] = "- BITMAP       : " . $iso->getBitmapString();
$output[] = "- BITS         : " . json_encode($iso->getFieldsIds());
$output[] = "- BIT[7]       : " . $iso->getField(7);
$output[] = "- BIT[11]      : " . $iso->getField(11);
$output[] = "- BIT[15]      : " . $iso->getField(15);
$output[] = "- BIT[48]      : " . $iso->getField(48);
$output[] = "- BIT[70]      : " . $iso->getField(70);
$output[] = null;

# UNPACK
$iso = new CustomMessage(new Protocol, [
    'mode' => CustomMessage::MODE_HPDH,
]);

$packed = "00236000000000081082200000020000000400000000000000120409245700000130300101";
$unpacked = $iso->unpack($packed);

# DEBUG
$output[] = "# UNPACKING `key_exchange` RESPONSE";
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