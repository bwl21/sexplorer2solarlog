<?php

/**
 * Program to convert datafiles from SunnyExplorer-Datafiles into the SolarLog dataformat
 * @author PhotonenSammler <photonensammler@freenet.de>
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * written and tested with PHP 5.4
 */
 
 /**
 * $RCSfile: classInverterDataSunnyExplorer.php $
 * $Date: 2012/05/01 12:34:06 $
 * $Id: classInverterDataSunnyExplorer.php 2169596c5230 2012/05/01 12:34:06 Bernhard $
 * $LocalRevision: 146 $
 * $Revision: 2169596c5230 $
 */
 
/*
  Diese Datei ist ein Teil von SExplorer2SolarLog.

  SExplorer2SolarLog ist Freie Software: Sie können es unter den Bedingungen
  der GNU General Public License, wie von der Free Software Foundation,
  Version 3 der Lizenz oder jeder späteren veröffentlichten Version,
  weiterverbreiten und/oder modifizieren.

  SExplorer2SolarLog wird in der Hoffnung, dass es nützlich sein wird, aber
  OHNE JEDE GEWAEHRLEISTUNG, bereitgestellt; sogar ohne die implizite
  Gewaehrleistung der MARKTFAEHIGKEIT oder EIGNUNG FUER EINEN BESTIMMTEN ZWECK.
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

/* general information
 *
 * Return parameters when running for example as cmd-process in Windows
 * and critical errors that lead to program termination
 * 0 -> everything flawlessly executed, any non-critical errors have been logged in the error file
 * 1 -> The file for the logging of error messages can not be opened
 * 2 -> In the file for error-logging can not be written
 * 3 -> error in the constructor (see in error logfile)
 * 4 -> error in the constructor (see in error logfile)
 * 5 -> error in the constructor (see in error logfile)
 * 6 -> error on ftp_connect
 * 7 -> error on ftp_login
 * 8 -> error on ftp_pasv
 * 9 -> error on ftp_chdir
 * 10 -> error on ftp_nlist
 * 11 -> error on ftp_set_option FTP_TIMEOUT
 * All other error messages are logged in the error logfile
 */


//include_once 'config.sexplorer.php';
include_once 'classInverterDataInterface.php';

class classInverterDataSunnyExplorer implements classInverterDataInterface {

	/**
	 * format of $this->data
	 *
	 *  '2012-03-12 15:20:00' =>						//Date Time of value
	 *    array
	 *      0 =>													//Inverter 1
	 *        array
	 *          'P_AC' => integer 3417				//PAC power in W must be present
	 *          'E_DAY' => integer 26				//yield of day at the date/time in Wh must be present
	 *          'T_WR' => integer 37				//Temperature inverter 1 in °C if present otherwise omit
	 *          0 =>											//Inverter 1 String 1 must be present because a inverter has at least one string
	 *            array
	 *              'U_DC' => integer 428		//UDC of String 1 in V must be present, can also be 0
	 *              'P_DC' => integer 1739		//PDC power of String 1 in W must be present
	 *          1 =>											//Inverter 1 String 2 if present, otherwise omit
	 *            array
	 *              'U_DC' => integer 427		//in V
	 *              'P_DC' => integer 1727		//in W
	 *          2 =>											//inverter 1 string 3 if present, otherwise omit
	 *            array
	 *              'U_DC' => integer 0
	 *              'P_DC' => integer 0
	 *      1 =>													//Inverter 2 if present, otherwise omit - see description of inverter 1
	 *        array
	 *          'P_AC' => integer 3417
	 *          'E_DAY' => integer 26
	 *          'T_WR' => integer 37
	 *          0 =>											//inverter 2 String 1
	 *            array
	 *              'U_DC' => integer 428
	 *              'P_DC' => integer 1739
	 *          1 =>											//inverter 2 String 2
	 *            array
	 *              'U_DC' => integer 427
	 *              'P_DC' => integer 1727
	 *          2 =>											//inverter 2 String 3
	 *            array
	 *              'U_DC' => integer 0
	 *              'P_DC' => integer 0
	 *  '2012-03-12 15:15:00' =>						//Date Time of the previous value
	 *    array
	 *      0 =>													//Inverter 1
	 *        array
	 *          'P_AC' => integer 3417				//in W must be present
	 *          'E_DAY' => integer 26				//in Wh must be present
	 * 									.
	 * 									.
	 * 									.
	 * 									.
	 * 									.
	 */
	private $data = array();
	
	
	function __construct() {
	    
	}

	/**
	 * @param timestamp||string||0 $startDate
	 */
	function loadData($startDate = 0) {
		ini_set('auto_detect_line_endings', true);
		ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', 0);
		if (is_string($startDate)) {
			$startDate = strtotime($startDate);
		}
		//Dateinamen für Datei suchen zum Datum bilden
		if ($startDate == 0) { //Noch keine Daten, alle passenden Dateien suchen
			$searchFileNames[] = SEXPLORER_PLANT_NAME . '????????.' . SEXPLORER_FILE_EXT;
		} else {
			$endDate = date('Ymd');
			$i = 0;
			do {
				$stDate = date('Ymd', $startDate + $i * 86400);
				$searchFileNames[] = SEXPLORER_PLANT_NAME . $stDate . '.' . SEXPLORER_FILE_EXT;
				$i++;
			} while ($stDate < $endDate);
		}
		if (USE_FTP) {
			$conn = ftp_connect(FTP_SERVER, FTP_PORT);
			if ($conn === false) {
				trigger_error('Fatal error when connect FTP-server "' . FTP_SERVER . '" in ' . __METHOD__ . ' line ' . __LINE__);
				die(6);
			}
			if (!ftp_login($conn, FTP_USERNAME, FTP_PASSWORD)) {
				ftp_close($conn);
				trigger_error('Fatal error at login on FTP-server "' . FTP_SERVER . '" in ' . __METHOD__ . ' line ' . __LINE__);
				die(7);
			}
			if (!ftp_pasv($conn, true)) {
				ftp_close($conn);
				trigger_error('Fatal error when turning on the passive mode on "' . FTP_SERVER . '" in ' . __METHOD__ . ' line ' . __LINE__);
				die(8);
			}
			if (!ftp_set_option($conn, FTP_TIMEOUT_SEC, FTP_TIMEOUT)) {
				ftp_close($conn);
				trigger_error('Fatal error when setting FTP_TIMEOUT on "' . FTP_SERVER . '" in ' . __METHOD__ . ' line ' . __LINE__);
				die(11);
			}
			if (defined('FTP_INVERTER_DATA_PATH')) {
				if (!ftp_chdir($conn, FTP_INVERTER_DATA_PATH)) {
					ftp_close($conn);
					trigger_error('Fatal error when switching FTP-directroie to "' . FTP_INVERTER_DATA_PATH . '" on "' . FTP_SERVER . '" in ' . __METHOD__ . ' line ' . __LINE__);
					die(9);
				}
			}
			$files = ftp_nlist($conn, '.');
			if ($files == false) {
				ftp_close($conn);
				trigger_error('Fatal error when redaing files in FTP directorie"' . FTP_INVERTER_DATA_PATH . '" on "' . FTP_SERVER . '" in ' . __METHOD__ . ' line ' . __LINE__);
				die(10);
			}
			$dataPath = (defined('LOCAL_TEMP_DIR')) ? ((is_null(LOCAL_TEMP_DIR)) ? sys_get_temp_dir() : LOCAL_TEMP_DIR) : sys_get_temp_dir();
			$fileNames = array();
			foreach ($searchFileNames as $fileName) {
				if (strpos($fileName, '?') !== false) {
					$fileName = str_replace('?', '', $fileName);
					$fileName = pathinfo($fileName, PATHINFO_FILENAME);
					$regex = preg_quote($fileName, '/') . '\d{8}\.' . SEXPLORER_FILE_EXT . '$';
				} else {
					$regex = preg_quote($fileName, '/') . '$';
				}
				foreach ($files as $remote_file_name) {
					if (preg_match('/' . $regex . '/', $remote_file_name)) {
						$remote_file_name = preg_replace('/^\W*/', '', $remote_file_name);
						$tempFileName = $dataPath . '/' . $remote_file_name; // zukünftiger Dateiname auf dem system
						//Datei runterladen
						if (ftp_get($conn, $tempFileName, $remote_file_name, FTP_BINARY)) {
							$fileNames[] = $tempFileName;
						} else {
							trigger_error('Fatal error when downloading FTP-file "' . $remote_file_name . '" from "' . FTP_SERVER . '" in ' . __METHOD__ . ' line ' . __LINE__);
						}
					}
				}
			}
			ftp_close($conn);
			unset($files);
		} else {
			$fileNames = array();
			$dataPath = LOCAL_INVERTER_DATA_PATH;
			foreach ($searchFileNames as $fileName) {
				$x = glob($dataPath . '/' . $fileName);
				if (($x !== false) && (count($x) > 0)) {
					foreach ($x as $filename) {
						$fileNames[] = $filename;
					}
				}
				unset($x);
			}
		}
		unset($searchFileNames);
		if (count($fileNames) > 0) {
			sort($fileNames, SORT_STRING);
			$kWhColumns = explode(',', SEXPLORER_YIELDSUM_COLUMN); //Spaltennummern, in denen die kWh stehen
			$kWColumns = explode(',', SEXPLORER_POWER_COLUMN); //Spaltennummern, in denen die kW stehen
			$yearPos = strpos(SEXPLORER_DATE_FORMAT, 'yyyy');
			$monthPos = strpos(SEXPLORER_DATE_FORMAT, 'MM');
			$dayPos = strpos(SEXPLORER_DATE_FORMAT, 'dd');
			$hourPos = strpos(SEXPLORER_DATE_FORMAT, 'HH');
			$minutePos = strpos(SEXPLORER_DATE_FORMAT, 'mm');
			$wrAnz = null;
			foreach ($fileNames as $fileName) {//Daten aller Dateien einlesen
				if (preg_match('/.+\d{8}\.' . SEXPLORER_FILE_EXT . '$/', $fileName)) {//Gültigkeit des Dateinamens prüfen, 8 Zahlen am Ende
					if (($handle = @fopen($fileName, 'rb', false)) === false) {
						trigger_error('Fatal error when opening file "' . $fileName . '"');
					} else {
						//Datei einlesen
						$dateFound = false;
						$wrAnz = null;
						$startkWh = array();
						$first = null;
						$last = null;
						$index = 0;
						$temp = array();
						$startkWhSet = false;
						//So lange suchen, bis die Zeile mit dem Datum gefunden wird
						while (($arr = fgetcsv($handle, 0, DELIMITER)) !== false) {
							if (!$dateFound) { //Datum Daten suchen
								$dateFound = $arr[SEXPLORER_DATE_COLUMN - 1] == SEXPLORER_DATE_FORMAT;
								if ($dateFound) {
									if (is_null($wrAnz)) {
										$wrAnz = floor(count($arr) / 2);
									} else {
										if ($wrAnz != floor(count($arr) / 2)) { //Fehler, die Anzahl wr hat sich geändert
											trigger_error('The count of inverters (' . (floor(count($arr) / 2)) . ') found in "' . $fileName . '" is diffrent between the count of inverters (' . $wrAnz . ') found in other data files!');
											$wrAnz != floor(count($arr) / 2); //Mit neuer WR-Anzahl weitermachen
										}
									}
								}
							} else { //Zeile mit Daten
								$time = substr($arr[SEXPLORER_DATE_COLUMN - 1], $yearPos, 4) . '-' .
												substr($arr[SEXPLORER_DATE_COLUMN - 1], $monthPos, 2) . '-' .
												substr($arr[SEXPLORER_DATE_COLUMN - 1], $dayPos, 2) . ' ' .
												substr($arr[SEXPLORER_DATE_COLUMN - 1], $hourPos, 2) . ':' .
												substr($arr[SEXPLORER_DATE_COLUMN - 1], $minutePos, 2);
								if (!$startkWhSet) {
									for ($wr = 0; $wr < $wrAnz; $wr++) {
										$startkWh[$wr] = str_replace(SEXPLORER_DECIMALPOINT, '.', $arr[$kWhColumns[$wr] - 1]);
									}
									$startkWhSet=true;
								}
								if (strtotime($time) > $startDate) {
									for ($wr = 0; $wr < $wrAnz; $wr++) {
										$kw = str_replace(SEXPLORER_DECIMALPOINT, '.', $arr[$kWColumns[$wr] - 1]);
										$temp[$index][$time][$wr]['P_AC'] = round($kw * 1000);
										$temp[$index][$time][$wr]['E_DAY'] = round((str_replace(SEXPLORER_DECIMALPOINT, '.', $arr[$kWhColumns[$wr] - 1]) - $startkWh[$wr]) * 1000);
										if (is_null($first) && ($kw > 0)) {
											$first = $index;
										}
										if ($kw > 0) {
											$last = $index;
										}
									}
									$index++;
								}
							}
						}
						@fclose($handle);
						if (!is_null($first)) { //Daten >0 vorhanden
							if ($first > 0) {
								$first--;
							}
							if ($last < count($temp) - 1) {
								$last++;
							}
							for ($index = $first; $index <= $last; $index++) {//Daten übernehmen
								$w = reset($temp[$index]);
								$time = key($temp[$index]);
								for ($wr = 0; $wr < $wrAnz; $wr++) {
									$this->data[$time . ':00'][$wr]['P_AC'] = intval(floor(0.97 * $w[$wr]['P_AC'])); //Wirkungsgrad 99% simulieren
									$this->data[$time . ':00'][$wr]['E_DAY'] = $w[$wr]['E_DAY'];
									$this->data[$time . ':00'][$wr][0]['U_DC'] = 0;
									$this->data[$time . ':00'][$wr][0]['P_DC'] = $w[$wr]['P_AC'];
								}
								unset($w);
							}
							unset($temp);
						}
					}
				}
				if (USE_FTP) { //Dateien in temp wieder löschen
					unlink($fileName);
				}
			}
			//aus allen U_DC und I_DC der Strings P_DC erzeugen und I_DC löschen
			unset($fileNames, $kWColumns, $kWhColumns, $startkWh);
			self::sort();
		} //keine neuen Dateien gefunden
	}

	/**
	 * Die Funktion gibt die Anzahl WR zurück, die aus der Datei ermittelt wurde
	 * sind keine neuen Daten vorhanden, gibt die Funktion false zurück
	 * @return integer||false
	 */
	public function getInverterCount() {
		return (count($this->data) > 0) ? count(reset($this->data)) : false;
	}

	/**
	 * funktion gibt die Werte, die zum Datum gehören als array zurück
	 * Ist kein Wert zum Datum vorhanden wird false zurückgegeben
	 *
	 *  '2012-03-12 15:20:00' =>						//Date Time of value
	 *    array
	 *      0 =>													//Inverter 1
	 *        array
	 *          'P_AC' => integer 3417				//PAC power in W must be present
	 *          'E_DAY' => integer 26				//yield of day at the date/time in Wh must be present
	 *          'T_WR' => integer 37				//Temperature inverter 1 in °C if present otherwise omit
	 *          0 =>											//Inverter 1 String 1 must be present because a inverter has at least one string
	 *            array
	 *              'U_DC' => integer 428		//UDC of String 1 in V must be present, can also be 0
	 *              'P_DC' => integer 1739		//PDC power of String 1 in W must be present
	 *          1 =>											//Inverter 1 String 2 if present, otherwise omit
	 *            array
	 *              'U_DC' => integer 427		//in V
	 *              'P_DC' => integer 1727		//in W
	 *          2 =>											//inverter 1 string 3 if present, otherwise omit
	 *            array
	 *              'U_DC' => integer 0
	 *              'P_DC' => integer 0
	 *      1 =>													//Inverter 2 if present, otherwise omit - see description of inverter 1
	 *        array
	 *          'P_AC' => integer 3417
	 *          'E_DAY' => integer 26
	 *          'T_WR' => integer 37
	 *          0 =>											//inverter 2 String 1
	 *            array
	 *              'U_DC' => integer 428
	 *              'P_DC' => integer 1739
	 *          1 =>											//inverter 2 String 2
	 *            array
	 *              'U_DC' => integer 427
	 *              'P_DC' => integer 1727
	 *          2 =>											//inverter 2 String 3
	 *            array
	 *              'U_DC' => integer 0
	 *              'P_DC' => integer 0
	 *  '2012-03-12 15:15:00' =>						//Date Time of the previous value
	 *    array
	 *      0 =>													//Inverter 1
	 *        array
	 *          'P_AC' => integer 3417				//in W must be present
	 *          'E_DAY' => integer 26				//in Wh must be present
	 * 									.
	 * 									.
	 * 									.
	 * 									.
	 * 									.
	 * @param string||timestamp $date date as timestamp or YYYY-MM-DD or YYYY-MM-DD HH:II:SS
	 * @return array||false
	 */
	public function getDailyData($date) {
		$ret = false;
		if (is_int($date)) {
			$date = date('Y-m-d', $date);
		}
		$date = substr($date, 0, 10);
		foreach ($this->data as $datum => $value) {
			$dat = substr($datum, 0, 10);
			if ($dat == $date) {
				$ret[$datum] = $value;
			}
			if ($dat < $date) {
				break;
			}
		}
		return $ret;
	}

	/**
	 * gibt das größte (jüngste, neueste) Datum der Daten in der Form DD.MM.YY oder DD.MM.YY HH:NN:SS zurück
	 * wenn keine Daten vorhanden sind, wird false zurückgegeben
	 *
	 * @return string|false
	 */
	public function getNewestDate() {
		reset($this->data);
		return (count($this->data) > 0) ? key($this->data) : false;
	}

	/**
	 * gibt das kleinste (älteste) Datum der Daten in der Form DD.MM.YY HH:NN:SS zurück
	 * wenn noch keine Daten vorhanden sind, wird false zurückgegeben
	 *
	 * @return string|false
	 */
	public function getOldestDate() {
		end($this->data);
		return(count($this->data) > 0) ? key($this->data) : false;
	}

	/**
	 * sortiert self::$data absteigend - neuestes Datum zuerst
	 */
	private function sort() {
		krsort($this->data, SORT_REGULAR);
	}

	public function __destruct() {
		unset($this->data);
	}

}

?>
