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
$iso->setTPDU('6000100000');
$iso->setField(3, '000000');
$iso->setField(4, str_pad(50000, 12, 0, STR_PAD_LEFT));
$iso->setField(11, '000001');
$iso->setField(22, '0051');
$iso->setField(24, '0010');
$iso->setField(25, '00');
$iso->setField(35, '4863995550001423D20072216050010410689F');
$iso->setField(41, '60060060');
$iso->setField(42, '600600600600600');
$iso->setField(48, 'AI014A000000602101000');
$iso->setField(52, hex2bin('5103AFE304A02C54'));
$iso->setField(55, hex2bin('5F2A0203605F340101820254008407A0000006021010950542800480009A031811229C01009F02060000000500009F03060000000000009F101C0101A00080000001431BCC00000000000000000000000000000000009F1A0203609F1E0830313635323739369F2608689BB04E70B50CDA9F2701809F3303E0F8C89F34030200009F3501229F360202CC9F370426F640469F410400000000'));
$iso->setField(60, '000001');
$iso->setField(62, '000036');
$iso->setField(64, hex2bin('0000000000000000'));

$packed = $iso->pack();

# DEBUG
$output[] = "# PACKING `sales` REQUEST";
$output[] = "- ISO MESSAGE  : " . $packed;
$output[] = "- LENGTH       : " . $iso->getLengthString();
$output[] = "- TPDU         : " . $iso->getTPDU();
$output[] = "- MTI          : " . $iso->getMTI();
$output[] = "- BITMAP       : " . $iso->getBitmapString();
$output[] = "- BITS         : " . json_encode($iso->getFieldsIds());
$output[] = "- BIT[3]       : " . $iso->getField(3);
$output[] = "- BIT[4]       : " . $iso->getField(4);
$output[] = "- BIT[11]      : " . $iso->getField(11);
$output[] = "- BIT[22]      : " . $iso->getField(22);
$output[] = "- BIT[24]      : " . $iso->getField(24);
$output[] = "- BIT[25]      : " . $iso->getField(25);
$output[] = "- BIT[35]      : " . $iso->getField(35);
$output[] = "- BIT[41]      : " . $iso->getField(41);
$output[] = "- BIT[42]      : " . $iso->getField(42);
$output[] = "- BIT[48]      : " . $iso->getField(48);
$output[] = "- BIT[52]      : " . strtoupper(bin2hex($iso->getField(52)));
$output[] = "- BIT[55]      : " . strtoupper(bin2hex($iso->getField(55)));
$output[] = "- BIT[60]      : " . $iso->getField(60);
$output[] = "- BIT[62]      : " . $iso->getField(62);
$output[] = "- BIT[64]      : " . strtoupper(bin2hex($iso->getField(64)));

$output[] = null;

# UNPACK
$iso = new CustomMessage(new Protocol, [
    'mode' => CustomMessage::MODE_HPDH,
]);

$packed = "021560001000000210303801000A810211000000000000050000000036094257112200103030303030303030303033363030363030363030363004054944303133415050524F564544202D2030304E503232355445524D494E414C204944203A2036303036303036303B4D45524348414E54204944203A203630303630303630303630303630303B3B4341524420545950452020203A2044454249543B343836333939585858585858313432332843484950293B3B53414C453B4441544520203A2032322F31312F3230313820202020202054494D45203A2030393A34323A35373B4241544348203A20303030303031202020202020202020205452414345204E4F203A203030303033363B5252454620203A20303030303030303030303336202020204150505256203A203032343936343B3B4250303236544F54414C20414D4F554E54203A3B5250203530302C30303B3B4E50313231202020202020202020202020204E4F205349474E41545552452052455155495245443B4920416772656520546F20506179205468652041626F766520546F74616C20416D6F756E74204163636F7264696E673B202020202020202020746F205468652043617264204973737565722041676772656D656E743B0052910A94B7CCBD4FA09126303071129F18040000000186098424000004A8C13D9572129F18040000000186098424000004A8C13D9500063030303030310000000000000000";
$unpacked = $iso->unpack($packed);

# DEBUG
$output[] = "# UNPACKING `sales` RESPONSE";
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
$output[] = "- BIT[48]      : " . $iso->getField(48);
$output[] = "- BIT[55]      : " . strtoupper(bin2hex($iso->getField(55)));
$output[] = "- BIT[60]      : " . $iso->getField(60);
$output[] = "- BIT[64]      : " . strtoupper(bin2hex($iso->getField(64)));

echo implode(PHP_EOL, $output);