<?php

use ISO8583\Protocol;
use ISO8583\CustomMessage;
use Socket\Raw\Factory;
use phpseclib\Crypt\DES;

describe('ISO8583-CUSTOM-PARSER', function() {

    $this->debug = true;

	beforeEach(function() {
        $this->iso = new CustomMessage(new Protocol(), [
            'mode' => CustomMessage::MODE_HPDH,
        ]);

        $this->samples = [
            'request' => [
                'cut_off' => '00236000098053080082220000000000000400000000000000123123595900000112310201',
                'log_on' => '001D6000098053080020200000008000009200000000023132333435363738',
                'key_exchange' => '00656000018053080082220000000100000400000000000000123123595900000312310064454339444636393533353346413037354246464136313030303030303030303030303030303030303030303030303030303030303030303030303030303030300101',
            ],
            'response' => [
                'cut_off' => '',
                'log_on' => '',
                'key_exchange' => '',
            ],
        ];
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
                echo "- ISO MESSAGE : " . chunk_split($packed, 2, ' ') . "\n";
                echo "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ') . "\n";
                echo "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ') . "\n";
                echo "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ') . "\n";
                echo "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ') . "\n";
                foreach ($bits as $bit => $value) echo "- BIT[$bit] : " . chunk_split($this->iso->getField($bit), 2, ' ') . "\n";
                echo "\n";
            }

            expect($packed)->toBe($this->samples['request']['cut_off']);
			expect($this->iso->getLength())->toBe(35);
			expect($this->iso->getLengthString())->toBe('0023');
			expect($this->iso->getTPDU())->toBe('6000098053');
            expect($this->iso->getMTI())->toBe('0800');
            expect($this->iso->getBitmapString())->toBe('82220000000000000400000000000000');
            expect($this->iso->getFieldsIds())->toBe(array_keys($bits));
            foreach ($bits as $bit => $value) expect($this->iso->getField($bit))->toBe($value);
            expect($this->iso->getFields())->toBe($bits);
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
                echo "- ISO MESSAGE : " . chunk_split($packed, 2, ' ') . "\n";
                echo "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ') . "\n";
                echo "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ') . "\n";
                echo "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ') . "\n";
                echo "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ') . "\n";
                foreach ($bits as $bit => $value) echo "- BIT[$bit] : " . chunk_split($this->iso->getField($bit), 2, ' ') . "\n";
                echo "\n";
            }

            expect($packed)->toBe($this->samples['request']['log_on']);
            expect($this->iso->getLength())->toBe(29);
			expect($this->iso->getLengthString())->toBe('001D');
            expect($this->iso->getMTI())->toBe('0800');
            expect($this->iso->getBitmapString())->toBe('2020000000800000');
            expect($this->iso->getFieldsIds())->toBe(array_keys($bits));
            foreach ($bits as $bit => $value) expect($this->iso->getField($bit))->toBe($value);
            expect($this->iso->getFields())->toBe($bits);
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
                'plain' => 'A87FA53383C75BB5',
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
                echo "\n\n";
				echo "# packing `key_exchange` request : \n";
				echo "- KEY_EXCHANGE PLAIN : " . chunk_split($keyExchange['plain'], 2, ' ') . "\n";
				echo "- KEY_EXCHANGE KEY : " . chunk_split($keyExchange['key'], 2, ' ') . "\n";
				echo "- KEY_EXCHANGE ENCRYPTED : " . chunk_split($keyExchange['encrypted'], 2, ' ') . "\n";
				echo "- KEY_EXCHANGE KCV : " . chunk_split($keyExchange['kcv'], 2, ' ') . "\n";
                echo "- ISO MESSAGE : " . chunk_split($packed, 2, ' ') . "\n";
                echo "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ') . "\n";
                echo "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ') . "\n";
                echo "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ') . "\n";
                echo "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ') . "\n";
    
                foreach ($bits as $bit => $value)
                    echo "- BIT[$bit] : " . chunk_split($this->iso->getField($bit), 2, ' ') . "\n";
    
                echo "\n";
            }

        	expect($packed)->toBe($this->samples['request']['key_exchange']);
            expect($this->iso->getLength())->toBe(101);
			expect($this->iso->getLengthString())->toBe('0065');
            expect($this->iso->getMTI())->toBe('0800');
            expect($this->iso->getBitmapString())->toBe('82220000000100000400000000000000');
            expect($this->iso->getFieldsIds())->toBe(array_keys($bits));
            foreach ($bits as $bit => $value) expect($this->iso->getField($bit))->toBe($value);
            expect($this->iso->getFields())->toBe($bits);
        });

	});
	
	describe('UNPACK', function () {

		it('should unpack `cut_off` request successfully', function () {
			$packed = $this->samples['request']['cut_off'];

			$this->iso->unpack($packed);

			if ($this->debug)
            {
                echo "\n\n";
                echo "# unpacking `cut_off` request : \n";
                echo "- ISO MESSAGE : " . chunk_split($packed, 2, ' ') . "\n";
                echo "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ') . "\n";
                echo "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ') . "\n";
                echo "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ') . "\n";
                echo "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ') . "\n";
                foreach ($this->iso->getFieldsIds() as $bit) echo "- BIT[$bit] : " . chunk_split($this->iso->getField($bit), 2, ' ') . "\n";
                echo "\n";
			}

			$bits = [
				7 => '1231235959',
				11 => '000001',
				15 => '1231',
				70 => '0201',
			];

			expect($this->iso->getLength())->toBe(35);
			expect($this->iso->getLengthString())->toBe('0023');
			expect($this->iso->getTPDU())->toBe('6000098053');
            expect($this->iso->getMTI())->toBe('0800');
            expect($this->iso->getBitmapString())->toBe('82220000000000000400000000000000');
            expect($this->iso->getFieldsIds())->toBe(array_keys($bits));
            foreach ($bits as $bit => $value) expect($this->iso->getField($bit))->toBe($value);
            expect($this->iso->getFields())->toBe($bits);
		});

		it('should unpack `log_on` request successfully', function () {
			$packed = $this->samples['request']['log_on'];

			$this->iso->unpack($packed);

			if ($this->debug)
            {
                echo "\n\n";
                echo "# unpacking `log_on` request : \n";
                echo "- ISO MESSAGE : " . chunk_split($packed, 2, ' ') . "\n";
                echo "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ') . "\n";
                echo "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ') . "\n";
                echo "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ') . "\n";
                echo "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ') . "\n";
                foreach ($this->iso->getFieldsIds() as $bit) echo "- BIT[$bit] : " . chunk_split($this->iso->getField($bit), 2, ' ') . "\n";
                echo "\n";
			}

			$bits = [
				3 => '920000',
				11 => '000002',
				41 => '12345678',
			];

			expect($this->iso->getLength())->toBe(29);
			expect($this->iso->getLengthString())->toBe('001D');
			expect($this->iso->getTPDU())->toBe('6000098053');
            expect($this->iso->getMTI())->toBe('0800');
            expect($this->iso->getBitmapString())->toBe('2020000000800000');
            expect($this->iso->getFieldsIds())->toBe(array_keys($bits));
            foreach ($bits as $bit => $value) expect($this->iso->getField($bit))->toBe($value);
            expect($this->iso->getFields())->toBe($bits);
		});

		it('should unpack `key_exchange` request successfully', function () {
			$packed = $this->samples['request']['key_exchange'];

			$this->iso->unpack($packed);

			if ($this->debug)
            {
                echo "\n\n";
                echo "# unpacking `key_exchange` request : \n";
                echo "- ISO MESSAGE : " . chunk_split($packed, 2, ' ') . "\n";
                echo "- LENGTH : " . chunk_split($this->iso->getLengthString(), 2, ' ') . "\n";
                echo "- TPDU : " . chunk_split($this->iso->getTPDU(), 2, ' ') . "\n";
                echo "- MTI : " . chunk_split($this->iso->getMTI(), 2, ' ') . "\n";
                echo "- BITMAP : " . chunk_split($this->iso->getBitmapString(), 2, ' ') . "\n";
                foreach ($this->iso->getFieldsIds() as $bit) echo "- BIT[$bit] : " . chunk_split($this->iso->getField($bit), 2, ' ') . "\n";
                echo "\n";
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
            $cipher->setKey(hex2bin($keyExchange['plain']));
            $keyExchange['kcv'] = str_pad(substr(bin2hex($cipher->encrypt(hex2bin('0000000000000000'))), 0, 6), 16, '0', STR_PAD_RIGHT);
			$keyExchange = array_map('strtoupper', $keyExchange);
			
			$bits[48] = str_pad($keyExchange['encrypted'] . $keyExchange['kcv'], 64, '0', STR_PAD_RIGHT);

			expect($this->iso->getLength())->toBe(101);
			expect($this->iso->getLengthString())->toBe('0065');
			expect($this->iso->getTPDU())->toBe('6000018053');
            expect($this->iso->getMTI())->toBe('0800');
            expect($this->iso->getBitmapString())->toBe('82220000000100000400000000000000');
            expect($this->iso->getFieldsIds())->toBe(array_keys($bits));
            foreach ($bits as $bit => $value) expect($this->iso->getField($bit))->toBe($value);
            expect($this->iso->getFields())->toBe($bits);
		});

	});

});