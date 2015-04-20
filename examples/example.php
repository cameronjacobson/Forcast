<?php

require_once(dirname(__DIR__).'/vendor/autoload.php');

use Forcast\Forcast;

class Blah
{
	private $blah1 = 1;
	protected $blah2 = 2;
	public $blah3 = 3;
	public static $blah4 = 4;
	public static $blah5 = 5;
	public static $blah6 = 6;
	public $blah7;

	public static function seeit(){
		var_dump(self::$blah4,self::$blah5,self::$blah6);
	}
}
class Blah2
{
	private $blah1 = 1;
	protected $blah2 = 2;
	public $blah3 = 3;
	public static $blah4 = 4;
	public static $blah5 = 5;
	public static $blah6 = 6;
	public $blah7;

	public static function seeit(){
		var_dump(self::$blah4,self::$blah5,self::$blah6);
	}
}


$values = array(
	array('@Blah'=>array(
		'blah1'=>11,
		'blah2'=>22,
		'blah3'=>33,
		'blah4'=>44,
		'blah5'=>55,
		'blah6'=>66,
		'blah7'=>77
	))
);

$values[0]['@Blah']['blah3'] = new Blah();
$values[0]['@Blah']['blah7'] = new Blah();

$values[0]['@Blah']['blah7']->blah7 = array('@Blah'=>array(
	'blah1'=>'a',
	'blah2'=>'b',
	'blah3'=>'c',
	'blah4'=>'d',
	'blah5'=>'e',
	'blah6'=>'f',
	'blah7'=>'g',
));

$values[0]['@Blah']['blah3']->blah3 = array('@Blah'=>array(
	'blah1'=>'a',
	'blah2'=>'b',
	'blah3'=>'c',
	'blah4'=>'d',
	'blah5'=>'e',
	'blah6'=>'d',
	'blah7'=>'g',
));
//var_dump($values[0]['@Blah']['blah7']);
//exit;
foreach($values as $key => $value){
	echo 'TEST#: '.$key.PHP_EOL;

	$start = microtime(true);
	$ser = Forcast::cast($value);
	var_dump($ser);
	Blah::seeit();

	echo ' '.(microtime(true) - $start).PHP_EOL;
}

