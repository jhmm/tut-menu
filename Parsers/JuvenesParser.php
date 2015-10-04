<?php
require_once(dirname(__FILE__).'/../Menu/MealOption.php');
require_once(dirname(__FILE__).'/../Menu/MenuItem.php');
require_once(dirname(__FILE__).'/../Menu/NutritiveValue.php');
require_once(dirname(__FILE__).'/../Menu/Restaurant.php');
require_once(dirname(__FILE__).'/../Menu/UnitValuePair.php');
require_once(dirname(__FILE__).'/../Menu/WeekDay.php');

class JuvenesParser
{
	private $restaurants;
	private $weekDays;

	function __construct(array &$weekDays)
	{
		$this->restaurants = array(
			array('kitchenId' => '6', 'menuTypeId' => '60', 'extraName' => 'Lounas'),
			array('kitchenId' => '6', 'menuTypeId' => '74', 'extraName' => 'Rohee Xtra'),
			array('kitchenId' => '60038', 'menuTypeId' => '77', 'extraName' => 'Såås Bar'),
			array('kitchenId' => '60038', 'menuTypeId' => '3', 'extraName' => 'Fusion Kitchen'),
		);
		$this->weekDays = $weekDays;
	}

	public function getMenu($lang)
	{
		for($i = 0; $i < count($this->weekDays); $i++)
		{
			$restaurantMenus = array();
			foreach($this->restaurants as $restaurantArray)
			{

				$weekDay = $this->weekDays[$i];
				$dateStr = $weekDay->getYear().'-'.$weekDay->getMonth().'-'.$weekDay->getDay();
				$date = new DateTime($dateStr);
				$weekNumber = $date->format("W");
				$weekDay = $date->format("N");

				echo 'Fetching ' . $dateStr . ' (Juvenes)<br />';
				$restaurant = $this->getRestaurant($restaurantArray['kitchenId'], $restaurantArray['menuTypeId'], $restaurantArray['extraName'], $weekNumber, $weekDay, $i, $lang);
				array_push($restaurantMenus, $restaurant);
			}

			$this->weekDays[$i]->addRestaurants($restaurantMenus);
		}
	}

	private function getRestaurant($restaurantId, $menuTypeId, $extraName, $week, $weekDay, $lang)
	{
		$url = 'http://www.juvenes.fi/DesktopModules/Talents.LunchMenu/LunchMenuServices.asmx/GetMenuByWeekday?'
			.'KitchenId='.$restaurantId.'&MenuTypeId='.$menuTypeId.'&Week='.$week.'&Weekday='.$weekDay.'&lang=%27'.$lang.'%27&format=json'
			.'&callback=jQuery16408886524771805853_1438643680062&_=1438643722567';
		$juvenesData = file_get_contents($url);

		$first = strpos($juvenesData, '(');
		$juvenesData = substr($juvenesData, $first+1);
		$juvenesData = substr($juvenesData, 0, strlen($juvenesData)-2);
		$juvenesData = urldecode($juvenesData);
 
		$juvenesArray = json_decode($juvenesData, true);
		$juvenesArray = json_decode($juvenesArray['d'], true);

		$mealOptions = array();
		foreach($juvenesArray['MealOptions'] as $mealOption)
		{	
			if(array_key_exists('MenuItems', $mealOption))
			{
				$menuItems = array();

				foreach($mealOption['MenuItems'] as $menuItem)
				{

					if(!array_key_exists('Name', $menuItem))
					{
						continue;
					}

					$menuItemName = $menuItem['Name'];
					$diets = array();
					$ingredients = '';
					$nutritiveValues = array();

					// Diets
					if(array_key_exists('Diets', $menuItem))
					{
						$diets = explode(',', $menuItem['Diets']);
					}

					// Ingredients
					if(array_key_exists('Ingredients', $menuItem))
					{
						$ingredients = $menuItem['Ingredients'];
					}

					// Nutritive values
					if(array_key_exists('NutritiveValues', $menuItem))
					{
						foreach($menuItem['NutritiveValues'] as $arrNutritiveValue)
						{
							if(!array_key_exists('Name', $arrNutritiveValue))
							{
								continue;
							}

							$nutritiveName = $arrNutritiveValue['Name'];
							$dailyAmount = NULL;
							$values = array();

							if(array_key_exists('DailyAmount', $arrNutritiveValue))
							{
								$dailyAmount = $arrNutritiveValue['DailyAmount'];
							}

							if(array_key_exists('Units', $arrNutritiveValue))
							{
								foreach($arrNutritiveValue['Units'] as $unit)
								{
									if(array_key_exists('Unit', $unit) && array_key_exists('Value', $unit))
									{
										$unitValuePair = new UnitValuePair($unit['Value'], $unit['Unit']);
										array_push($values, $unitValuePair);
									}

								}					
							}

							$nutritiveValue = new NutritiveValue($nutritiveName, $dailyAmount, $values);
							array_push($nutritiveValues, $nutritiveValue);
						}			
					}
			
					$menuItem = new MenuItem($menuItemName, $diets, $ingredients, $nutritiveValues);
					array_push($menuItems, $menuItem);
			
				}
			}

			$mealOption = new MealOption($menuItems);
			array_push($mealOptions, $mealOption);
		}

		return new Restaurant($juvenesArray['KitchenName'].' - '.$extraName, $mealOptions);		
	}
}