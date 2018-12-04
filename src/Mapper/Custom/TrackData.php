<?php
namespace ISO8583\Mapper\Custom;

use ISO8583\Mapper\AbstractMapper;

class TrackData extends AbstractMapper
{
	public function pack($message)
	{
		$packed = $message;

		if ($this->getVariableLength() > 0)
		{
			$hexLength = (int) ceil($this->getVariableLength() / 2) * 2;
			$packedLength = strlen($packed);
			
			# Track 2 Special Fix
			if ($this->getLength() == 38)
				$packedLength = min($packedLength, 37);

			$length = sprintf('%0' . $hexLength . 'd', $packedLength);
			$packed = $length . $packed;
		}

		return $packed;
	}

	public function unpack(&$message)
	{
		if ($this->getVariableLength() > 0) {
			$hexLength = (int) ceil($this->getVariableLength() / 2) * 2;
			$length = (int) substr($message, 0, $hexLength);
			if ($length % 2 == 1) $length++;
		} else {
			$hexLength = 0;
			$length = $this->getLength();
		}

		$parsed = substr($message, $hexLength, $length);
		$message = substr($message, $hexLength + $length);

		return $parsed;
	}
}