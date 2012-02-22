<?php

/**
 * Beschreibung von $RCSfile: index.php $
 *
 * Hauptproramm zur Konvertierung von SMA SunnyExplorer nach SolarLog
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 *
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 *
 *
 * $Date: 2012/02/22 16:50:14 $
 * $Id: index.php 6815b370ccd5 2012/02/22 16:50:14 WebAdmin $
 * $LocalRevision: 101 $
 * $Revision: 6815b370ccd5 $
 */

/*

    Diese Datei ist Teil von SExplore2SlLog.

    SExplore2SlLog ist Freie Software: Sie können es unter den Bedingungen
    der GNU General Public License, wie von der Free Software Foundation,
    Version 3 der Lizenz oder jeder späteren veröffentlichten Version,
    weiterverbreiten und/oder modifizieren.

    FuSExplore2SlLog wird in der Hoffnung, dass es nützlich sein wird, aber
    OHNE JEDE GEWÄHELEISTUNG, bereitgestellt; sogar ohne die implizite
    Gewährleistung der MARKTFÄHIGKEIT oder EIGNUNG FÜR EINEN BESTIMMTEN ZWECK.
    Siehe die GNU General Public License für weitere Details.

    <http://www.gnu.org/licenses/>

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
$p = $min_day->getP();
if(isset($p)){ //es gibt Veränderungen
	$min_cur = new classMinCur();
	$min_cur->setDatum(substr($p['datum_zeit'], 0, 8));
	$min_cur->setUhrzeit(substr($p['datum_zeit'], 9));
	$base_vars->setSLDatum(substr($p['datum_zeit'], 0, 8));
	$base_vars->setSLUhrzeit(substr($p['datum_zeit'], 9));
	$sumP = 0;
	for ($wr = 0; $wr < $min_day->getWrAnz(); $wr++) {
		$sumP+=$p[$wr];
	}
	$min_cur->setPac($sumP);
	for ($wr = 0; $wr < $min_day->getWrAnz(); $wr++) {
		if($sumP>0){
			$min_cur->setStatusCode($wr, 1);
		}else{
			$min_cur->setStatusCode($wr, 255);
		}
	}
	if (!key_exists(1, $p)) {
		$p[1] = 0;
	}
	$p[2] = 0;
	$min_cur->setaPdc($p[0], $p[1], $p[2]);
	unset($min_cur);
}
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
