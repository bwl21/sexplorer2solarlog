<?php

/*
 * erzeugt alle Solarlog-Dateien ausgehned vom in der config.inc.php eingetragenen Startdatum
 *
 *  @version 0.1
 */

if (function_exists('xdebug_disable')) {
	xdebug_disable();
}
include_once 'Classes/classSExplorerDataNeu.php';
include_once 'Classes/classErrorLog.php';
include_once 'Classes/classMin_File.php';
include_once 'Classes/classMonths_File.php';
include_once 'Classes/classDaysHist.php';
include_once 'config.inc.php';

// die minxxxx SL-Dateien aus einer Tagesdatei von SExpl erzeugen
$startDate=START_DATUM;
$aktDate=date('Y-m-d',time());
while($startDate<$aktDate){
	$fileDate=str_replace('-', '', $startDate);
	$filename = SEXPLORER_DATA_PATH . '/' . CSV_ANLAGEN_NAME . '-' . $fileDate . '.csv';
	createSLMinFiles($filename);
	$startDate=date('Y-m-d',strtotime($startDate)+86400);
}
createSLMonthsFile();
createSLDaysHistFile();


/**
 * öffnet eine csv-Datei von SunnyExplorer mit Tagesdaten und erzeugt daraus die zugehörigen
 * Solarlog-Dateien mit Tagesdaten (min_day.js und minYYMMDD.js)
 * Bei Erfolg gibt die Funktion True zurück
 *
 * @param string $filename
 * @return boolean
 */
function createSLMinFiles($SExplCSVfilename) {
	if ($fp = @fopen($SExplCSVfilename, 'r')) {
		@fclose($fp);
		$sexpl = new classSExplorerData($SExplCSVfilename);
		$data = $sexpl->getData();
		$min_file = new classMin_File($data);
		$ret = $min_file->createMin_day();
		if ($ret) {
			$ret = $min_file->createMinYYMMDD();
		}
		unset($min_file, $sexpl);
	} else {
		classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Fehler beim Öffnen der Datei ' . $SExplCSVfilename . ' in ' . __METHOD__);
		return false;
	}
	return $ret;
}

/**
 * @version 0.5
 * erzeugt die Datei months.js aus allen vorhandenen csv-Monatsdateien
 */
function createSLMonthsFile() {
	//Aus allen csv-Dateien die für jeden Monat gebildet werden, months.js erzeugen
	$aktdate = START_DATUM;
	$enddate = date('Y-m-d', strtotime('+1 month', time()));
	$data = array();
	while ($aktdate < $enddate) {
		$filename = SEXPLORER_DATA_PATH . '/' . CSV_ANLAGEN_NAME . '-' . substr($aktdate, 0, 4) . substr($aktdate, 5, 2) . '.csv';
		if ($fp = @fopen($filename, 'r')) {
			@fclose($fp);
			$sexpl = new classSExplorerData($filename);
			$d = $sexpl->getData();
			if ($d[classSExplorerData::type] == classSExplorerData::monthly) {
				unset($d[classSExplorerData::type]);
				foreach ($d as $key => $value) {
					if ($key != classSExplorerData::type) {
						$data[$key] = $value;
					}
				}
			}
			unset($d, $sexpl);
		}
		$aktdate = date('Y-m-d', strtotime('+1 month', strtotime($aktdate)));
	}
	$months = new classMonths_File($data);
	$months->createMonths();
	unset($months,$data);
}


/**
 * @version 0.5
 * erzeugt die Datei months.js aus allen vorhandenen csv-Monatsdateien
 */
function createSLDaysHistFile() {
	//Aus allen csv-Dateien die für jeden Monat gebildet werden, months.js erzeugen
	$aktdate = START_DATUM;
	$enddate = date('Y-m-d', strtotime('+1 month', time()));
	$data = array();
	while ($aktdate < $enddate) {
		$filename = SEXPLORER_DATA_PATH . '/' . CSV_ANLAGEN_NAME . '-' . substr($aktdate, 0, 4) . substr($aktdate, 5, 2) . '.csv';
		if ($fp = @fopen($filename, 'r')) {
			@fclose($fp);
			$sexpl = new classSExplorerData($filename);
			$d = $sexpl->getData();
			if ($d[classSExplorerData::type] == classSExplorerData::monthly) {
				unset($d[classSExplorerData::type]);
				foreach ($d as $key => $value) {
					if ($key != classSExplorerData::type) {
						$data[$key] = $value;
					}
				}
			}
			unset($d, $sexpl);
		}
		$aktdate = date('Y-m-d', strtotime('+1 month', strtotime($aktdate)));
	}
	$daysHist = new classDaysHist($data);
	$daysHist->createDays_hist();
	unset($daysHist,$data);
}

?>
