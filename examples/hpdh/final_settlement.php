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
$iso->setField(3, '960000');
$iso->setField(11, '000042');
$iso->setField(24, '0010');
$iso->setField(41, '60060060');
$iso->setField(42, '600600600600600');
$iso->setField(60, '000001');
$iso->setField(63, '001000000050000');

$packed = $iso->pack();

# DEBUG
$output[] = "# PACKING `final_settlement` REQUEST";
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

$packed = "01B3600010000005102038010002C10002960000000042094514112200103030363030363030363036303036303036303036303036303003634944303133415050524F564544202D2030304E503037335445524D494E414C204944203A2036303036303036303B4D45524348414E54204944203A203630303630303630303630303630303B3B544F54414C2042592041435155495245523B3B4250303131534554544C454D454E543B4E503136383B4441544520203A2032322F31312F323031382054494D45203A2030393A34353A31343B4241544348203A2030303030303120434C4F5345443B3B5452414E53414354494F4E20202020434F554E5420202020414D4F554E54203B544F54414C2053414C45202020202030303520202020202052502E2031342E3937352C30303B544F54414C20564F4944202020202030303320202020202052502E2031332E3630302C30303B3B42503033353B4752414E4420544F54414C202020203030323B3B52502E20312E3337352C30303B3B4E5030333320202020202020202020202020534554544C454D454E5420434F4E4649524D45440015303031303030303030303530303030";
$unpacked = $iso->unpack($packed);

# DEBUG
$output[] = "# UNPACKING `final_settlement` RESPONSE";
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
$output[] = "- BIT[48]      : " . $iso->getField(48);
$output[] = "- BIT[63]      : " . $iso->getField(63);

echo implode(PHP_EOL, $output);