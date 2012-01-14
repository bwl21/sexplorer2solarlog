<?php

/*
 * Daten-URL zum Testen http://www.weichel21.de/SunnyExplorer
 * Link zur Anlage Sonnenertrag http://www.sonnenertrag.eu/de/stuttgart/anlage21/17964/17514.html
 * @version 0.4
 */

if(function_exists('xdebug_disable')){
	xdebug_disable();
}
include_once 'Classes/classSExplorerDataNeu.php';
include_once 'Classes/classErrorLog.php';
include_once 'config.inc.php';

define('DaysHistKennung', 'da[dx++]=');
define('MonthsKennung', 'mo[mx++]=');

//Schauen, ob für den aktuellen Tag schon eine Datei existiert und daraus die Datei min_day.js erzeugen
$filename = SEXPLORER_DATA_PATH . '/' . CSV_ANLAGEN_NAME .'-'. date('Ymd', time()-86400) . '.csv';
if ($fp = @fopen($filename, 'r')) {
	@fclose($fp);
	$sexpl = new classSExplorerData($filename);
	unset($sexpl);
	//Aus den csv-Dateien die für jeden Monat gebildet werden, months.js und days_hist.js erzeugen
	$aktdate = START_DATUM;
	$enddate = date('Y-m-d', strtotime('+1 month', time()));
	$data = array();
	while ($aktdate < $enddate) {
		$filename = SEXPLORER_DATA_PATH . '/' . CSV_ANLAGEN_NAME . substr($aktdate, 0, 4) . substr($aktdate, 5, 2) . '.csv';
		if ($fp = @fopen($filename, 'r')) {
			@fclose($fp);
			$sexpl = new classSExplorerData($filename);
			$d = $sexpl->getData();
			foreach ($d as $key => $value) {
				if ($key != classSExplorerData::type) {
					$data[$key] = $value;
				}
			}
			unset($d, $sexpl);
		}
		$aktdate = date('Y-m-d', strtotime('+1 month', strtotime($aktdate)));
	}
	//Sortieren
	if (!is_null($data)) {
		uksort($data, "cmp");
		createDays_hist($data);
		createMonths($data);
		unset($data);
	}
}

/**
 * @version 0.3
 * Hilfsfunktion zum Sortieren des Arrays
 * @param type $a
 * @param type $b
 */
function cmp($a, $b) {
	$a = explode('.', $a);
	$b = explode('.', $b);
	return mktime(0, 0, 0, $b[1], $b[0], '20' . $b[2]) - mktime(0, 0, 0, $a[1], $a[0], '20' . $a[2]);
}

/**
 * @version 0.3
 * erzeugt die Datei days_hist.js
 *
 * @param array $data
 */
function createDays_hist($data) {
	$filename = SLFILE_DATA_PATH . '/days_hist.js';
	$fp = @fopen($filename, 'wb');
	if ($fp === false) {
		classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Fehler beim Öffnen von ' . $filename);
	} else {
		foreach ($data as $datum => $value) {
			if (!fwrite($fp, DaysHistKennung . '"' . $datum . '|' . $value[classSExplorerData::etag] . ';0"' . chr(13))) {
				classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Fehler beim Schreiben in ' . $filename);
			}
		}
		@fclose($fp);
	}
}

/**
 * erzeugt die Datei months.js
 *
 * @param array $data
 */
function createMonths($data) {
	$filename = SLFILE_DATA_PATH . '/months.js';
	$fp = @fopen($filename, 'wb');
	if ($fp === false) {
		classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Fehler beim Öffnen von ' . $filename);
	} else {
		reset($data);
		$aktdate = key($data);
		$aktmonth = substr($aktdate, 3);
		$summe = 0;
		foreach ($data as $datum => $value) {
			if (substr($datum, 3) == $aktmonth) {
				$summe+=$value[classSExplorerData::etag];
			} else {
				if (!fwrite($fp, MonthsKennung . '"' . $aktdate . '|' . $summe . '"' . chr(13))) {
					classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Fehler beim Schreiben in ' . $filename);
				}
				$aktdate = $datum;
				$aktmonth = substr($aktdate, 3);
				$summe = 0;
			}
		}
		if ($summe > 0) {
			if (!fwrite($fp, MonthsKennung . '"' . $aktdate . '|' . $summe . '"' . chr(13))) {
				classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Fehler beim Schreiben in ' . $filename);
			}
		}
		@fclose($fp);
	}
}

?>
