<?php
namespace ISO8583\Mapper\Custom;

use ISO8583\Mapper\AbstractMapper;

class AlphaNumeric extends AbstractMapper
{
	public function pack($message)
	{
		$packed = bin2hex($message);

		if ($this->getVariableLength() > 0)
		{
			$hexLength = (int) ceil(strlen(dechex($this->getVariableLength())) / 2) * 4;
			$length = sprintf('%0' . $hexLength . 'd', strlen($packed) / 2);
			$packed = $length . $packed;
		}

		return $packed;
	}

	public function unpack(&$message)
	{
		if ($this->getVariableLength() > 0) {
			$hexLength = (int) ceil(strlen(dechex($this->getVariableLength())) / 2) * 4;
			$length = (int) substr($message, 0, $hexLength);
		} else {
			$hexLength = 0;
			$length = $this->getLength();
		}

		$parsed = hex2bin(substr($message, $hexLength, $length * 2));
		$message = substr($message, $hexLength + ($length * 2));

		return $parsed;
	}
}