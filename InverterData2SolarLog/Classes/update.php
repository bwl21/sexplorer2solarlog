<?php

/**
 * Beschreibung von $RCSfile: update.php $
 *
 * Hauptproramm zur Konvertierung von SMA SunnyExplorer nach SolarLog
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 *
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 *
 */
  
 /**
 * $RCSfile: update.php $
 * $Date: 2012/06/16 17:58:52 $
 * $Id: update.php 4cdc2d05a44c 2012/06/16 17:58:52 Bernhard $
 * $LocalRevision: 160 $
 * $Revision: 4cdc2d05a44c $
 */
 

/**
 * Program to convert datafiles from SExplorer TLX Solar-inverters into the SolarLog dataformat
 * @author PhotonenSammler <photonensammler@freenet.de>
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * written and tested with PHP 5.4
 */
/*
  Diese Datei ist ein Teil von SExplorer2SolarLog.

  SExplorer2SolarLog ist Freie Software: Sie können es unter den Bedingungen
  der GNU General Public License, wie von der Free Software Foundation,
  Version 3 der Lizenz oder jeder späteren veröffentlichten Version,
  weiterverbreiten und/oder modifizieren.

  SExplorer2SolarLog wird in der Hoffnung, dass es nützlich sein wird, aber
  OHNE JEDE GEWÄHELEISTUNG, bereitgestellt; sogar ohne die implizite
  Gewährleistung der MARKTFÄHIGKEIT oder EIGNUNG FÜR EINEN BESTIMMTEN ZWECK.
  Siehe die GNU General Public License für weitere Details.

  <http://www.gnu.org/licenses/>

 * ********************************************
  This file is a part of SExplorer2SolarLog.

  SExplorer2SolarLog is free software: you can use it under the terms of
  the GNU General Public License as published by the Free Software Foundation;
  Version 3 of the License, or any later versions published,
  and distribute / or modify it.

  SExplorer2SolarLog was programmed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY provided, without even the implied
  Warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  See the GNU General Public License for more details.

  <http://www.gnu.org/licenses/>
 */

/*
 * you must execute this script in a cronjob whenever new inverter data available
 * this script converts the inverter data into solarlog format
 * for more information visit http://photonensammler.homedns.org/SExplorer2SolarLog
 */

include_once realpath(dirname(__FILE__)).'/classDays.php';
include_once realpath(dirname(__FILE__)).'/classSLDataFile.php'; 
include_once realpath(dirname(__FILE__)).'/classMinYYMMDD.php';
include_once realpath(dirname(__FILE__)).'/classMin_day.php';
include_once realpath(dirname(__FILE__)).'/classBaseVars.php';
include_once realpath(dirname(__FILE__)).'/classMinCur.php';
include_once realpath(dirname(__FILE__)).'/classYears.php';
include_once realpath(dirname(__FILE__)).'/classMonths.php';
include_once realpath(dirname(__FILE__)).'/classDaysHist.php';
include_once realpath(dirname(__FILE__)).'/classInverterDataFactory.php';

//###########################################################################################################
//######################## main # Hauptprogramm #############################################################
//###########################################################################################################
if (function_exists('xdebug_disable')) {
	xdebug_disable();
}
ini_set('date.timezone', TIMEZONE);
ini_set('max_execution_time', 0);
set_error_handler('myErrorHandler');


$min_day = new classMin_day();
$pdc = $min_day->getaPdc();
if (!is_null($pdc)) { //es gibt veränderungen
	$pac = $min_day->getaPac();
	$sumP=0;
	foreach ($pac as $value) {
		$sumP +=$value;
	}
	$base_vars = new classBaseVars();
	$base_vars->setOnline($sumP > 0); //Onlinestatus WR setzen
	$base_vars->setSLDatum(substr($pdc['datum_zeit'], 0, 8));
	$base_vars->setSLUhrzeit(substr($pdc['datum_zeit'], 9));
	unset($base_vars);
	$min_cur = new classMinCur(count($pac));
	$min_cur->setDatum(substr($pdc['datum_zeit'], 0, 8));
	$min_cur->setUhrzeit(substr($pdc['datum_zeit'], 9));
	unset($pdc['datum_zeit']);
	$min_cur->setPac($sumP);
	$min_cur->setaPdc($pdc);
	foreach ($pac as $wr=>$value) {
		$min_cur->setStatusCode($wr, $value>0?1:0);
		$min_cur->setFehlerCode($wr,0);
	}
	unset($min_cur, $pdc, $sumP,$pac);
	if ($min_day->isNewDay()) {//Neuer Tag,andere Dateien auch ergänzen
		$slObject = new classYears(new classMonths(new classDaysHist($min_day)));
		unset($slObject);
	}
}
unset($min_day);

//###########################################################################################################
//###########################################################################################################
//###########################################################################################################

/**
 * Error Handler - Schreibt die Fehlermeldung in die Logdatei für Fehler
 * Wird das Script aus dem Browser ausgeführt, wird die Fehlermeldung zusätzlich ausgegeben
 *
 * @param integer $errorCode		error code
 * @param string|array $errorText  error description
 * @param string $errorScript		script, in which the error occured
 * @param integer $errorLine		line in script in which the error occured
 */
function myErrorHandler($errorCode, $errorText, $errorScript, $errorLine) {
	ini_set('date.timezone', TIMEZONE);
	if (is_array($errorText)) {
		$errorText = implode(chr(13), $errorText);
	}
	$msg = date('Y-m-d H:i:s', time()) . ' - ' . $errorText . ' Code: ' . $errorCode . ' in Datei: ' . $errorScript . ' Zeile: ' . $errorLine;
	if (!isset($_SERVER['argc'])) {//script wird vom Browser ausgeführt
		echo str_replace(chr(13), '<br>', $msg) . '<br>';
	}
	if (!($fp = @fopen(ERROR_LOG_FILE, 'ab'))) {
		echo('Fehler beim &Ouml;ffnen der Datei ' . ERROR_LOG_FILE . '<br>');
		die(1);
	}
	if (!fwrite($fp, $msg . chr(13))) {
		echo('Fehler beim Schreiben in Datei ' . ERROR_LOG_FILE . '<br>');
		fclose($fp);
		die(2);
	}
	fclose($fp);
	
switch ($errorCode) {
    case E_USER_ERROR:
        echo "\nEERROR [$errorCode] $errorText<br />\n";
        echo "  Fatal error on line $errorLine in file $errorScript";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...\n";
        exit(1);
        break;

    case E_USER_WARNING:
        echo "\nWARNING [$errorCode] $errorText\n";
        break;

    case E_USER_NOTICE:
        echo "\nNOTICE [$errorCode] $errorText\n";
        break;

    default:
        echo "\nUNKNOWN [$errorCode] $errorText<br />\n";
        echo "  error on line $errorLine in file $errorScript";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")\n";
        break;
    }

	
	return TRUE; //True php-interne Fehlerbehandlung wird nicht mehr ausgeführt
}

?>
