<?php
	include_once 'Classes/classSLDataFile.php';

	xdebug_disable();

	$years=new classYears();
	$years->check();


	unset($years);


/**
 * Autoload von Klassen
 *
 * @param string $class_name
 */
function __autoload($class_name) {
	include_once 'Classes/'.$class_name.'.php';
}


?>
