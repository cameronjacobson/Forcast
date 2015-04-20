<?php

namespace Forcast;

use \ReflectionClass;
use \ReflectionProperty;
use \ReflectionFunction;
use \ReflectionException;

class Forcast
{
	private static $callback;
	private static $replace_callback;
	public static $cb_flag = '#';
	public static $obj_flag = '@';
	public static $replace_flag = '!';
	public static $obj2array = false;
	public static $wakeup = false;
	private static $objects = array();
	private static $number;

	public static function setCallback(callable $cb){
		if(self::isValidCallback($cb, 2)){
			self::$callback = $cb;
			return true;
		}
		return false;
	}

	public static function setReplaceCallback(callable $cb){
		if(self::isValidCallback($cb, 1)){
			self::$replace_callback = $cb;
			return true;
		}
		return false;
	}

	public static function cast($anything){
		$anything = self::cast_internal($anything);
		self::$objects = array();
		if(count(self::$objects) > 0){
			foreach(self::$objects as $object){
				method_exists($obj,'__wakeup') ? $obj->__wakeup() : '';
			}
		}
		return $anything;
	}

	private static function cast_internal($anything){
		if(is_null($anything)
		 || is_int($anything)
		 || is_bool($anything)
		 || is_float($anything)
		 || is_string($anything)){
			return $anything;
		}
		else if(is_array($anything)){
			if((count($anything) === 1) && (key($anything)[0] === self::$obj_flag)){
				return self::castArray2Object(substr(key($anything),1),$anything[key($anything)]);
			}
			else if((count($anything) === 1)
			 && (key($anything)[0] === self::$replace_flag)
			 && !empty(self::$replace_callback)
			 && is_callable(self::$replace_callback)){
				$anything = self::$replace_callback(substr(key($anything),1),$anything[key($anything)]);
				return self::cast($anything);
			}
			else{
				foreach($anything as $k=>$v){
					if(($k[0] === self::$cb_flag)
					 && !empty(self::$callback)
					 && is_callable(self::$callback)){
						list($k,$v) = self::$callback($k,$v);
					}
					$stuff[$k] = self::cast($v);
				}
				return $stuff;
			}
		}
		else if(is_object($anything)){
			return self::$obj2array ? self::castObject2Array(get_class($anything), $anything) : self::castObject($anything);
		}
	}

	private static function isValidCallback($callback, $numargs){
		$rf = new ReflectionFunction($callback);
		if($rf->getNumberOfParameters() !== 2){
			return false;
		}
		return true;
	}

	private static function castArray2Object($classname, array $arr){
		$rc = new ReflectionClass($classname);

		$obj = $rc->newInstanceWithoutConstructor();

		$props = $rc->getProperties();
		foreach($props as $prop){
			$prop->setAccessible(true);
			$prop->setValue($obj, self::cast($arr[$prop->getName()]));
		}
		return $obj;
	}

	private static function castObject2Array($classname, $obj){
		$rc = new ReflectionClass($classname);
		$props = $rc->getProperties();
		$return = array();
		foreach($props as $prop){
			$prop->setAccessible(true);
			$return[$prop->getName()] = self::cast($prop->getValue($obj));
		}
		return array(self::$obj_flag.$classname => $return);
	}

	private static function castObject($obj){
		$classname = get_class($obj);
		$rc = new \ReflectionClass($obj);
		$props = $rc->getProperties();
		$obj2 = self::copyObjectProperties($classname, $obj, $props);
		return $obj2;
	}

	private static function copyObjectProperties($classname, $obj, $props){
		foreach($props as $k=>$prop){
			$prop->setAccessible(true);
			$prop->setValue($obj, self::cast($prop->getValue($obj)));
		}
		return $obj;
	}
}

