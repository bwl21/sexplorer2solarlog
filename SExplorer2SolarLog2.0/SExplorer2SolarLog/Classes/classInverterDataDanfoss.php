<?php

/**
 * Program to convert datafiles from Danfoss-inverters into the SolarLog dataformat
 * @author PhotonenSammler <photonensammler@freenet.de>
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * written and tested with PHP 5.4
 */
 
 /**
 * $RCSfile: classInverterDataDanfoss.php $
 * $Date: 2012/05/13 20:30:35 $
 * $Id: classInverterDataDanfoss.php 4dae32f3a0aa 2012/05/13 20:30:35 Bernhard $
 * $LocalRevision: 150 $
 * $Revision: 4dae32f3a0aa $
 */
 
/*
  Diese Datei ist ein Teil von Danfoss2SolarLog.

  Danfoss2SolarLog ist Freie Software: Sie können es unter den Bedingungen
  der GNU General Public License, wie von der Free Software Foundation,
  Version 3 der Lizenz oder jeder späteren veröffentlichten Version,
  weiterverbreiten und/oder modifizieren.

  Danfoss2SolarLog wird in der Hoffnung, dass es nützlich sein wird, aber
  OHNE JEDE GEWAEHRLEISTUNG, bereitgestellt; sogar ohne die implizite
  Gewaehrleistung der MARKTFAEHIGKEIT oder EIGNUNG FUER EINEN BESTIMMTEN ZWECK.
  Siehe die GNU General Public License fuer weitere Details.

  <http://www.gnu.org/licenses/>

 * ********************************************
  This file is a part of Danfoss2SolarLog.

  Danfoss2SolarLog is free software: you can use it under the terms of
  the GNU General Public License as published by the Free Software Foundation;
  Version 3 of the License, or any later versions published,
  and distribute / or modify it.

  Danfoss2SolarLog was programmed in the hope that it will be useful,
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


include_once 'config.danfoss.php';
include_once 'classInverterDataInterface.php';

class classInverterData implements classInverterDataInterface {

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
//	/*
//	 * Online-Status inverters determined from the newest measuring values
//	 *
//	 * array
//	 * 		[0] => true  //online-status of inverter 1
//	 * 		[1] => false //online-status of inverter 2	 */
//	private $isOnline = array(); //Online-Status inverters
//	/*
//	 * Array with PAC max values
//	 *
//	 * array
//	 *  '2012-03-12' => 1234 //pmax value PAC on date
//	 * 		array
//	 * 				0 => integer 135 //pmax inverter 1
//	 * 				1 => integer 205 //pmax inverter 2
//	 *  '2012-03-13' => //Date of pmax value
//	 * 		array
//	 * 				0 => integer 1400
//	 * 				1 => integer 2505	 */
//	private $pmax = array();

	/*
	 * Überschriften der verwendeten Spalten aus den Dateien des WR */
	var $HEADLINE = array('TIMESTAMP', 'SERIAL', 'P_AC', 'E_DAY', 'T_WR',
			'U_DC_1', 'I_DC_1', 'U_DC_2', 'I_DC_2', 'U_DC_3', 'I_DC_3');

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
			$searchFileNames[] = DANFOSS_PLANT_NAME . '-*';
		} else {
			$endDate = date('ymd');
			$lastHour = date('H', $startDate);
			$i = 0;
			do {
				$stDate = date('ymd', $startDate + $i * 86400);
				if ($stDate == $endDate) {
					$endHour = date('H');
				} else {
					$endHour = 23;
				}
				for ($hour = $lastHour; $hour <= $endHour; $hour++) {
					$searchFileNames[] = DANFOSS_PLANT_NAME . '-' . $stDate . str_pad($hour, 2, '0', STR_PAD_LEFT) . '*';
				}
				$lastHour = 0;
				$i++;
			} while ($stDate < $endDate);
		}
		if (USE_FTP) {
			$conn = ftp_connect(FTP_SERVER, FTP_PORT);
			if ($conn === false) {
				trigger_error(date('Y-m-d H:i:s') . ' fatal error when connect FTP-server "' . FTP_SERVER . '" in ' . __METHOD__ . ' line ' . __LINE__);
				die(6);
			}
			if (!ftp_login($conn, FTP_USERNAME, FTP_PASSWORD)) {
				ftp_close($conn);
				trigger_error(date('Y-m-d H:i:s') . ' fatal error at login on FTP-server "' . FTP_SERVER . '" in ' . __METHOD__ . ' line ' . __LINE__);
				die(7);
			}
			if (!ftp_pasv($conn, true)) {
				ftp_close($conn);
				trigger_error(date('Y-m-d H:i:s') . ' fatal error when turning on the passive mode on "' . FTP_SERVER . '" in ' . __METHOD__ . ' line ' . __LINE__);
				die(8);
			}
			if (!ftp_set_option($conn, FTP_TIMEOUT_SEC, FTP_TIMEOUT)) {
				ftp_close($conn);
				trigger_error(date('Y-m-d H:i:s') . ' fatal error when setting FTP_TIMEOUT on "' . FTP_SERVER . '" in ' . __METHOD__ . ' line ' . __LINE__);
				die(11);
			}
			if (defined('FTP_INVERTER_DATA_PATH')) {
				if (!ftp_chdir($conn, FTP_INVERTER_DATA_PATH)) {
					ftp_close($conn);
					trigger_error(date('Y-m-d H:i:s') . ' fatal error when switching FTP-directroie to "' . FTP_INVERTER_DATA_PATH . '" on "' . FTP_SERVER . '" in ' . __METHOD__ . ' line ' . __LINE__);
					die(9);
				}
			}
			$files = ftp_nlist($conn, '.');
			if ($files == false) {
				ftp_close($conn);
				trigger_error(date('Y-m-d H:i:s') . ' fatal error when redaing files in FTP directorie"' . FTP_INVERTER_DATA_PATH . '" on "' . FTP_SERVER . '" in ' . __METHOD__ . ' line ' . __LINE__);
				die(10);
			}
			$dataPath = (defined('LOCAL_TEMP_DIR')) ? ((is_null(LOCAL_TEMP_DIR)) ? sys_get_temp_dir() : LOCAL_TEMP_DIR) : sys_get_temp_dir();
			$fileNames = array();
			foreach ($searchFileNames as $fileName) {
				$fileName = str_replace('*', '', $fileName);
				$regex = preg_quote($fileName, '/');
				foreach ($files as $remote_file_name) {
					if (preg_match('/.*' . $regex . '\d{4,12}$/', $remote_file_name)) {
						$remote_file_name = preg_replace('/^\W*/', '', $remote_file_name);
						$tempFileName = $dataPath . '/' . $remote_file_name; // zukünftiger Dateiname auf dem system
						//Datei runterladen
						if (ftp_get($conn, $tempFileName, $remote_file_name, FTP_BINARY)) {
							$fileNames[0][] = $tempFileName;
						} else {
							trigger_error(date('Y-m-d H:i:s') . ' fatal error when downloading FTP-file "' . $remote_file_name . '" from "' . FTP_SERVER . '" in ' . __METHOD__ . ' line ' . __LINE__);
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
				if (($x !== false)&&(count($x)>0)) {
					$fileNames[] = $x;
				}
			}
		}
		unset($searchFileNames);
		if (count($fileNames) > 0) {
			$tempSerNoWr = array();
			$usedStrings = array();
			foreach ($fileNames as $fileName1) {//Daten aller Dateien einlesen
				foreach ($fileName1 as $fileName) {
					if (preg_match('/.+\d{12}$/', $fileName)) {//Gültigkeit des Dateinamens prüfen, 12 Zahlen am Ende
						if (($handle = @fopen($fileName, 'rb', false)) === false) {
							trigger_error(date('Y-m-d H:i:s') . ' fatal error when opening file "' . $fileName . '"');
						} else {
							//Datei einlesen
							$headLineFound = false;
							while (($arr = fgetcsv($handle, 0, DELIMITER)) !== false) {
								if (!$headLineFound) { //Kopfzeile der Daten suchen
									$headLine = array_intersect($arr, $this->HEADLINE);
									if ($headLineFound = (count($headLine) == count($this->HEADLINE))) {//Kopfzeile gefunden
										$headLine = array_flip($headLine);
										$indexSerial = $headLine['SERIAL'];
										$indexTime = $headLine['TIMESTAMP'];
										unset($headLine['SERIAL'], $headLine['TIMESTAMP']);
									}
								} else { //Zeile mit Daten
									if (is_numeric($arr[0])) {//Zeile mit Daten
										foreach ($headLine as $key => $value) {
											$time = $arr[$indexTime];
											if (strtotime($time) > $startDate) {
												$serial = $arr[$indexSerial];
												if (!in_array($serial, $tempSerNoWr)) {
													$tempSerNoWr[] = $serial;
													$serNoWR = array_flip($tempSerNoWr);
													//Benutzte Strings des WR ermitteln
													for ($wr = 0; $wr < count($serNoWR); $wr++) {
														$st='USED_STRINGS_' . ($wr + 1);
														if(!defined($st)){
															trigger_error(date('Y-m-d H:i:s') .
																			' missing definition "' . $st .
																			'" in config file for inverter ' . ($wr+1) .
																			' with serial number "'.$tempSerNoWr[$wr].'" in data file  "'.$fileName.'" => this data are not used');
															unset($serNoWR[$tempSerNoWr[$wr]]);
															unset($tempSerNoWr[$wr]);
															goto ignore_data;
														}else{
															$usedStrings[$wr] = constant($st);
															if (!is_null($usedStrings[$wr])) {
																$usedStrings[$wr] = explode(',', $usedStrings[$wr]);
																for ($i = 0; $i < count($usedStrings[$wr]); $i++) {
																	$usedStrings[$wr][$i]--;
																}
															}
														}
													}
												}
												switch ($key) {
													case 'U_DC_1':
													case 'U_DC_2':
													case 'U_DC_3':
													case 'I_DC_1':
													case 'I_DC_2':
													case 'I_DC_3':
														$i = substr($key, -1) - 1;
														if (in_array($i, $usedStrings[$serNoWR[$serial]])) {
															$key1 = substr($key, 0, 4);
															$this->data[$time][$serNoWR[$serial]][array_search($i, $usedStrings[$serNoWR[$serial]])][$key1] = $arr[$value]; //nicht runden !!!
														}
														break;
													case 'E_DAY':
														$this->data[$time][$serNoWR[$serial]][$key] = intval(round($arr[$value] * 1000)); //in Wh umrechnen
														break;
													default:
														$this->data[$time][$serNoWR[$serial]][$key] = intval(round($arr[$value]));
														break;
												}
											}
										}
									}
								}
							}
							@fclose($handle);
						}
					}
					ignore_data: //Absprungmarke bei unbekannter wr-Seriennummer
					if (USE_FTP) { //Dateien in temp wieder löschen
						unlink($fileName);
					}
				}
			}
			//aus allen U_DC und I_DC der Strings P_DC erzeugen und I_DC löschen
			unset($fileNames, $fileName1, $headLine, $serNoWR, $usedStrings);
			foreach ($this->data as $datum => $values) {
				foreach ($values as $wr => $werte) {
					foreach ($werte as $string => $werte1) {
						if (is_array($werte1)) {
							$this->data[$datum][$wr][$string]['P_DC'] = round($werte1['U_DC'] * $werte1['I_DC']);
							$this->data[$datum][$wr][$string]['U_DC'] = round($werte1['U_DC']);
							unset($this->data[$datum][$wr][$string]['I_DC']);
						}
					}
				}
			}
			$c=count($tempSerNoWr);
			unset($werte, $werte1, $tempSerNoWr);
			if($c>1){ //Prüfen, ob für alle WR auch Daten vorhanden sind
				foreach($this->data as $key=>$values){
					if(count($values)<$c){
						unset($this->data[$key]);
					}
				}
			}
			unset($values);
			self::sort();
		} //keine neuen Dateien gefunden
	}

	public function deleteValue($forDate){
		if(isset($this->data[$forDate])){
			unset($this->data[$forDate]);
		}
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
		unset($this->data, $this->HEADLINE);
	}

}

?>
