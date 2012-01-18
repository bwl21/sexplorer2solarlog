<?php

/*
 * Daten-URL zum Testen http://www.weichel21.de/SunnyExplorer
 * SL-Dateien unter http://www.weichel21.de/SolarLog
 * Link zur Anlage Sonnenertrag http://www.sonnenertrag.eu/de/stuttgart/anlage21/17964/17514.html
 * @version 0.5
 */
include_once 'config.inc.php';


//###########################################################################################################
//######################## main # Hauptprogramm #############################################################
//###########################################################################################################
	set_error_handler('myErrorHandler');
	if (function_exists('xdebug_disable')) {
		xdebug_disable();
	}
	$min_day=new classMin_day();
	$min_day->check();
	if($min_day->isNewDay()){//Neuer Tag,andere Dateien auch ergänzen
		$days=new classDaysHist();
		$months=new classMonths();
		$years=new classYears();
		unset($years,$months,$days);
	}
	unset($min);
//###########################################################################################################
//###########################################################################################################
//###########################################################################################################


/**
 * Autoload von Klassen
 *
 * @param string $class_name
 */
function __autoload($class_name) {
	include_once 'Classes/'.$class_name.'.php';
}


/**
 * Error Handler - Schreibt die Fehlermeldung in die Logdatei für Fehler
 * Wird das Script aus dem Browser ausgeführt, wird die Fehlermeldung zusätzlich ausgegeben
 *
 * @param integer $fehlercode
 * @param string|array $fehlertext
 * @param string $fehlerdatei
 * @param integer $fehlerzeile
 */
function myErrorHandler($fehlercode, $fehlertext, $fehlerdatei, $fehlerzeile){
	if (is_array($fehlertext)) {
		$fehlertext = implode(chr(13), $fehlertext);
	}
	$msg=date('Y-m-d',time()).' - '.$fehlertext.' Code: '.$fehlercode.' in Datei: '.$fehlerdatei.' Zeile: '.$fehlerzeile;
	if(!isset($_SERVER['argc'])){//script wird vom Browser ausgeführt
		echo str_replace(chr(13), '<br>', $msg).'<br>';
	}
	if (!($fp = @fopen(ERROR_LOG_FILE, 'ab'))) {
		echo('Fehler beim Öffnen der Datei ' . ERROR_LOG_FILE . '<br>');
		die(1);
	}
	if (!fwrite($fp, $msg . chr(13))) {
		echo('Fehler beim Schreiben in Datei ' . ERROR_LOG_FILE . '<br>');
		die(2);
	}
	fclose($fp);
	return TRUE; //True php-interne Fehlerbehandlung wird nicht mehrausgeführt
}

?>
