<?php

require_once(dirname(__DIR__).'/vendor/autoload.php');

use Forcast\Forcast;

class Blah
{
	private $blah1 = 1;
	protected $blah2 = 2;
	public $blah3 = 3;
	public static $blah4 = 4;
	protected static $blah5 = 5;
	private static $blah6 = 6;
	public $blah7;

	public function setit($a,$b,$c,$d,$e,$f){
		$this->blah1 = $a;
		$this->blah2 = $b;
		$this->blah3 = $c;
		self::$blah4 = $d;
		self::$blah5 = $e;
		self::$blah6 = $f;
	}

	public function seeit(){
		var_dump(
			$this->blah1,
			$this->blah2,
			$this->blah3,
			self::$blah4,
			self::$blah5,
			self::$blah6
		);
	}
}

$obj = new Blah();
$obj->blah7 = new Blah();


$obj->setit(111,222,333,444,555,666);
Forcast::$obj2array = true;
$ser = Forcast::cast($obj);
$obj->seeit();


$ser['@Blah']['blah1'] = 99;
$ser['@Blah']['blah2'] = 88;
$ser['@Blah']['blah3'] = 77;
$ser['@Blah']['blah4'] = 66;
$ser['@Blah']['blah5'] = 55;
$ser['@Blah']['blah6'] = 44;
unset($ser['@Blah']['blah7']);

$obj2 = Forcast::cast($ser);
var_dump($obj2);
$obj2->seeit();
