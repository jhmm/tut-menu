<?php

class Restaurant
{
	public $name;
	public $mealOptions;

	function __construct($restaurantName, $mealOptions = array() )
	{
		$this->name = $restaurantName;
		$this->mealOptions = $mealOptions;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getMealOptions()
	{
		return $this->mealOptions;
	}
}
