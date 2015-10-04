<?php

class NutritiveValue
{
	public $nutritiveName;
	public $dailyAmount;
	public $values;

	function __construct($nutritiveName, $dailyAmount, $values = array())
	{
		$this->nutritiveName = $nutritiveName;
		$this->values = $values;
	}

	public function getNutritiveName()
	{
		return $this->nutritiveName;
	}

	public function getDailyAmount()
	{
		return $this->dailyAmount;
	}

	public function getValues()
	{
		return $this->values;
	}
}