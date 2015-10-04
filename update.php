<?php
ob_start();
header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once('Parsers/JuvenesParser.php');
require_once('Parsers/AmicaParser.php');
require_once('Parsers/SodexoParser.php');

echo "<pre>";

$weekDays = array();

// add 2 hours to initial time in order to make sure we won't have any problems with summer 
// times etc (maybe they wouldn't be an issue anyway?)
$unixTime = strtotime('last Monday', strtotime('tomorrow'))+7200; 

for($day = 0; $day < 14; $day++)
{			
	$year = date('Y', $unixTime);
	$month = date('m', $unixTime);
	$dayOfMonth = date('d', $unixTime);

	$weekDay = new WeekDay($year, $month, $dayOfMonth, array());
	array_push($weekDays, $weekDay);

	$unixTime += 86400; // add one day
}


$juvenes = new JuvenesParser($weekDays);
$juvenesMenu = $juvenes->getMenu('fi');

$amica = new AmicaParser($weekDays);
$amicaMenu = $amica->getMenu('fi');

$sodexo = new SodexoParser($weekDays);
$sodexoMenu = $sodexo->getMenu('fi');

//print_r($weekDays);
//print_r($amicaMenu);
//print_r($sodexoMenu);

if(!empty($weekDays))
{
	file_put_contents('menus.json', json_encode( (array)$weekDays ));
}

echo "</pre>";

ob_end_flush();
?>