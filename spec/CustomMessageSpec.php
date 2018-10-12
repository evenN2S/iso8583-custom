<?php

use ISO8583\Protocol;
use ISO8583\CustomMessage;

describe('ISO8583-CUSTOM-PARSER', function() {

	beforeEach(function() {
        $this->iso = new CustomMessage(new Protocol(), [
            'lengthPrefix' => 4
        ]);

        $this->isoExample = [
        	'network' => '002208042230000000000008000000123123595900000123595900083132333435363738',
        	'init' => '002C0304223000000000010800000012312359590000022359590008333333333333333300083132333435363738',
        	'topup_inquiry' => '00B401047238000000C1000C16888878100000090500000000000500000012312359590000032359591231323232323232323231313131313131313131313131313100964AA60A291A57423757FE73D399356915DE732A60073662BCDE983D5CBB994642539CB510D2EA717FAEF0988BF48426324BEF5EDE0541D17A2C920BED3A3932DA8AF6BA518A13DB7A100FD642282CE3B6D33D4D0F404D5426E88D57FBCC7BE009000831323334353637380006303030303031',
        	'topup_commit' => '00D602047238000004C1000C1688887810000009050000000000050000001231235959000004235959123135304138353432323232323232323131313131313131313131313131310124701622FE34E507DFA90A0B1AFA3EF9D43DA9E961FB2ADD97DAE959B3938B711B901F91C8925E71F46F62BA648B4148E868A617C60E5267B27EBF6093447D975CBC6170F8010704612A7788B44997C813FAD779939A9D3B1F608353CA74B7F656C6A5439A4E021667089CACAE3A12C7F529FFFFFFFFFFFFFFFFFFFFFF000831323334353637380006303030303031',
        ];
	});

	describe('UNPACK', function() {
		it('should unpack `network` message successfully', function() {
			$this->iso->unpack($this->isoExample['network']);

			expect($this->iso->getLength())->toBe(34);
			expect($this->iso->getLengthString())->toBe('0022');
			expect($this->iso->getMTI())->toBe('0804');
			expect($this->iso->getBitmapString())->toBe('2230000000000008');
			expect($this->iso->getFieldsIds())->toBe([3,7,11,12,61]);
			expect($this->iso->getField(3))->toBe('000000');
			expect($this->iso->getField(7))->toBe('1231235959');
			expect($this->iso->getField(11))->toBe('000001');
			expect($this->iso->getField(12))->toBe('235959');
			expect($this->iso->getField(61))->toBe('12345678');
			expect($this->iso->getFields())->toBe([
				3 => '000000',
				7 => '1231235959',
				11 => '000001',
				12 => '235959',
				61 => '12345678',
			]);
		});

		it('should unpack `init` message successfully', function() {
			$this->iso->unpack($this->isoExample['init']);

			expect($this->iso->getLength())->toBe(44);
			expect($this->iso->getMTI())->toBe('0304');
			expect($this->iso->getBitmapString())->toBe('2230000000000108');
			expect($this->iso->getFieldsIds())->toBe([3,7,11,12,56,61]);
			expect($this->iso->getField(3))->toBe('000000');
			expect($this->iso->getField(7))->toBe('1231235959');
			expect($this->iso->getField(11))->toBe('000002');
			expect($this->iso->getField(12))->toBe('235959');
			expect($this->iso->getField(56))->toBe('33333333');
			expect($this->iso->getField(61))->toBe('12345678');
			expect($this->iso->getFields())->toBe([
				3 => '000000',
				7 => '1231235959',
				11 => '000002',
				12 => '235959',
				56 => '33333333',
				61 => '12345678',
			]);
		});

		it('should unpack `topup_inquiry` message successfully', function() {
			$this->iso->unpack($this->isoExample['topup_inquiry']);

			expect($this->iso->getLength())->toBe(180);
			expect($this->iso->getMTI())->toBe('0104');
			expect($this->iso->getBitmapString())->toBe('7238000000C1000C');
			expect($this->iso->getFieldsIds())->toBe([2,3,4,7,11,12,13,41,42,48,61,62]);
			expect($this->iso->getField(2))->toBe('8888781000000905');
			expect($this->iso->getField(3))->toBe('000000');
			expect($this->iso->getField(4))->toBe('000005000000');
			expect($this->iso->getField(7))->toBe('1231235959');
			expect($this->iso->getField(11))->toBe('000003');
			expect($this->iso->getField(12))->toBe('235959');
			expect($this->iso->getField(13))->toBe('1231');
			expect($this->iso->getField(41))->toBe('22222222');
			expect($this->iso->getField(42))->toBe('111111111111111');
			expect(strtoupper(bin2hex($this->iso->getField(48))))->toBe('4AA60A291A57423757FE73D399356915DE732A60073662BCDE983D5CBB994642539CB510D2EA717FAEF0988BF48426324BEF5EDE0541D17A2C920BED3A3932DA8AF6BA518A13DB7A100FD642282CE3B6D33D4D0F404D5426E88D57FBCC7BE009');
			expect($this->iso->getField(61))->toBe('12345678');
			expect($this->iso->getField(62))->toBe('000001');
			expect($this->iso->getFields())->toBe([
				2 => '8888781000000905',
				3 => '000000',
				4 => '000005000000',
				7 => '1231235959',
				11 => '000003',
				12 => '235959',
				13 => '1231',
				41 => '22222222',
				42 => '111111111111111',
				48 => hex2bin('4AA60A291A57423757FE73D399356915DE732A60073662BCDE983D5CBB994642539CB510D2EA717FAEF0988BF48426324BEF5EDE0541D17A2C920BED3A3932DA8AF6BA518A13DB7A100FD642282CE3B6D33D4D0F404D5426E88D57FBCC7BE009'),
				61 => '12345678',
				62 => '000001',
			]);
		});

		it('should unpack `topup_commit` message successfully', function() {
			$this->iso->unpack($this->isoExample['topup_commit']);

			expect($this->iso->getLength())->toBe(214);
			expect($this->iso->getMTI())->toBe('0204');
			expect($this->iso->getBitmapString())->toBe('7238000004C1000C');
			expect($this->iso->getFieldsIds())->toBe([2,3,4,7,11,12,13,38,41,42,48,61,62]);
			expect($this->iso->getField(2))->toBe('8888781000000905');
			expect($this->iso->getField(3))->toBe('000000');
			expect($this->iso->getField(4))->toBe('000005000000');
			expect($this->iso->getField(7))->toBe('1231235959');
			expect($this->iso->getField(11))->toBe('000004');
			expect($this->iso->getField(12))->toBe('235959');
			expect($this->iso->getField(13))->toBe('1231');
			expect($this->iso->getField(38))->toBe('50A854');
			expect($this->iso->getField(41))->toBe('22222222');
			expect($this->iso->getField(42))->toBe('111111111111111');
			expect(strtoupper(bin2hex($this->iso->getField(48))))->toBe('701622FE34E507DFA90A0B1AFA3EF9D43DA9E961FB2ADD97DAE959B3938B711B901F91C8925E71F46F62BA648B4148E868A617C60E5267B27EBF6093447D975CBC6170F8010704612A7788B44997C813FAD779939A9D3B1F608353CA74B7F656C6A5439A4E021667089CACAE3A12C7F529FFFFFFFFFFFFFFFFFFFFFF');
			expect($this->iso->getField(61))->toBe('12345678');
			expect($this->iso->getField(62))->toBe('000001');
			expect($this->iso->getFields())->toBe([
				2 => '8888781000000905',
				3 => '000000',
				4 => '000005000000',
				7 => '1231235959',
				11 => '000004',
				12 => '235959',
				13 => '1231',
				38 => '50A854',
				41 => '22222222',
				42 => '111111111111111',
				48 => hex2bin('701622FE34E507DFA90A0B1AFA3EF9D43DA9E961FB2ADD97DAE959B3938B711B901F91C8925E71F46F62BA648B4148E868A617C60E5267B27EBF6093447D975CBC6170F8010704612A7788B44997C813FAD779939A9D3B1F608353CA74B7F656C6A5439A4E021667089CACAE3A12C7F529FFFFFFFFFFFFFFFFFFFFFF'),
				61 => '12345678',
				62 => '000001',
			]);
		});

		it('should throw error on wrong length', function() {
            expect(function() {
                $message = substr($this->isoExample['network'], 0, strlen($this->isoExample['network']) - 2);
                $this->iso->unpack($message);
            })->toThrow(new \ISO8583\Error\UnpackError('Message length is 33 and should be 34'));
		});
	});

	describe('PACK', function() {
        it('should pack `network` message successfully', function() {
            $this->iso->setMTI('0804');
			$this->iso->setField(3, '000000');
			$this->iso->setField(7, '1231235959');
			$this->iso->setField(11, '000001');
			$this->iso->setField(12, '235959');
			$this->iso->setField(61, '12345678');

            $packed = $this->iso->pack();

            expect($packed)->toBe($this->isoExample['network']);
            expect($this->iso->getLength())->toBe(34);
			expect($this->iso->getMTI())->toBe('0804');
			expect($this->iso->getBitmapString())->toBe('2230000000000008');
			expect($this->iso->getFieldsIds())->toBe([3,7,11,12,61]);
			expect($this->iso->getField(3))->toBe('000000');
			expect($this->iso->getField(7))->toBe('1231235959');
			expect($this->iso->getField(11))->toBe('000001');
			expect($this->iso->getField(12))->toBe('235959');
			expect($this->iso->getField(61))->toBe('12345678');
			expect($this->iso->getFields())->toBe([
				3 => '000000',
				7 => '1231235959',
				11 => '000001',
				12 => '235959',
				61 => '12345678',
			]);
        });

        it('should pack `init` message successfully', function() {
            $this->iso->setMTI('0304');
			$this->iso->setField(3, '000000');
			$this->iso->setField(7, '1231235959');
			$this->iso->setField(11, '000002');
			$this->iso->setField(12, '235959');
			$this->iso->setField(56, '33333333');
			$this->iso->setField(61, '12345678');

            $packed = $this->iso->pack();

            expect($packed)->toBe($this->isoExample['init']);
			expect($this->iso->getLength())->toBe(44);
			expect($this->iso->getMTI())->toBe('0304');
			expect($this->iso->getBitmapString())->toBe('2230000000000108');
			expect($this->iso->getFieldsIds())->toBe([3,7,11,12,56,61]);
			expect($this->iso->getField(3))->toBe('000000');
			expect($this->iso->getField(7))->toBe('1231235959');
			expect($this->iso->getField(11))->toBe('000002');
			expect($this->iso->getField(12))->toBe('235959');
			expect($this->iso->getField(56))->toBe('33333333');
			expect($this->iso->getField(61))->toBe('12345678');
			expect($this->iso->getFields())->toBe([
				3 => '000000',
				7 => '1231235959',
				11 => '000002',
				12 => '235959',
				56 => '33333333',
				61 => '12345678',
			]);
        });

        it('should pack `topup_inquiry` message successfully', function() {
            $this->iso->setMTI('0104');
			$this->iso->setField(2, '8888781000000905');
			$this->iso->setField(3, '000000');
			$this->iso->setField(4, '000005000000');
			$this->iso->setField(7, '1231235959');
			$this->iso->setField(11, '000003');
			$this->iso->setField(12, '235959');
			$this->iso->setField(13, '1231');
			$this->iso->setField(41, '22222222');
			$this->iso->setField(42, '111111111111111');
			$this->iso->setField(48, hex2bin('4AA60A291A57423757FE73D399356915DE732A60073662BCDE983D5CBB994642539CB510D2EA717FAEF0988BF48426324BEF5EDE0541D17A2C920BED3A3932DA8AF6BA518A13DB7A100FD642282CE3B6D33D4D0F404D5426E88D57FBCC7BE009'));
			$this->iso->setField(61, '12345678');
			$this->iso->setField(62, '000001');

            $packed = $this->iso->pack();

            expect($packed)->toBe($this->isoExample['topup_inquiry']);
			expect($this->iso->getLength())->toBe(180);
			expect($this->iso->getMTI())->toBe('0104');
			expect($this->iso->getBitmapString())->toBe('7238000000C1000C');
			expect($this->iso->getFieldsIds())->toBe([2,3,4,7,11,12,13,41,42,48,61,62]);
			expect($this->iso->getField(2))->toBe('8888781000000905');
			expect($this->iso->getField(3))->toBe('000000');
			expect($this->iso->getField(4))->toBe('000005000000');
			expect($this->iso->getField(7))->toBe('1231235959');
			expect($this->iso->getField(11))->toBe('000003');
			expect($this->iso->getField(12))->toBe('235959');
			expect($this->iso->getField(13))->toBe('1231');
			expect($this->iso->getField(41))->toBe('22222222');
			expect($this->iso->getField(42))->toBe('111111111111111');
			expect(strtoupper(bin2hex($this->iso->getField(48))))->toBe('4AA60A291A57423757FE73D399356915DE732A60073662BCDE983D5CBB994642539CB510D2EA717FAEF0988BF48426324BEF5EDE0541D17A2C920BED3A3932DA8AF6BA518A13DB7A100FD642282CE3B6D33D4D0F404D5426E88D57FBCC7BE009');
			expect($this->iso->getField(61))->toBe('12345678');
			expect($this->iso->getField(62))->toBe('000001');
			expect($this->iso->getFields())->toBe([
				2 => '8888781000000905',
				3 => '000000',
				4 => '000005000000',
				7 => '1231235959',
				11 => '000003',
				12 => '235959',
				13 => '1231',
				41 => '22222222',
				42 => '111111111111111',
				48 => hex2bin('4AA60A291A57423757FE73D399356915DE732A60073662BCDE983D5CBB994642539CB510D2EA717FAEF0988BF48426324BEF5EDE0541D17A2C920BED3A3932DA8AF6BA518A13DB7A100FD642282CE3B6D33D4D0F404D5426E88D57FBCC7BE009'),
				61 => '12345678',
				62 => '000001',
			]);
        });

        it('should pack `topup_commit` message successfully', function() {
            $this->iso->setMTI('0204');
			$this->iso->setField(2, '8888781000000905');
			$this->iso->setField(3, '000000');
			$this->iso->setField(4, '000005000000');
			$this->iso->setField(7, '1231235959');
			$this->iso->setField(11, '000004');
			$this->iso->setField(12, '235959');
			$this->iso->setField(13, '1231');
			$this->iso->setField(38, '50A854');
			$this->iso->setField(41, '22222222');
			$this->iso->setField(42, '111111111111111');
			$this->iso->setField(48, hex2bin('701622FE34E507DFA90A0B1AFA3EF9D43DA9E961FB2ADD97DAE959B3938B711B901F91C8925E71F46F62BA648B4148E868A617C60E5267B27EBF6093447D975CBC6170F8010704612A7788B44997C813FAD779939A9D3B1F608353CA74B7F656C6A5439A4E021667089CACAE3A12C7F529FFFFFFFFFFFFFFFFFFFFFF'));
			$this->iso->setField(61, '12345678');
			$this->iso->setField(62, '000001');

            $packed = $this->iso->pack();

            expect($packed)->toBe($this->isoExample['topup_commit']);
			expect($this->iso->getLength())->toBe(214);
			expect($this->iso->getMTI())->toBe('0204');
			expect($this->iso->getBitmapString())->toBe('7238000004C1000C');
			expect($this->iso->getFieldsIds())->toBe([2,3,4,7,11,12,13,38,41,42,48,61,62]);
			expect($this->iso->getField(2))->toBe('8888781000000905');
			expect($this->iso->getField(3))->toBe('000000');
			expect($this->iso->getField(4))->toBe('000005000000');
			expect($this->iso->getField(7))->toBe('1231235959');
			expect($this->iso->getField(11))->toBe('000004');
			expect($this->iso->getField(12))->toBe('235959');
			expect($this->iso->getField(13))->toBe('1231');
			expect($this->iso->getField(38))->toBe('50A854');
			expect($this->iso->getField(41))->toBe('22222222');
			expect($this->iso->getField(42))->toBe('111111111111111');
			expect(strtoupper(bin2hex($this->iso->getField(48))))->toBe('701622FE34E507DFA90A0B1AFA3EF9D43DA9E961FB2ADD97DAE959B3938B711B901F91C8925E71F46F62BA648B4148E868A617C60E5267B27EBF6093447D975CBC6170F8010704612A7788B44997C813FAD779939A9D3B1F608353CA74B7F656C6A5439A4E021667089CACAE3A12C7F529FFFFFFFFFFFFFFFFFFFFFF');
			expect($this->iso->getField(61))->toBe('12345678');
			expect($this->iso->getField(62))->toBe('000001');
			expect($this->iso->getFields())->toBe([
				2 => '8888781000000905',
				3 => '000000',
				4 => '000005000000',
				7 => '1231235959',
				11 => '000004',
				12 => '235959',
				13 => '1231',
				38 => '50A854',
				41 => '22222222',
				42 => '111111111111111',
				48 => hex2bin('701622FE34E507DFA90A0B1AFA3EF9D43DA9E961FB2ADD97DAE959B3938B711B901F91C8925E71F46F62BA648B4148E868A617C60E5267B27EBF6093447D975CBC6170F8010704612A7788B44997C813FAD779939A9D3B1F608353CA74B7F656C6A5439A4E021667089CACAE3A12C7F529FFFFFFFFFFFFFFFFFFFFFF'),
				61 => '12345678',
				62 => '000001',
			]);
        });
	});

});