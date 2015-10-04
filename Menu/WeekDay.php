<?php

class WeekDay
{
	public $year;
	public $month;
	public $day;
	public $restaurants;

	function __construct($year, $month, $day, $restaurants = array())
	{
		$this->year = $year;
		$this->month = $month;
		$this->day = $day;
		
		$this->restaurants = $restaurants;
	}

	public function getYear()
	{
		return $this->year;
	}

	public function getMonth() 
	{
		return $this->month;
	}

	public function getDay()
	{
		return $this->day;
	}

	public function addRestaurants(array $restaurants)
	{
		foreach($restaurants as $restaurant)
		{
			array_push($this->restaurants, $restaurant);
		}
	}
}