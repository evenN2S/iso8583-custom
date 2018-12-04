<?php

use ISO8583\Protocol;
use ISO8583\CustomMessage;
use Socket\Raw\Factory;
use phpseclib\Crypt\DES;

describe('ISO8583-CUSTOM-PARSER', function() {

    $this->debug = false;

	beforeEach(function() {
        $this->iso = new CustomMessage(new Protocol(), [
            'mode' => CustomMessage::MODE_HPDH,
        ]);

        $this->samples = [
            'request' => [
                'cut_off' => '00236000098053080082220000000000000400000000000000123123595900000112310201',
                'log_on' => '001D6000098053080020200000008000009200000000023132333435363738',
                'key_exchange' => '00656000018053080082220000000100000400000000000000123123595900000312310064454339444636393533353346413037354246464136313030303030303030303030303030303030303030303030303030303030303030303030303030303030300101',
                'sales' => '011C600010000002003020058020C112150000000000000500000000360051001000374863995550001423D20072216050010410689F363030363030363036303036303036303036303036303000214149303134413030303030303630323130313030305103AFE304A02C5401525F2A0203605F340101820254008407A0000006021010950542800480009A031811229C01009F02060000000500009F03060000000000009F101C0101A00080000001431BCC00000000000000000000000000000000009F1A0203609F1E0830313635323739369F2608689BB04E70B50CDA9F2701809F3303E0F8C89F34030200009F3501229F360202CC9F370426F640469F410400000000000630303030303100063030303033360000000000000000',
                'sales_reversal' => '0052600009000004007024058000C0000516486399555000142300000000000015000000006320070051000900393837303938303339383730313330303133303030303100063030303030320000000000000000',
                'void' => '011760000900000200703C058008C00215164863995550001423020000000000500000000062101820112820070111000900303030303030303030303631393837303938303339383730313330303133303030303101705F2A0203605F340101820254008407A0000006021010950542000488309A031811289C01009F02060000005000009F03060000000000009F090201009F101C010161008000000A280B4700000000000000000000000000000000009F1A0203609F1E0835313432353834329F2608BB6EFB73D91142189F2701409F3303E0F8C89F34030200009F3501229F360202E79F370437BB15F89F410400000061DF5B0A11000000011100000001000630303030303100063030303030310000000000000000',
                'void_reversal' => '006B60000900000400703C058008C000151648639955500014230200000000009800000000651027321128200701110009003030303030303030303036343938373039383033393837303133303031333030303031000630303030303100063030303030320000000000000000',
                'settlement' => '0047600010000005002020010000C000129200000000370010363030363030363036303036303036303036303036303000063030303030310015303031303030303030303530303030',
                'batch_upload' => '0071600010000003207038058008C000111652642206126935760000000000012800000001231403470711000000100031313037313830303031313836303036303030343630303630303630303630303630300022303230303030303131383131303731383030303131380000000000000000',
                'final_settlement' => '0047600010000005002020010000C000129600000000420010363030363030363036303036303036303036303036303000063030303030310015303031303030303030303530303030',
            ],
            'response' => [
                'cut_off' => '',
                'log_on' => '',
                'key_exchange' => '',
                'sales' => '021560001000000210303801000A810211000000000000050000000036094257112200103030303030303030303033363030363030363030363004054944303133415050524F564544202D2030304E503232355445524D494E414C204944203A2036303036303036303B4D45524348414E54204944203A203630303630303630303630303630303B3B4341524420545950452020203A2044454249543B343836333939585858585858313432332843484950293B3B53414C453B4441544520203A2032322F31312F3230313820202020202054494D45203A2030393A34323A35373B4241544348203A20303030303031202020202020202020205452414345204E4F203A203030303033363B5252454620203A20303030303030303030303336202020204150505256203A203032343936343B3B4250303236544F54414C20414D4F554E54203A3B5250203530302C30303B3B4E50313231202020202020202020202020204E4F205349474E41545552452052455155495245443B4920416772656520546F20506179205468652041626F766520546F74616C20416D6F756E74204163636F7264696E673B202020202020202020746F205468652043617264204973737565722041676772656D656E743B0052910A94B7CCBD4FA09126303071129F18040000000186098424000004A8C13D9572129F18040000000186098424000004A8C13D9500063030303030310000000000000000',
                'sales_reversal' => '003860000900000410303801000A8000000000000000001500000000631024571128000932383131313830303030363330303938373039383033',
                'void' => '021D60000900000210703801000A810211164863995550001423020000000000500000000062101846112800093030303030303030303036323030393837303938303302864944303133415050524F564544202D2030304E503232385445524D494E414C204944203A2039383730393830333B4D45524348414E54204944203A203938373031333030313330303030313B3B4341524420545950452020203A2044454249543B343836333939585858585858313432332843484950293B3B564F49442053414C453B44415445203A2032382F31312F3230313820202020202054494D45203A2031303A31383A34363B4241544348203A20303030303031202020202020202020205452414345204E4F203A203030303030313B52524546203A20303030303030303030303632202020204150505256203A203032353930393B3B4250303330544F54414C20414D4F554E54203A3B202D525020352E3030302C30303B3B01705F2A0203605F340101820254008407A0000006021010950542000488309A031811289C01009F02060000005000009F03060000000000009F090201009F101C010161008000000A280B4700000000000000000000000000000000009F1A0203609F1E0835313432353834329F2608BB6EFB73D91142189F2701409F3303E0F8C89F34030200009F3501229F360202E79F370437BB15F89F410400000061DF5B0A1100000001110000000100063030303030310000000000000000',
                'void_reversal' => '003860000900000410303801000A8000000200000000009800000000651029131128000932383131313830303030363530303938373039383033',
                'settlement' => '0046600010000005102038010002C0000292000000003709451011220010393536303036303036303630303630303630303630303630300015303031303030303030303530303030',
                'batch_upload' => '003260001000000330203801000A8000000000000001231403470711001031313037313830303031313830303630303630303034',
                'final_settlement' => '01B3600010000005102038010002C10002960000000042094514112200103030363030363030363036303036303036303036303036303003634944303133415050524F564544202D2030304E503037335445524D494E414C204944203A2036303036303036303B4D45524348414E54204944203A203630303630303630303630303630303B3B544F54414C2042592041435155495245523B3B4250303131534554544C454D454E543B4E503136383B4441544520203A2032322F31312F323031382054494D45203A2030393A34353A31343B4241544348203A2030303030303120434C4F5345443B3B5452414E53414354494F4E20202020434F554E5420202020414D4F554E54203B544F54414C2053414C45202020202030303520202020202052502E2031342E3937352C30303B544F54414C20564F4944202020202030303320202020202052502E2031332E3630302C30303B3B42503033353B4752414E4420544F54414C202020203030323B3B52502E20312E3337352C30303B3B4E5030333320202020202020202020202020534554544C454D454E5420434F4E4649524D45440015303031303030303030303530303030',
            ],
        ];
	});
	
	describe('UNPACK REQUEST', function () {

		it('should unpack `cut_off` request successfully', function () {
			$packed = $this->samples['request']['cut_off'];

			$this->iso->unpack($packed);

			if ($this->debug)
            {
                echo "\n\n";
                echo "# unpacking `cut_off` request : \n";
                echo "- ISO MESSAGE : " . chunk_split($packed, 2, ' ') . PHP_EOL;
                echo "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ') . PHP_EOL;
                echo "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ') . PHP_EOL;
                echo "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ') . PHP_EOL;
                echo "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ') . PHP_EOL;
                foreach ($this->iso->getFieldsIds() as $bit) echo "- BIT[$bit] : " . chunk_split($this->iso->getField($bit), 2, ' ') . PHP_EOL;
                echo PHP_EOL;
			} else {
                expect(1)->toBe(1);
            }

			$bits = [
				7 => '1231235959',
				11 => '000001',
				15 => '1231',
				70 => '0201',
			];

			// expect($this->iso->getLength())->toBe(35);
			// expect($this->iso->getLengthString())->toBe('0023');
			// expect($this->iso->getTPDU())->toBe('6000098053');
            // expect($this->iso->getMTI())->toBe('0800');
            // expect($this->iso->getBitmapString())->toBe('82220000000000000400000000000000');
            // expect($this->iso->getFieldsIds())->toBe(array_keys($bits));
            // foreach ($bits as $bit => $value) expect($this->iso->getField($bit))->toBe($value);
            // expect($this->iso->getFields())->toBe($bits);
		});

		it('should unpack `log_on` request successfully', function () {
			$packed = $this->samples['request']['log_on'];

			$this->iso->unpack($packed);

			if ($this->debug)
            {
                echo "\n\n";
                echo "# unpacking `log_on` request : \n";
                echo "- ISO MESSAGE : " . chunk_split($packed, 2, ' ') . PHP_EOL;
                echo "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ') . PHP_EOL;
                echo "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ') . PHP_EOL;
                echo "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ') . PHP_EOL;
                echo "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ') . PHP_EOL;
                foreach ($this->iso->getFieldsIds() as $bit) echo "- BIT[$bit] : " . chunk_split($this->iso->getField($bit), 2, ' ') . PHP_EOL;
                echo PHP_EOL;
			} else {
                expect(1)->toBe(1);
            }

			$bits = [
				3 => '920000',
				11 => '000002',
				41 => '12345678',
			];

			// expect($this->iso->getLength())->toBe(29);
			// expect($this->iso->getLengthString())->toBe('001D');
			// expect($this->iso->getTPDU())->toBe('6000098053');
            // expect($this->iso->getMTI())->toBe('0800');
            // expect($this->iso->getBitmapString())->toBe('2020000000800000');
            // expect($this->iso->getFieldsIds())->toBe(array_keys($bits));
            // foreach ($bits as $bit => $value) expect($this->iso->getField($bit))->toBe($value);
            // expect($this->iso->getFields())->toBe($bits);
		});

		it('should unpack `key_exchange` request successfully', function () {
			$packed = $this->samples['request']['key_exchange'];

			$this->iso->unpack($packed);

			if ($this->debug)
            {
                echo "\n\n";
                echo "# unpacking `key_exchange` request : \n";
                echo "- ISO MESSAGE : " . chunk_split($packed, 2, ' ') . PHP_EOL;
                echo "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ') . PHP_EOL;
                echo "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ') . PHP_EOL;
                echo "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ') . PHP_EOL;
                echo "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ') . PHP_EOL;
                foreach ($this->iso->getFieldsIds() as $bit) echo "- BIT[$bit] : " . chunk_split($this->iso->getField($bit), 2, ' ') . PHP_EOL;
                echo PHP_EOL;
			} else {
                expect(1)->toBe(1);
            }

			$bits = [
				7 => '1231235959',
                11 => '000003',
                15 => '1231',
				48 => null,
				70 => '0101',
			];

			$keyExchange = [
                # 'plain' => bin2hex(random_bytes(8)),
                'plain' => 'A87FA53383C75BB5',
                'key' => '10213A4B5C6D7E8F',
				'encrypted' => null, # EC9DF695353FA075
				'kcv' => null, # BFFA610000000000
            ];
			
			$cipher = new DES(DES::MODE_ECB);
            $cipher->paddable = false;
            $cipher->setKey(hex2bin($keyExchange['key']));
            $keyExchange['encrypted'] = bin2hex($cipher->encrypt(hex2bin($keyExchange['plain'])));
            $cipher->setKey(hex2bin($keyExchange['key']));
            $keyExchange['kcv'] = str_pad(substr(bin2hex($cipher->encrypt(hex2bin('0000000000000000'))), 0, 6), 16, '0', STR_PAD_RIGHT);
			$keyExchange = array_map('strtoupper', $keyExchange);
			
			$bits[48] = str_pad($keyExchange['encrypted'] . $keyExchange['kcv'], 64, '0', STR_PAD_RIGHT);

			// expect($this->iso->getLength())->toBe(101);
			// expect($this->iso->getLengthString())->toBe('0065');
			// expect($this->iso->getTPDU())->toBe('6000018053');
            // expect($this->iso->getMTI())->toBe('0800');
            // expect($this->iso->getBitmapString())->toBe('82220000000100000400000000000000');
            // expect($this->iso->getFieldsIds())->toBe(array_keys($bits));
            // foreach ($bits as $bit => $value) expect($this->iso->getField($bit))->toBe($value);
            // expect($this->iso->getFields())->toBe($bits);
		});

        it('should unpack `sales` request successfully', function () {
            $packed = $this->samples['request']['sales'];

            $this->iso->unpack($packed);

            if ($this->debug)
            {
                echo PHP_EOL;
                echo PHP_EOL;
                echo "# unpacking `sales` request : " . PHP_EOL;
                echo "- ISO MESSAGE : " . chunk_split($packed, 2, ' ') . PHP_EOL;
                echo "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ') . PHP_EOL;
                echo "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ') . PHP_EOL;
                echo "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ') . PHP_EOL;
                echo "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ') . PHP_EOL;
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);
    
                    if (in_array($bit, [52,55,64]))
                    {
                        $value = strtoupper(bin2hex($value));
                    }
    
                    echo "- BIT[$bit] : " . chunk_split($value, 2, ' ') . PHP_EOL;
                }
    
                echo PHP_EOL;
            } else {
                expect(1)->toBe(1);
            }
        });

        it('should unpack `sales_reversal` request successfully', function () {
            $packed = $this->samples['request']['sales_reversal'];

            $this->iso->unpack($packed);

            if ($this->debug)
            {
                $output = [];
                
                $output[] = PHP_EOL;
                $output[] = "# unpacking `sales_reversal` request : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);

                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));
    
                    $output[] = "- BIT[$bit] : " . chunk_split($value, 2, ' ');
                }
    
                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }
        });

        it('should unpack `void` request successfully', function () {
            $packed = $this->samples['request']['void'];

            $this->iso->unpack($packed);

            if ($this->debug)
            {
                $output = [];
                
                $output[] = PHP_EOL;
                $output[] = "# unpacking `void` request : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);

                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));
    
                    $output[] = "- BIT[$bit] : " . chunk_split($value, 2, ' ');
                }
    
                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }
        });

        it('should unpack `void_reversal` request successfully', function () {
            $packed = $this->samples['request']['void_reversal'];

            $this->iso->unpack($packed);

            if ($this->debug)
            {
                $output = [];
                
                $output[] = PHP_EOL;
                $output[] = "# unpacking `void_reversal` request : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);

                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));
    
                    $output[] = "- BIT[$bit] : " . chunk_split($value, 2, ' ');
                }
    
                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }
        });

        it('should unpack `settlement` request successfully', function () {
            $packed = $this->samples['request']['settlement'];

            $this->iso->unpack($packed);

            if ($this->debug)
            {
                echo PHP_EOL;
                echo PHP_EOL;
                echo "# unpacking `settlement` request : " . PHP_EOL;
                echo "- ISO MESSAGE : " . chunk_split($packed, 2, ' ') . PHP_EOL;
                echo "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ') . PHP_EOL;
                echo "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ') . PHP_EOL;
                echo "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ') . PHP_EOL;
                echo "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ') . PHP_EOL;
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);
    
                    echo "- BIT[$bit] : " . chunk_split($value, 2, ' ') . PHP_EOL;
                }
    
                echo PHP_EOL;
            } else {
                expect(1)->toBe(1);
            }
        });

        it('should unpack `batch_upload` request successfully', function () {
            $packed = $this->samples['request']['batch_upload'];

            $this->iso->unpack($packed);

            if ($this->debug)
            {
                $output = [];
                
                $output[] = PHP_EOL;
                $output[] = "# unpacking `batch_upload` request : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);

                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));
    
                    $output[] = "- BIT[$bit] : " . chunk_split($value, 2, ' ');
                }
    
                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }
        });

        it('should unpack `final_settlement` request successfully', function () {
            $packed = $this->samples['request']['final_settlement'];

            $this->iso->unpack($packed);

            if ($this->debug)
            {
                $output = [];
                
                $output[] = PHP_EOL;
                $output[] = "# unpacking `final_settlement` request : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);

                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));
    
                    $output[] = "- BIT[$bit] : " . chunk_split($value, 2, ' ');
                }
    
                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }
        });

    });

    describe('PACK', function () {

        it('should pack `cut_off` request successfully', function() {
            $bits = [
                7 => '1231235959', # gmdate('mdHis'),
                11 => '000001',
                15 => '1231', # date('md'),
                70 => '0201',
            ];

            $this->iso->setMTI('0800');
            $this->iso->setTPDU('6000098053');
            foreach ($bits as $bit => $value) $this->iso->setField($bit, $value);

            $packed = $this->iso->pack();

            if ($this->debug)
            {
                echo "\n\n";
                echo "# packing `cut_off` request : \n";
                echo "- ISO MESSAGE : " . chunk_split($packed, 2, ' ') . PHP_EOL;
                echo "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ') . PHP_EOL;
                echo "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ') . PHP_EOL;
                echo "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ') . PHP_EOL;
                echo "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ') . PHP_EOL;
                foreach ($bits as $bit => $value) echo "- BIT[$bit] : " . chunk_split($this->iso->getField($bit), 2, ' ') . PHP_EOL;
                echo PHP_EOL;
            } else {
                expect(1)->toBe(1);
            }

            // expect($packed)->toBe($this->samples['request']['cut_off']);
			// expect($this->iso->getLength())->toBe(35);
			// expect($this->iso->getLengthString())->toBe('0023');
			// expect($this->iso->getTPDU())->toBe('6000098053');
            // expect($this->iso->getMTI())->toBe('0800');
            // expect($this->iso->getBitmapString())->toBe('82220000000000000400000000000000');
            // expect($this->iso->getFieldsIds())->toBe(array_keys($bits));
            // foreach ($bits as $bit => $value) expect($this->iso->getField($bit))->toBe($value);
            // expect($this->iso->getFields())->toBe($bits);
        });

        it('should pack `log_on` request successfully', function() {
            $bits = [
                3 => "920000",
                11 => "000002",
                41 => "12345678",
            ];

            $this->iso->setMTI('0800');
            $this->iso->setTPDU('6000098053');
            foreach ($bits as $bit => $value) $this->iso->setField($bit, $value);

            $packed = $this->iso->pack();

            if ($this->debug)
            {
                echo "\n\n";
                echo "# packing `log_on` request : \n";
                echo "- ISO MESSAGE : " . chunk_split($packed, 2, ' ') . PHP_EOL;
                echo "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ') . PHP_EOL;
                echo "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ') . PHP_EOL;
                echo "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ') . PHP_EOL;
                echo "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ') . PHP_EOL;
                foreach ($bits as $bit => $value) echo "- BIT[$bit] : " . chunk_split($this->iso->getField($bit), 2, ' ') . PHP_EOL;
                echo PHP_EOL;
            } else {
                expect(1)->toBe(1);
            }

            // expect($packed)->toBe($this->samples['request']['log_on']);
            // expect($this->iso->getLength())->toBe(29);
			// expect($this->iso->getLengthString())->toBe('001D');
            // expect($this->iso->getMTI())->toBe('0800');
            // expect($this->iso->getBitmapString())->toBe('2020000000800000');
            // expect($this->iso->getFieldsIds())->toBe(array_keys($bits));
            // foreach ($bits as $bit => $value) expect($this->iso->getField($bit))->toBe($value);
            // expect($this->iso->getFields())->toBe($bits);
        });

        it('should pack `key_exchange` request successfully', function() {
            $bits = [
                7 => '1231235959', # gmdate('mdHis'),
                11 => '000003',
                15 => '1231', # date('md'),
                48 => null,
                70 => '0101',
			];

			$keyExchange = [
                # 'plain' => bin2hex(random_bytes(8)),
                'plain' => '0202020202020202', # 'A87FA53383C75BB5',
                'key' => '10213A4B5C6D7E8F',
				'encrypted' => null, # EC9DF695353FA075
				'kcv' => null, # BFFA610000000000
            ];
			
			$cipher = new DES(DES::MODE_ECB);
            $cipher->paddable = false;
            $cipher->setKey(hex2bin($keyExchange['key']));
            $keyExchange['encrypted'] = bin2hex($cipher->encrypt(hex2bin($keyExchange['plain'])));
            $cipher->setKey(hex2bin($keyExchange['plain']));
            $keyExchange['kcv'] = str_pad(substr(bin2hex($cipher->encrypt(hex2bin('0000000000000000'))), 0, 6), 16, '0', STR_PAD_RIGHT);
			$keyExchange = array_map('strtoupper', $keyExchange);
			
			$bits[48] = str_pad($keyExchange['encrypted'] . $keyExchange['kcv'], 64, '0', STR_PAD_RIGHT);

            $this->iso->setMTI('0800');
            $this->iso->setTPDU('6000018053');
            foreach ($bits as $bit => $value) $this->iso->setField($bit, $value);

            $packed = $this->iso->pack();

            if ($this->debug)
            {
                echo PHP_EOL;
                echo PHP_EOL;
				echo "# packing `key_exchange` request : " . PHP_EOL;
				echo "- KEY_EXCHANGE PLAIN : " . chunk_split($keyExchange['plain'], 2, ' ') . PHP_EOL;
				echo "- KEY_EXCHANGE KEY : " . chunk_split($keyExchange['key'], 2, ' ') . PHP_EOL;
				echo "- KEY_EXCHANGE ENCRYPTED : " . chunk_split($keyExchange['encrypted'], 2, ' ') . PHP_EOL;
				echo "- KEY_EXCHANGE KCV : " . chunk_split($keyExchange['kcv'], 2, ' ') . PHP_EOL;
                echo "- ISO MESSAGE : " . chunk_split($packed, 2, ' ') . PHP_EOL;
                echo "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ') . PHP_EOL;
                echo "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ') . PHP_EOL;
                echo "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ') . PHP_EOL;
                echo "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ') . PHP_EOL;
    
                foreach ($bits as $bit => $value)
                    echo "- BIT[$bit] : " . chunk_split($this->iso->getField($bit), 2, ' ') . PHP_EOL;
    
                echo PHP_EOL;
            } else {
                expect(1)->toBe(1);
            }

        	// expect($packed)->toBe($this->samples['request']['key_exchange']);
            // expect($this->iso->getLength())->toBe(101);
			// expect($this->iso->getLengthString())->toBe('0065');
            // expect($this->iso->getMTI())->toBe('0800');
            // expect($this->iso->getBitmapString())->toBe('82220000000100000400000000000000');
            // expect($this->iso->getFieldsIds())->toBe(array_keys($bits));
            // foreach ($bits as $bit => $value) expect($this->iso->getField($bit))->toBe($value);
            // expect($this->iso->getFields())->toBe($bits);
        });

        it('should pack `sales` request successfully', function () {
            $bits = [
                3 => '000000',
                4 => str_pad(50000, 12, 0, STR_PAD_LEFT),
                11 => '000036',
                22 => '0051',
                24 => '0010',
                25 => '00',
                35 => '4863995550001423D20072216050010410689F',
                41 => '60060060',
                42 => '600600600600600',
                48 => 'AI014A000000602101000',
                52 => hex2bin('5103AFE304A02C54'),
                55 => hex2bin('5F2A0203605F340101820254008407A0000006021010950542800480009A031811229C01009F02060000000500009F03060000000000009F101C0101A00080000001431BCC00000000000000000000000000000000009F1A0203609F1E0830313635323739369F2608689BB04E70B50CDA9F2701809F3303E0F8C89F34030200009F3501229F360202CC9F370426F640469F410400000000'),
                60 => '000001',
                62 => '000036',
                64 => hex2bin('0000000000000000'),
            ];

            $this->iso->setMTI('0200');
            $this->iso->setTPDU('6000100000');
            foreach ($bits as $bit => $value) $this->iso->setField($bit, $value);

            $packed = $this->iso->pack();
            
            if ($this->debug)
            {
                $output = [];
                
                $output[] = PHP_EOL;
                $output[] = "# packing `sales` request : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);
    
                    if (in_array($bit, [52,55,64]))
                    {
                        $value = strtoupper(bin2hex($value));
                    }
    
                    $output[] = "- BIT[$bit] : " . chunk_split($value, 2, ' ');
                }
    
                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }

            expect($packed)->toBe($this->samples['request']['sales']);
        });

        it('should pack `sales_reversal` request successfully', function () {
            $bits = [
                2 => '4863995550001423',
                3 => '000000',
                4 => str_pad(150000, 12, 0, STR_PAD_LEFT),
                11 => '000063',
                14 => '2007',
                22 => '0051',
                24 => '0009',
                25 => '00',
                41 => '98709803',
                42 => '987013001300001',
                62 => '000002',
                64 => hex2bin('0000000000000000'),
            ];

            $this->iso->setMTI('0400');
            $this->iso->setTPDU('6000090000');
            foreach ($bits as $bit => $value) $this->iso->setField($bit, $value);

            $packed = $this->iso->pack();

            if ($this->debug)
            {
                $output = [];
                
                $output[] = PHP_EOL;
                $output[] = "# packing `sales_reversal` request : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);
    
                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));
    
                    $output[] = "- BIT[$bit] : " . chunk_split($value, 2, ' ');
                }
    
                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }

            expect($packed)->toBe($this->samples['request']['sales_reversal']);
        });

        it('should pack `void` request successfully', function () {
            $bits = [
                2 => '4863995550001423',
                3 => '020000',
                4 => str_pad(500000, 12, 0, STR_PAD_LEFT),
                11 => '000062',
                12 => '101820',
                13 => '1128',
                14 => '2007',
                22 => '0111',
                24 => '0009',
                25 => '00',
                37 => '000000000061',
                41 => '98709803',
                42 => '987013001300001',
                55 => hex2bin('5F2A0203605F340101820254008407A0000006021010950542000488309A031811289C01009F02060000005000009F03060000000000009F090201009F101C010161008000000A280B4700000000000000000000000000000000009F1A0203609F1E0835313432353834329F2608BB6EFB73D91142189F2701409F3303E0F8C89F34030200009F3501229F360202E79F370437BB15F89F410400000061DF5B0A11000000011100000001'),
                60 => '000001',
                62 => '000001',
                64 => hex2bin('0000000000000000'),
            ];

            $this->iso->setMTI('0200');
            $this->iso->setTPDU('6000090000');
            foreach ($bits as $bit => $value) $this->iso->setField($bit, $value);

            $packed = $this->iso->pack();
            
            if ($this->debug)
            {
                $output = [];
                
                $output[] = PHP_EOL;
                $output[] = "# packing `void` request : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);
    
                    if (in_array($bit, [52,55,64]))
                    {
                        $value = strtoupper(bin2hex($value));
                    }
    
                    $output[] = "- BIT[$bit] : " . chunk_split($value, 2, ' ');
                }
    
                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }

            expect($packed)->toBe($this->samples['request']['void']);
        });

        it('should pack `void_reversal` request successfully', function () {
            $bits = [
                2 => '4863995550001423',
                3 => '020000',
                4 => str_pad(980000, 12, 0, STR_PAD_LEFT),
                11 => '000065',
                12 => '102732',
                13 => '1128',
                14 => '2007',
                22 => '0111',
                24 => '0009',
                25 => '00',
                37 => '000000000064',
                41 => '98709803',
                42 => '987013001300001',
                60 => '000001',
                62 => '000002',
                64 => hex2bin('0000000000000000'),
            ];

            $this->iso->setMTI('0400');
            $this->iso->setTPDU('6000090000');
            foreach ($bits as $bit => $value) $this->iso->setField($bit, $value);

            $packed = $this->iso->pack();

            if ($this->debug)
            {
                $output = [];
                
                $output[] = PHP_EOL;
                $output[] = "# packing `void_reversal` request : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);
    
                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));
    
                    $output[] = "- BIT[$bit] : " . chunk_split($value, 2, ' ');
                }
    
                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }

            expect($packed)->toBe($this->samples['request']['void_reversal']);
        });

        it('should pack `settlement` request successfully', function () {
            $bits = [
                3 => '920000',
                11 => '000037',
                24 => '0010',
                41 => '60060060',
                42 => '600600600600600',
                60 => '000001',
                63 => '001000000050000',
            ];

            $this->iso->setMTI('0500');
            $this->iso->setTPDU('6000100000');
            foreach ($bits as $bit => $value) $this->iso->setField($bit, $value);

            $packed = $this->iso->pack();
            
            if ($this->debug)
            {
                $output = [];
                
                $output[] = PHP_EOL;
                $output[] = "# packing `settlement` request : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);
    
                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));
    
                    $output[] = "- BIT[$bit] : " . chunk_split($value, 2, ' ');
                }
    
                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }

            expect($packed)->toBe($this->samples['request']['settlement']);
        });

        it('should pack `batch_upload` request successfully', function () {
            $bits = [
                2 => '5264220612693576',
                3 => '000000',
                4 => str_pad(1280000, 12, 0, STR_PAD_LEFT),
                11 => '000123',
                12 => '140347',
                13 => '0711',
                22 => '0000',
                24 => '0010',
                25 => '00',
                37 => '110718000118',
                41 => '60060004',
                42 => '600600600600600',
                60 => '0200000118110718000118',
                64 => hex2bin('0000000000000000'),
            ];

            $this->iso->setMTI('0320');
            $this->iso->setTPDU('6000100000');
            foreach ($bits as $bit => $value) $this->iso->setField($bit, $value);

            $packed = $this->iso->pack();
            
            if ($this->debug)
            {
                $output = [];
                
                $output[] = PHP_EOL;
                $output[] = "# packing `batch_upload` request : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);
    
                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));
    
                    $output[] = "- BIT[$bit] : " . chunk_split($value, 2, ' ');
                }
    
                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }

            expect($packed)->toBe($this->samples['request']['batch_upload']);
        });

        it('should pack `final_settlement` request successfully', function () {
            $bits = [
                3 => '960000',
                11 => '000042',
                24 => '0010',
                41 => '60060060',
                42 => '600600600600600',
                60 => '000001',
                63 => '001000000050000',
            ];

            $this->iso->setMTI('0500');
            $this->iso->setTPDU('6000100000');
            foreach ($bits as $bit => $value) $this->iso->setField($bit, $value);

            $packed = $this->iso->pack();
            
            if ($this->debug)
            {
                $output = [];
                
                $output[] = PHP_EOL;
                $output[] = "# packing `final_settlement` request : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);
    
                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));
    
                    $output[] = "- BIT[$bit] : " . chunk_split($value, 2, ' ');
                }
    
                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }

            expect($packed)->toBe($this->samples['request']['final_settlement']);
        });

	});
    
    describe('UNPACK RESPONSE', function () {

        it('should unpack `sales` response successfully', function () {
            $packed = $this->samples['response']['sales'];

            $this->iso->unpack($packed);

            if ($this->debug)
            {
                $output = [];
                $output[] = PHP_EOL;
                $output[] = "# unpacking `sales_reversal` response : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);
    
                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));

                    if (! in_array($bit, [48]))
                        $value = chunk_split($value, 2, ' ');
    
                    $output[] = "- BIT[$bit] : " . $value;
                }

                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }
        });

        it('should unpack `sales_reversal` response successfully', function () {
            $packed = $this->samples['response']['sales_reversal'];

            $this->iso->unpack($packed);

            if ($this->debug)
            {
                $output = [];
                $output[] = PHP_EOL;
                $output[] = "# unpacking `sales_reversal` response : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);
    
                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));

                    if (! in_array($bit, [48]))
                        $value = chunk_split($value, 2, ' ');
    
                    $output[] = "- BIT[$bit] : " . $value;
                }

                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }
        });

        it('should unpack `void` response successfully', function () {
            $packed = $this->samples['response']['void'];

            $this->iso->unpack($packed);

            if ($this->debug)
            {
                $output = [];
                $output[] = PHP_EOL;
                $output[] = "# unpacking `void` response : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);
    
                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));

                    if (! in_array($bit, [48]))
                        $value = chunk_split($value, 2, ' ');
    
                    $output[] = "- BIT[$bit] : " . $value;
                }

                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }
        });

        it('should unpack `void_reversal` response successfully', function () {
            $packed = $this->samples['response']['void_reversal'];

            $this->iso->unpack($packed);

            if ($this->debug)
            {
                $output = [];
                $output[] = PHP_EOL;
                $output[] = "# unpacking `void_reversal` response : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);
    
                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));

                    if (! in_array($bit, [48]))
                        $value = chunk_split($value, 2, ' ');
    
                    $output[] = "- BIT[$bit] : " . $value;
                }

                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }
        });

        it('should unpack `settlement` response successfully', function () {
            $packed = $this->samples['response']['settlement'];

            $this->iso->unpack($packed);

            if ($this->debug)
            {
                $output = [];
                $output[] = PHP_EOL;
                $output[] = "# unpacking `sales_reversal` response : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);
    
                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));

                    if (! in_array($bit, [48]))
                        $value = chunk_split($value, 2, ' ');
    
                    $output[] = "- BIT[$bit] : " . $value;
                }

                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }
        });

        it('should unpack `batch_upload` response successfully', function () {
            $packed = $this->samples['response']['batch_upload'];

            $this->iso->unpack($packed);

            if ($this->debug)
            {
                $output = [];
                $output[] = PHP_EOL;
                $output[] = "# unpacking `batch_upload` response : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);
    
                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));

                    if (! in_array($bit, [48]))
                        $value = chunk_split($value, 2, ' ');
    
                    $output[] = "- BIT[$bit] : " . $value;
                }

                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }
        });

        it('should unpack `final_settlement` response successfully', function () {
            $packed = $this->samples['response']['final_settlement'];

            $this->iso->unpack($packed);

            if ($this->debug)
            {
                $output = [];
                $output[] = PHP_EOL;
                $output[] = "# unpacking `final_settlement` response : ";
                $output[] = "- ISO MESSAGE : " . chunk_split($packed, 2, ' ');
                $output[] = "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ');
                $output[] = "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ');
                $output[] = "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ');
                $output[] = "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ');
    
                foreach ($this->iso->getFieldsIds() as $bit) 
                {
                    $value = $this->iso->getField($bit);
    
                    if (in_array($bit, [52,55,64]))
                        $value = strtoupper(bin2hex($value));

                    if (! in_array($bit, [48]))
                        $value = chunk_split($value, 2, ' ');
    
                    $output[] = "- BIT[$bit] : " . $value;
                }

                $output[] = PHP_EOL;
    
                echo implode(PHP_EOL, $output);
            } else {
                expect(1)->toBe(1);
            }
        });

    });

});