<?php
	include_once 'Classes/classSLDataFile.php';

	xdebug_disable();

	$events=new classEvents('C:\\Users\\WebAdmin\\Documents\\NetBeansProjects\\SExplorer2Solarlog\\Test\\SolarlogTest\\events.js' ,'e[ev++]');
	//$events->check();


	unset($events);


/**
 * Autoload von Klassen
 *
 * @param string $class_name
 */
function __autoload($class_name) {
	include_once 'Classes/'.$class_name.'.php';
}


?>
