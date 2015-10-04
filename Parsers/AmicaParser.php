<?php
require_once(dirname(__FILE__).'/../Menu/MealOption.php');
require_once(dirname(__FILE__).'/../Menu/MenuItem.php');
require_once(dirname(__FILE__).'/../Menu/NutritiveValue.php');
require_once(dirname(__FILE__).'/../Menu/Restaurant.php');
require_once(dirname(__FILE__).'/../Menu/UnitValuePair.php');
require_once(dirname(__FILE__).'/../Menu/WeekDay.php');

class AmicaParser
{
	private $restaurants;

	function __construct(array &$weekDays)
	{
		$this->restaurants = array('0812');
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
				echo 'Fetching ' . $dateStr . ' (Amica)<br />';
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
		$amicaData = file_get_contents('http://www.amica.fi/modules/json/json/Index?costNumber='
			.$restaurantId.'&language='.$lang.'&firstDay='.$year.'-'.ltrim($month, '0').'-'.ltrim($day, '0'));

		$amicaArray = json_decode($amicaData, true);

		$mealOptions = array();
		foreach($amicaArray['MenusForDays'] as $dailyMenu)
		{	

			if(array_key_exists('SetMenus', $dailyMenu) 
				&& array_key_exists('Date', $dailyMenu)
				&& $year.'-'.$month.'-'.$day == date('Y-m-d', strtotime($dailyMenu['Date']))
			)
			{
				foreach($dailyMenu['SetMenus'] as $mealOption)
				{
					$mealOptionName = '';
					$mealOptionPrice = '';

					if(array_key_exists('Name', $mealOption))
					{
						$mealOptionName = $mealOption['Name'];
					}

					if(array_key_exists('Price', $mealOption))
					{
						$mealOptionPrice = $mealOption['Price'];
					}

					if(array_key_exists('Components', $mealOption))
					{
						$menuItems = array();

						foreach($mealOption['Components'] as $menuItem)
						{
							$menuItemName = $menuItem;
							$diets = array();
							$ingredients = '';
							$nutritiveValues = array();

							// Diets
							//if(array_key_exists('Diets', $menuItem))
							//{
						//		foreach($menuItem['Diets'] as $diet)
					//			{
					//				array_push($diets, $diet);
					//			}
					//		}
					
							$menuItem = new MenuItem($menuItemName, $diets, $ingredients, $nutritiveValues);
							array_push($menuItems, $menuItem, array($mealOptionPrice));
					
						}
					}


					$mealOption = new MealOption($menuItems, $mealOptionName);
					array_push($mealOptions, $mealOption);
				}
			}
		}

		$restaurantName = 'Amica';		
		if(array_key_exists('RestaurantName', $amicaArray))
		{
			$restaurantName = $amicaArray['RestaurantName'];
		}

		return new Restaurant($restaurantName, $mealOptions);		
	}
}