<?php
namespace ISO8583;

use ISO8583\Error\UnpackError;
use ISO8583\Error\PackError;

class CustomMessage extends Message
{
	protected $mappers = [
		'a'     => Mapper\Custom\AlphaNumeric::class,
		'n'     => Mapper\Custom\Numeric::class,
		's'     => Mapper\Custom\AlphaNumeric::class,
		'an'    => Mapper\Custom\AlphaNumeric::class,
		'as'    => Mapper\Custom\AlphaNumeric::class,
		'ns'    => Mapper\Custom\AlphaNumeric::class,
		'ans'   => Mapper\Custom\AlphaNumeric::class,
		'b'	    => Mapper\Binary::class,
		'z'	    => Mapper\Custom\AlphaNumeric::class
	];

	public function getLength()
	{
		# Returns integer representation of message length
		return (int) ceil($this->length);
	}

	public function getLengthString()
	{
		# Returns hex representation of message length
		return (string) sprintf('%0' . $this->options['lengthPrefix'] . 'X', $this->length);
	}

	public function getBitmap()
	{
		# Returns binary representation of message bitmap
		return implode(
			null,
			array_map(
				function ($binary)
				{
					return sprintf('%04d', base_convert($binary, 16, 2));
				}, str_split($this->bitmap, 1)
			)
		);
	}

	public function getBitmapString()
	{
		# Returns hex representation of message bitmap
		return $this->bitmap;
	}

	public function unpack($message)
	{
		# Getting message length if we have one
		if ($this->options['lengthPrefix'] > 0)
		{		
			$this->length = $length = (int) hexdec(substr($message, 0, (int) $this->options['lengthPrefix']));

			$this->shrink($message, (int) $this->options['lengthPrefix']);

			if (floor(strlen($message) / 2) != $length)
				throw new UnpackError('Message length is ' . floor(strlen($message) / 2) . ' and should be ' . $length);
		}

		# Parsing MTI 
		$this->setMTI(substr($message, 0, 4));
		$this->shrink($message, 4);

		# Parsing bitmap
		$bitmap = "";

		for (;;)
		{
			$tmp = implode(null, array_map(function($bit) {
				return str_pad(base_convert($bit, 16, 2), 8, 0, STR_PAD_LEFT);
			}, str_split(substr($message, 0, 16), 2)));

			$this->shrink($message, 16);

			$bitmap .= $tmp;

			if (substr($tmp, 0, 1) !== "1" || strlen($bitmap) > 128) {
				break;
			}
		}

		$this->bitmap = implode(null, array_map(function($bit) {
			return strtoupper(base_convert($bit, 2, 16));
		}, str_split($bitmap, 4)));

		# Parsing fields
		for ($i=0; $i < strlen($bitmap); $i++)
		{
			if ($bitmap[$i] === "1")
			{
				$fieldNumber = $i + 1;

				if ($fieldNumber === 1 || $fieldNumber === 65)
					continue;

				$fieldData = $this->protocol->getFieldData($fieldNumber);
				$fieldMapper = $fieldData['type'];

				if (!isset($this->mappers[$fieldMapper]))
					throw new \Exception('Unknown field mapper for "' . $fieldMapper . '" type');

				$mapper = new $this->mappers[$fieldMapper]($fieldData['length']);
				$unpacked = $mapper->unpack($message);

				$this->setField($fieldNumber, $unpacked);
			}
		}

		return $this;
	}

	public function pack()
	{
		# Setting MTI
		$mti = $this->mti;
		
		# Dropping bad fields
		foreach ($this->fields as $key => $val)
		{
			if (in_array($key, [1, 65]))
				unset($this->fields[$key]);
		}
		
		# Populating bitmap
		$bitmap = "";
		$bitmapLength = 64 * (floor(max(array_keys($this->fields)) / 64) + 1);
		$tmpBitmap = "";

		for ($i=1; $i <= $bitmapLength; $i++)
		{
			if (
				$i == 1 && $bitmapLength > 64 ||
				$i == 65 && $bitmapLength > 128 ||
				isset($this->fields[$i])
			) {
				$tmpBitmap .= '1';
			} else {
				$tmpBitmap .= '0';
			}

			if ($i % $bitmapLength == 0)
			{
				for ($j = ($bitmapLength / $i) - 1; $j < $bitmapLength; $j += 4)
				{
        			$bitmap .= sprintf('%01x', base_convert(substr($tmpBitmap, $j, 4), 2, 10));
      			}
			}

			// if ($i % 64 == 0)
			// {
			// 	for ($i=0; $i<64; $i+=4)
   //      			$bitmap .= sprintf('%01x', base_convert(substr($tmpBitmap, $i, 4), 2, 10));
			// }
		}

		$this->bitmap = strtoupper($bitmap);

		# Getting field IDS
		ksort($this->fields);

		# Packing fields
		$message = "";

		foreach($this->fields as $id => $data)
		{
			$fieldData = $this->protocol->getFieldData($id);
			$fieldMapper = $fieldData['type'];

			if (!isset($this->mappers[$fieldMapper]))
				throw new \Exception('Unknown field mapper for "' . $fieldMapper . '" type');
			
			$mapper = new $this->mappers[$fieldMapper]($fieldData['length']);

			if (
				($mapper->getLength() > strlen($data) && $mapper->getVariableLength() === 0 ) ||
				$mapper->getLength() < strlen($data)
			) {
				$error = 'Field [' . $id . '] should have length ' . $mapper->getLength() . ' and your message "' . $data . "' is " . strlen($data);
				throw new Error\PackError($error);
			}			

			$message .= $mapper->pack($data);		
		}

		# Packing all message
		$message = $mti . $bitmap . $message;

		if ($this->options['lengthPrefix'] > 0)
		{
			$hexLength = sprintf('%0' . $this->options['lengthPrefix'] . 'X', strlen($message) / 2);
			$this->length = hexdec($hexLength);
			$message = $hexLength . $message;
		}

		return strtoupper($message);
	}
}