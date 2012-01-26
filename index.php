<?php

/*
 * Daten-URL zum Testen http://www.weichel21.de/SunnyExplorer
 * SL-Dateien unter http://www.weichel21.de/SolarLog
 * Link zur Anlage Sonnenertrag http://www.sonnenertrag.eu/de/stuttgart/anlage21/17964/17514.html
 */
include_once 'config.inc.php';


//###########################################################################################################
//######################## main # Hauptprogramm #############################################################
//###########################################################################################################
ini_set('date.timezone', TIMEZONE);
set_error_handler('myErrorHandler');
$min_day = new classMin_day();
$min_day->check();
$base_vars = new classBaseVars();
$base_vars->setOnline($min_day->isOnline());
$min_cur = new classMinCur();
$p = $min_day->getP();
$min_cur->setDatum(substr($p['datum_zeit'], 0, 8));
$min_cur->setUhrzeit(substr($p['datum_zeit'], 9));
$sumP = 0;
for ($wr = 0; $wr < $min_day->getWrAnz(); $wr++) {
	$sumP+=$p[$wr];
}
$min_cur->setPac($sumP);
if (!key_exists(1, $p)) {
	$p[1] = 0;
}
$p[2] = 0;
$min_cur->setaPdc($p[0], $p[1], $p[2]);
unset($min_cur);
if ($min_day->isNewDay()) {//Neuer Tag,andere Dateien auch ergänzen
	$SLObject = new classDaysHist($min_day->getPMax());
	$SLObject->check();
	unset($SLObject);
	$SLObject = new classMonths();
	$SLObject->check();
	unset($SLObject);
	$SLObject = new classYears();
	$SLObject->check();
	unset($SLObject);
}
unset($base_vars, $min_day);
//###########################################################################################################
//###########################################################################################################
//###########################################################################################################

/**
 * Autoload von Klassen
 *
 * @param string $class_name
 */
function __autoload($class_name) {
	include_once 'Classes/' . $class_name . '.php';
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
function myErrorHandler($fehlercode, $fehlertext, $fehlerdatei, $fehlerzeile) {
	ini_set('date.timezone', TIMEZONE);
	if (is_array($fehlertext)) {
		$fehlertext = implode(chr(13), $fehlertext);
	}
	$msg = date('Y-m-d H:i:s', time()) . ' - ' . $fehlertext . ' Code: ' . $fehlercode . ' in Datei: ' . $fehlerdatei . ' Zeile: ' . $fehlerzeile;
	if (!isset($_SERVER['argc'])) {//script wird vom Browser ausgeführt
		if(strtolower(trim(ini_get('display_errors')))!='off'){
			echo str_replace(chr(13), '<br>', $msg) . '<br>';
		}
	}
	if (!($fp = @fopen(ERROR_LOG_FILE, 'ab'))) {
		echo('Fehler beim &Ouml;ffnen der Datei ' . ERROR_LOG_FILE . '<br>');
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
