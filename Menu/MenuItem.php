<?php

class MenuItem
{
	public $menuItemName;
	public $diets;
	public $ingredients;
	public $nutritiveValues;

	function __construct($menuItemName, array $diets = array(), $ingredients = '', $nutritiveValues = array())
	{
			$this->menuItemName = $menuItemName;
			$this->diets = $diets;
			$this->ingredients = $ingredients;
			$this->nutritiveValues = $nutritiveValues;
	}

	public function getMenuItemName()
	{
		return $this->menuItemName;
	}

	public function getDiets()
	{
		return $this->diets;
	}

	public function getIngredients()
	{
		return $this->ingredients;
	}

	public function getNutritiveValues()
	{
		return $this->nutritiveValues;
	}
}