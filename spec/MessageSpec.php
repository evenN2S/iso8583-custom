<?php

use ISO8583\Protocol;
use ISO8583\Message;

describe('ISO8583-PARSER', function() {

	beforeEach(function() {
        $this->iso = new Message(new Protocol(), [
            'lengthPrefix' => 4
        ]);

        $this->isoExample = [
        	'network' => '00550800822000000000000004000000000000000925105308000051301',
        	'finance' => '01680200F2384001888080040000000006000000161111222233334444470040000001000000092510530800005217530809256010035030350318092500005211112222360004201811101150201041110110131374',
        	'reverse' => '02520400F2384001888080040000004206000000161111222233334444470040000001000000092510530800005317530809256010035030350318092500005211112222360004201802100000520925105308000000005030000000050300000100000000000100000000000000000000000011101150201041110110131374',
        ];
	});

	describe('UNPACK', function() {
		it('should unpack `network` message successfully', function() {
			$this->iso->unpack($this->isoExample['network']);

			expect($this->iso->getLength())->toBe(55);
			expect($this->iso->getLengthString())->toBe('0055');
			expect($this->iso->getMTI())->toBe('0800');
			expect($this->iso->getBitmap())->toBe('10000010001000000000000000000000000000000000000000000000000000000000010000000000000000000000000000000000000000000000000000000000');
			expect($this->iso->getBitmapString())->toBe('82200000000000000400000000000000');
			expect($this->iso->getFieldsIds())->toBe([7,11,70]);
			expect($this->iso->getField(7))->toBe('0925105308');
			expect($this->iso->getField(11))->toBe('000051');
			expect($this->iso->getField(70))->toBe('301');
			expect($this->iso->getFields())->toBe([
				7 => '0925105308',
				11 => '000051',
				70 => '301',
			]);
		});

		it('should unpack `finance` message successfully', function() {
			$this->iso->unpack($this->isoExample['finance']);

			expect($this->iso->getLength())->toBe(168);
			expect($this->iso->getLengthString())->toBe('0168');
			expect($this->iso->getMTI())->toBe('0200');
			expect($this->iso->getBitmap())->toBe('11110010001110000100000000000001100010001000000010000000000001000000000000000000000000000000000000000110000000000000000000000000');
			expect($this->iso->getBitmapString())->toBe('F2384001888080040000000006000000');
			expect($this->iso->getFieldsIds())->toBe([2,3,4,7,11,12,13,18,32,33,37,41,49,62,102,103]);
			expect($this->iso->getField(2))->toBe('1111222233334444');
			expect($this->iso->getField(3))->toBe('470040');
			expect($this->iso->getField(4))->toBe('000001000000');
			expect($this->iso->getField(7))->toBe('0925105308');
			expect($this->iso->getField(11))->toBe('000052');
			expect($this->iso->getField(12))->toBe('175308');
			expect($this->iso->getField(13))->toBe('0925');
			expect($this->iso->getField(18))->toBe('6010');
			expect($this->iso->getField(32))->toBe('503');
			expect($this->iso->getField(33))->toBe('503');
			expect($this->iso->getField(37))->toBe('180925000052');
			expect($this->iso->getField(41))->toBe('11112222');
			expect($this->iso->getField(49))->toBe('360');
			expect($this->iso->getField(62))->toBe('2018');
			expect($this->iso->getField(102))->toBe('10115020104');
			expect($this->iso->getField(103))->toBe('10110131374');
			expect($this->iso->getFields())->toBe([
				2 => '1111222233334444',
				3 => '470040',
				4 => '000001000000',
				7 => '0925105308',
				11 => '000052',
				12 => '175308',
				13 => '0925',
				18 => '6010',
				32 => '503',
				33 => '503',
				37 => '180925000052',
				41 => '11112222',
				49 => '360',
				62 => '2018',
				102 => '10115020104',
				103 => '10110131374',
			]);
		});

		it('should unpack `reverse` message successfully', function() {
			$this->iso->unpack($this->isoExample['reverse']);

			expect($this->iso->getLength())->toBe(252);
			expect($this->iso->getLengthString())->toBe('0252');
			expect($this->iso->getMTI())->toBe('0400');
			expect($this->iso->getBitmap())->toBe('11110010001110000100000000000001100010001000000010000000000001000000000000000000000000000100001000000110000000000000000000000000');
			expect($this->iso->getBitmapString())->toBe('F2384001888080040000004206000000');
			expect($this->iso->getFieldsIds())->toBe([2,3,4,7,11,12,13,18,32,33,37,41,49,62,90,95,102,103]);
			expect($this->iso->getField(2))->toBe('1111222233334444');
			expect($this->iso->getField(3))->toBe('470040');
			expect($this->iso->getField(4))->toBe('000001000000');
			expect($this->iso->getField(7))->toBe('0925105308');
			expect($this->iso->getField(11))->toBe('000053');
			expect($this->iso->getField(12))->toBe('175308');
			expect($this->iso->getField(13))->toBe('0925');
			expect($this->iso->getField(18))->toBe('6010');
			expect($this->iso->getField(32))->toBe('503');
			expect($this->iso->getField(33))->toBe('503');
			expect($this->iso->getField(37))->toBe('180925000052');
			expect($this->iso->getField(41))->toBe('11112222');
			expect($this->iso->getField(49))->toBe('360');
			expect($this->iso->getField(62))->toBe('2018');
			expect($this->iso->getField(90))->toBe('021000005209251053080000000050300000000503');
			expect($this->iso->getField(95))->toBe('000001000000000001000000000000000000000000');
			expect($this->iso->getField(102))->toBe('10115020104');
			expect($this->iso->getField(103))->toBe('10110131374');
			expect($this->iso->getFields())->toBe([
				2 => '1111222233334444',
				3 => '470040',
				4 => '000001000000',
				7 => '0925105308',
				11 => '000053',
				12 => '175308',
				13 => '0925',
				18 => '6010',
				32 => '503',
				33 => '503',
				37 => '180925000052',
				41 => '11112222',
				49 => '360',
				62 => '2018',
				90 => '021000005209251053080000000050300000000503',
				95 => '000001000000000001000000000000000000000000',
				102 => '10115020104',
				103 => '10110131374',
			]);
		});

		it('should throw error on wrong length', function() {
            expect(function() {
                $message = substr($this->isoExample['network'], 0, strlen($this->isoExample['network']) - 2);
                $this->iso->unpack($message);
            })->toThrow(new \ISO8583\Error\UnpackError('Message length is 53 and should be 55'));
		});
	});

	describe('PACK', function() {
        it('should pack `network` message successfully', function() {
            $this->iso->setMTI('0800');
			$this->iso->setField(7, '0925105308');
			$this->iso->setField(11, '000051');
			$this->iso->setField(70, '301');

            $packed = $this->iso->pack();

			expect($this->iso->getLength())->toBe(55);
			expect($this->iso->getLengthString())->toBe('0055');
			expect($this->iso->getMTI())->toBe('0800');
			expect($this->iso->getBitmap())->toBe('10000010001000000000000000000000000000000000000000000000000000000000010000000000000000000000000000000000000000000000000000000000');
			expect($this->iso->getBitmapString())->toBe('82200000000000000400000000000000');
			expect($this->iso->getFieldsIds())->toBe([7,11,70]);
			expect($this->iso->getField(7))->toBe('0925105308');
			expect($this->iso->getField(11))->toBe('000051');
			expect($this->iso->getField(70))->toBe('301');
			expect($this->iso->getFields())->toBe([
				7 => '0925105308',
				11 => '000051',
				70 => '301',
			]);
        });

		it('should pack `finance` message successfully', function() {
			$this->iso->setMTI('0200');
			$this->iso->setField(2, '1111222233334444');
			$this->iso->setField(3, '470040');
			$this->iso->setField(4, '000001000000');
			$this->iso->setField(7, '0925105308');
			$this->iso->setField(11, '000052');
			$this->iso->setField(12, '175308');
			$this->iso->setField(13, '0925');
			$this->iso->setField(18, '6010');
			$this->iso->setField(32, '503');
			$this->iso->setField(33, '503');
			$this->iso->setField(37, '180925000052');
			$this->iso->setField(41, '11112222');
			$this->iso->setField(49, '360');
			$this->iso->setField(62, '2018');
			$this->iso->setField(102, '10115020104');
			$this->iso->setField(103, '10110131374');

			$packed = $this->iso->pack();
			
			expect($this->iso->getLength())->toBe(168);
			expect($this->iso->getLengthString())->toBe('0168');
			expect($this->iso->getMTI())->toBe('0200');
			expect($this->iso->getBitmap())->toBe('11110010001110000100000000000001100010001000000010000000000001000000000000000000000000000000000000000110000000000000000000000000');
			expect($this->iso->getBitmapString())->toBe('F2384001888080040000000006000000');
			expect($this->iso->getFieldsIds())->toBe([2,3,4,7,11,12,13,18,32,33,37,41,49,62,102,103]);
			expect($this->iso->getField(2))->toBe('1111222233334444');
			expect($this->iso->getField(3))->toBe('470040');
			expect($this->iso->getField(4))->toBe('000001000000');
			expect($this->iso->getField(7))->toBe('0925105308');
			expect($this->iso->getField(11))->toBe('000052');
			expect($this->iso->getField(12))->toBe('175308');
			expect($this->iso->getField(13))->toBe('0925');
			expect($this->iso->getField(18))->toBe('6010');
			expect($this->iso->getField(32))->toBe('503');
			expect($this->iso->getField(33))->toBe('503');
			expect($this->iso->getField(37))->toBe('180925000052');
			expect($this->iso->getField(41))->toBe('11112222');
			expect($this->iso->getField(49))->toBe('360');
			expect($this->iso->getField(62))->toBe('2018');
			expect($this->iso->getField(102))->toBe('10115020104');
			expect($this->iso->getField(103))->toBe('10110131374');
			expect($this->iso->getFields())->toBe([
				2 => '1111222233334444',
				3 => '470040',
				4 => '000001000000',
				7 => '0925105308',
				11 => '000052',
				12 => '175308',
				13 => '0925',
				18 => '6010',
				32 => '503',
				33 => '503',
				37 => '180925000052',
				41 => '11112222',
				49 => '360',
				62 => '2018',
				102 => '10115020104',
				103 => '10110131374',
			]);
		});

		it('should pack `reverse` message successfully', function() {
			$this->iso->setMTI('0400');
			$this->iso->setField(2, '1111222233334444');
			$this->iso->setField(3, '470040');
			$this->iso->setField(4, '000001000000');
			$this->iso->setField(7, '0925105308');
			$this->iso->setField(11, '000053');
			$this->iso->setField(12, '175308');
			$this->iso->setField(13, '0925');
			$this->iso->setField(18, '6010');
			$this->iso->setField(32, '503');
			$this->iso->setField(33, '503');
			$this->iso->setField(37, '180925000052');
			$this->iso->setField(41, '11112222');
			$this->iso->setField(49, '360');
			$this->iso->setField(62, '2018');
			$this->iso->setField(90, '021000005209251053080000000050300000000503');
			$this->iso->setField(95, '000001000000000001000000000000000000000000');
			$this->iso->setField(102, '10115020104');
			$this->iso->setField(103, '10110131374');

			$packed = $this->iso->pack();

			expect($this->iso->getLength())->toBe(252);
			expect($this->iso->getLengthString())->toBe('0252');
			expect($this->iso->getMTI())->toBe('0400');
			expect($this->iso->getBitmap())->toBe('11110010001110000100000000000001100010001000000010000000000001000000000000000000000000000100001000000110000000000000000000000000');
			expect($this->iso->getBitmapString())->toBe('F2384001888080040000004206000000');
			expect($this->iso->getFieldsIds())->toBe([2,3,4,7,11,12,13,18,32,33,37,41,49,62,90,95,102,103]);
			expect($this->iso->getField(2))->toBe('1111222233334444');
			expect($this->iso->getField(3))->toBe('470040');
			expect($this->iso->getField(4))->toBe('000001000000');
			expect($this->iso->getField(7))->toBe('0925105308');
			expect($this->iso->getField(11))->toBe('000053');
			expect($this->iso->getField(12))->toBe('175308');
			expect($this->iso->getField(13))->toBe('0925');
			expect($this->iso->getField(18))->toBe('6010');
			expect($this->iso->getField(32))->toBe('503');
			expect($this->iso->getField(33))->toBe('503');
			expect($this->iso->getField(37))->toBe('180925000052');
			expect($this->iso->getField(41))->toBe('11112222');
			expect($this->iso->getField(49))->toBe('360');
			expect($this->iso->getField(62))->toBe('2018');
			expect($this->iso->getField(90))->toBe('021000005209251053080000000050300000000503');
			expect($this->iso->getField(95))->toBe('000001000000000001000000000000000000000000');
			expect($this->iso->getField(102))->toBe('10115020104');
			expect($this->iso->getField(103))->toBe('10110131374');
			expect($this->iso->getFields())->toBe([
				2 => '1111222233334444',
				3 => '470040',
				4 => '000001000000',
				7 => '0925105308',
				11 => '000053',
				12 => '175308',
				13 => '0925',
				18 => '6010',
				32 => '503',
				33 => '503',
				37 => '180925000052',
				41 => '11112222',
				49 => '360',
				62 => '2018',
				90 => '021000005209251053080000000050300000000503',
				95 => '000001000000000001000000000000000000000000',
				102 => '10115020104',
				103 => '10110131374',
			]);
		});
	});

});