<?php
require_once(dirname(__FILE__).'/../Menu/MealOption.php');
require_once(dirname(__FILE__).'/../Menu/MenuItem.php');
require_once(dirname(__FILE__).'/../Menu/NutritiveValue.php');
require_once(dirname(__FILE__).'/../Menu/Restaurant.php');
require_once(dirname(__FILE__).'/../Menu/UnitValuePair.php');
require_once(dirname(__FILE__).'/../Menu/WeekDay.php');

class SodexoParser
{
	private $restaurants;

	function __construct(array &$weekDays)
	{
		$this->restaurants = array('12812');
		$this->weekDays = $weekDays;
	}

	public function getMenu($lang)
	{
		for($i = 0; $i < count($this->weekDays); $i++)
		{
			$restaurantMenus = array();
			foreach($this->restaurants as $restaurantId)
			{

				$weekDay = $this->weekDays[$i];

				$dateStr = $weekDay->getYear().'-'.$weekDay->getMonth().'-'.$weekDay->getDay();
				echo 'Fetching ' . $dateStr . ' (Sodexo)<br />';
				$restaurant = $this->getRestaurant(
					$restaurantId,
					$weekDay->getYear(), 
					$weekDay->getMonth(),
					$weekDay->getDay(),
					$lang);

				array_push($restaurantMenus, $restaurant);
			}

			$this->weekDays[$i]->addRestaurants($restaurantMenus);
		}
	}

	private function getRestaurant($restaurantId, $year, $month, $day, $lang)
	{
		$url = 'http://www.sodexo.fi/ruokalistat/output/daily_json/'.$restaurantId.'/'.$year.'/'.$month.'/'.$day.'/'.$lang;
		$sodexoData = file_get_contents($url);
		$sodexoArray = json_decode($sodexoData, true);

		$mealOptions = array();
		foreach($sodexoArray['courses'] as $course)
		{	
			$menuItems = array();
			if(!array_key_exists('title_'.$lang, $course))
			{
				continue;
			}

			$menuItemName = $course['title_'.$lang];
			$diets = array();
			$ingredients = '';
			$nutritiveValues = array();
			$category  = '';

			// Diets
			if(array_key_exists('properties', $course))
			{
				$diets = explode(', ', $course['properties']);
			}

			// Category/meal option name
			if(array_key_exists('category', $course))
			{
				$category = explode(', ', $course['category']);
			}

			
			$menuItem = new MenuItem($menuItemName, $diets, $ingredients, $nutritiveValues);
			array_push($menuItems, $menuItem);
			
				
			$mealOption = new MealOption($menuItems, $category);
			array_push($mealOptions, $mealOption);
		}

		$restaurantName = 'Sodexo';		
		if(array_key_exists('meta', $sodexoArray))
		{
			if(array_key_exists('ref_title', $sodexoArray['meta']))
			{
				$restaurantName = $sodexoArray['meta']['ref_title'];
			}
		}

		return new Restaurant($restaurantName, $mealOptions);		
	}
}