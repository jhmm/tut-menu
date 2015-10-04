<?php

class MealOption
{
	public $menuItems;
	public $name;
	public $prices;

	function __construct($menuItems = array(), $name='', array $prices = array())
	{
		$this->menuItems = $menuItems;
		$this->name = $name;
		$this->prices = $prices;
	}

	public function getMenuItems()
	{
		return $this->menuItems;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getPrices()
	{
		return $this->prices;
	}
}