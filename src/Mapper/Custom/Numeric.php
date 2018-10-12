<?php
namespace ISO8583\Mapper\Custom;

use ISO8583\Mapper\AbstractMapper;

class Numeric extends AbstractMapper
{
	public function pack($message)
	{
		$packed = $message;

		if ($this->getVariableLength() > 0)
		{
			$length = sprintf('%0' . $this->getVariableLength() . 'd', strlen($packed));
			$packed = $length . $packed;
		}

		return $packed;
	}

	public function unpack(&$message)
	{
		if ($this->getVariableLength() > 0) {
			$start = (int) ceil(strlen(dechex($this->getVariableLength())) / 2) * 2;
			$length = (int) substr($message, 0, $start);
		} else {
			$start = 0;
			$length = $this->getLength();
		}

		$parsed = substr($message, $start, $length);
		$message = substr($message, $start + $length);

		return $parsed;
	}
}