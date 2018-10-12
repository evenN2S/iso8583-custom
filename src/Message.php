<?php
namespace ISO8583;

use ISO8583\Error\UnpackError;
use ISO8583\Error\PackError;

class Message 
{
	protected $protocol;
	protected $options;

	protected $length;
	protected $mti;
    protected $bitmap;
    protected $fields = [];
	protected $mappers = [
		'a'     => Mapper\AlphaNumeric::class,
		'n'     => Mapper\AlphaNumeric::class,
		's'     => Mapper\AlphaNumeric::class,
		'an'    => Mapper\AlphaNumeric::class,
		'as'    => Mapper\AlphaNumeric::class,
		'ns'    => Mapper\AlphaNumeric::class,
		'ans'   => Mapper\AlphaNumeric::class,
		'b'	    => Mapper\Binary::class,
		'z'	    => Mapper\AlphaNumeric::class
	];

	public function __construct(Protocol $protocol, $options = [])
	{
		$defaults = [
			'lengthPrefix' => 4,
		];

		$this->options = $options + $defaults;
		$this->protocol = $protocol;
	}

	protected function hex2str($hex)
	{
	    $str = '';
	    
	    for ($i = 0; $i < strlen($hex); $i += 2)
	    	$str .= chr(hexdec(substr($hex, $i, 2)));

	    return strtoupper($str);
	}

	protected function str2hex($string)
	{
	    $hex = '';
	    
	    for ($i = 0; $i < strlen($string); $i++)
	    {
	        $ord = ord($string[$i]);
	        $hexCode = dechex($ord);
	        $hex .= substr('0' . $hexCode, -2);
	    }

	    return strtoupper($hex);
	}

	protected function shrink(&$message, $length) 
	{
		$message = substr($message, $length);
	}

	public function pack()
	{
		// Setting MTI
		$mti = bin2hex($this->mti);
		
		// Dropping bad fields
		foreach($this->fields as $key=>$val) {
			if (in_array($key, [1, 65])) {
				unset($this->fields[$key]);
			}
		}

		// Populating bitmap
		$bitmap = "";
		$bitmapLength = 64 * (floor(max(array_keys($this->fields)) / 64) + 1);
		$tmpBitmap = "";
		
		for($i=1; $i <= $bitmapLength; $i++) {
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
		}

		$this->bitmap = $tmpBitmap;

		$bitmap = bin2hex($bitmap);

		// Getting field IDS
		ksort($this->fields);

		// Packing fields
		$message = "";

		foreach ($this->fields as $id => $data)
		{
			$fieldData = $this->protocol->getFieldData($id);
			$fieldMapper = $fieldData['type'];

			if (!isset($this->mappers[$fieldMapper])) {
				throw new \Exception('Unknown field mapper for "' . $fieldMapper . '" type');
			}
			
			$mapper = new $this->mappers[$fieldMapper]($fieldData['length']);

			if (
				($mapper->getLength() > strlen($data) && $mapper->getVariableLength() === 0 ) ||
				$mapper->getLength() < strlen($data)
			) {
				$error = 'BIT [' . $id . '] should have length ' . $mapper->getLength() . ' and your message "' . $data . '" is ' . strlen($data);
				throw new Error\PackError($error, $id);
			}			

			$message .= $mapper->pack($data);		
		}

		// Packing all message
		$message = $mti . $bitmap . $message;
		
		if ($this->options['lengthPrefix'] > 0)
		{
			$modifier = 2;
		
			$length = bin2hex(sprintf('%0' . $this->options['lengthPrefix'] . 'd', strlen($message) / $modifier));
			
			$this->length = $this->hex2str($length);

			$message = $length . $message;
		}

		return $this->hex2str($message);
	}

	public function unpack($message)
	{
		$message = $this->str2hex($message);

		// Getting message length if we have one
		if ($this->options['lengthPrefix'] > 0) {
			$modifier = 2;

			$this->length = $length = (int) hex2bin(substr($message, 0, (int) $this->options['lengthPrefix'] * $modifier));

			$this->shrink($message, (int) $this->options['lengthPrefix'] * $modifier);

			if (strlen($message) != $length * $modifier)
				throw new UnpackError('Message length is ' . strlen($message) / $modifier . ' and should be ' . $length);
		}

		// Parsing MTI 
		$this->setMTI(hex2bin(substr($message, 0, 8)));
		$this->shrink($message, 8);

		// Parsing bitmap
		$bitmap = "";

		for(;;) {
			$tmp = implode(null, array_map(function($bit) {
				return str_pad(base_convert($bit, 16, 2), 8, 0, STR_PAD_LEFT);
			}, str_split($this->hex2str(substr($message, 0, 32)), 2)));

			$this->shrink($message, 32);

			$bitmap .= $tmp;

			if (substr($tmp, 0, 1) !== "1" || strlen($bitmap) > 128) {
				break;
			}
		}

		$this->bitmap = $bitmap;

		// Parsing fields
		for($i=0; $i < strlen($bitmap); $i++) {
			if ($bitmap[$i] === "1") {
				
				$fieldNumber = $i + 1;

				if ($fieldNumber === 1 || $fieldNumber === 65) {
					continue;
				}

				$fieldData = $this->protocol->getFieldData($fieldNumber);
				$fieldMapper = $fieldData['type'];

				if (!isset($this->mappers[$fieldMapper])) {
					throw new \Exception('Unknown field mapper for "' . $fieldMapper . '" type');
				}

				$mapper = new $this->mappers[$fieldMapper]($fieldData['length']);
				$unpacked = $mapper->unpack($message);

				$this->setField($fieldNumber, $unpacked);
			}
		}

		return $this;
	}

	public function getMTI()
	{
		return $this->mti;
	}

	public function setMTI($mti)
	{
		if (!preg_match('/^[0-9]{4}$/', $mti)) {
			throw new Error\UnpackError('Bad MTI field it should be 4 digits string');
		}

		$this->mti = $mti;
	}

	public function set(array $fields)
	{
		$this->fields = $fields;
	}

	public function getFieldsIds()
	{
		$keys = array_keys($this->fields);
		sort($keys);

		return $keys;
	}

	public function getFields()
	{
		ksort($this->fields);

		return $this->fields;
	}

	public function setField($field, $value)
	{
		if (! preg_match('/^(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9]|[1-9])$/', $field))
			throw new \Exception("BIT [$field] IS OUT OF RANGE");

		$this->fields[(int)$field] = $value;
	}

	public function getField($field)
	{
		return isset($this->fields[$field]) ? $this->fields[$field] : null;
	}

	public function getBitmap()
	{
		return $this->bitmap;
	}

	public function getBitmapString()
	{
		return implode(
			null,
			array_map(
				function ($binary)
				{
					return strtoupper(base_convert($binary, 2, 16));
				}, str_split($this->bitmap, 4)
			)
		);
	}

	public function getLength()
	{
		return (int) $this->length;
	}

	public function getLengthString()
	{
		return str_pad($this->length, $this->options['lengthPrefix'], '0', 0);
	}
}
