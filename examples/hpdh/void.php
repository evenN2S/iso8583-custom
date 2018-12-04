<?php

require dirname(__FILE__) . '/../../vendor/autoload.php';

use ISO8583\Protocol;
use ISO8583\CustomMessage;

$output = [];

# PACK
$iso = new CustomMessage(new Protocol, [
    'mode' => CustomMessage::MODE_HPDH,
]);

$iso->setMTI('0200');
$iso->setTPDU('6000090000');
$iso->setField(2, '4863995550001423');
$iso->setField(3, '020000');
$iso->setField(4, str_pad(500000, 12, 0, STR_PAD_LEFT));
$iso->setField(11, '000062');
$iso->setField(12, '101820');
$iso->setField(13, '1128');
$iso->setField(14, '2007');
$iso->setField(22, '0111');
$iso->setField(24, '0009');
$iso->setField(25, '00');
$iso->setField(37, '000000000061');
$iso->setField(41, '98709803');
$iso->setField(42, '987013001300001');
$iso->setField(55, hex2bin('5F2A0203605F340101820254008407A0000006021010950542000488309A031811289C01009F02060000005000009F03060000000000009F090201009F101C010161008000000A280B4700000000000000000000000000000000009F1A0203609F1E0835313432353834329F2608BB6EFB73D91142189F2701409F3303E0F8C89F34030200009F3501229F360202E79F370437BB15F89F410400000061DF5B0A11000000011100000001'));
$iso->setField(60, '000001');
$iso->setField(62, '000001');
$iso->setField(64, hex2bin('0000000000000000'));

$packed = $iso->pack();

# DEBUG
$output[] = "# PACKING `void` REQUEST";
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
$output[] = "- BIT[14]      : " . $iso->getField(14);
$output[] = "- BIT[22]      : " . $iso->getField(22);
$output[] = "- BIT[24]      : " . $iso->getField(24);
$output[] = "- BIT[25]      : " . $iso->getField(25);
$output[] = "- BIT[37]      : " . $iso->getField(37);
$output[] = "- BIT[41]      : " . $iso->getField(41);
$output[] = "- BIT[42]      : " . $iso->getField(42);
$output[] = "- BIT[55]      : " . strtoupper(bin2hex($iso->getField(55)));
$output[] = "- BIT[60]      : " . $iso->getField(60);
$output[] = "- BIT[62]      : " . $iso->getField(62);
$output[] = "- BIT[64]      : " . strtoupper(bin2hex($iso->getField(64)));

$output[] = null;

# UNPACK
$iso = new CustomMessage(new Protocol, [
    'mode' => CustomMessage::MODE_HPDH,
]);

$packed = "021D60000900000210703801000A810211164863995550001423020000000000500000000062101846112800093030303030303030303036323030393837303938303302864944303133415050524F564544202D2030304E503232385445524D494E414C204944203A2039383730393830333B4D45524348414E54204944203A203938373031333030313330303030313B3B4341524420545950452020203A2044454249543B343836333939585858585858313432332843484950293B3B564F49442053414C453B44415445203A2032382F31312F3230313820202020202054494D45203A2031303A31383A34363B4241544348203A20303030303031202020202020202020205452414345204E4F203A203030303030313B52524546203A20303030303030303030303632202020204150505256203A203032353930393B3B4250303330544F54414C20414D4F554E54203A3B202D525020352E3030302C30303B3B01705F2A0203605F340101820254008407A0000006021010950542000488309A031811289C01009F02060000005000009F03060000000000009F090201009F101C010161008000000A280B4700000000000000000000000000000000009F1A0203609F1E0835313432353834329F2608BB6EFB73D91142189F2701409F3303E0F8C89F34030200009F3501229F360202E79F370437BB15F89F410400000061DF5B0A1100000001110000000100063030303030310000000000000000";
$unpacked = $iso->unpack($packed);

# DEBUG
$output[] = "# UNPACKING `void` RESPONSE";
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
$output[] = "- BIT[24]      : " . $iso->getField(24);
$output[] = "- BIT[37]      : " . $iso->getField(37);
$output[] = "- BIT[39]      : " . $iso->getField(39);
$output[] = "- BIT[41]      : " . $iso->getField(41);
$output[] = "- BIT[48]      : " . $iso->getField(48);
$output[] = "- BIT[55]      : " . strtoupper(bin2hex($iso->getField(55)));
$output[] = "- BIT[60]      : " . $iso->getField(60);
$output[] = "- BIT[64]      : " . strtoupper(bin2hex($iso->getField(64)));

echo implode(PHP_EOL, $output);