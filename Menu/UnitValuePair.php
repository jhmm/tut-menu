<?php

class UnitValuePair
{
	public $value;
	public $unit;

	function __construct($value, $unit)
	{
		$this->value = $value;
		$this->unit = $unit;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getUnit()
	{
		return $this->unit;
	}
}