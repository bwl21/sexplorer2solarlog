<?php
/**
 * Program to convert datafiles from Solar-inverters into the SolarLog dataformat
 * @author PhotonenSammler <photonensammler@freenet.de>
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * written and tested with PHP 5.4
 */
/*
  Diese Datei ist ein Teil von InverterData2SolarLog.

  InverterData2SolarLog ist Freie Software: Sie können es unter den Bedingungen
  der GNU General Public License, wie von der Free Software Foundation,
  Version 3 der Lizenz oder jeder späteren veröffentlichten Version,
  weiterverbreiten und/oder modifizieren.

  InverterData2SolarLog wird in der Hoffnung, dass es nützlich sein wird, aber
  OHNE JEDE GEWÄHELEISTUNG, bereitgestellt; sogar ohne die implizite
  Gewährleistung der MARKTFÄHIGKEIT oder EIGNUNG FÜR EINEN BESTIMMTEN ZWECK.
  Siehe die GNU General Public License für weitere Details.

  <http://www.gnu.org/licenses/>

 * ********************************************
  This file is a part of InverterData2SolarLog.

  InverterData2SolarLog is free software: you can use it under the terms of
  the GNU General Public License as published by the Free Software Foundation;
  Version 3 of the License, or any later versions published,
  and distribute / or modify it.

  InverterData2SolarLog was programmed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY provided, without even the implied
  Warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  See the GNU General Public License for more details.

  <http://www.gnu.org/licenses/>
 */

//include_once 'config.solarlog.php';


class classBaseVars {

	const base_vars = 'base_vars.js'; //Dateiname der base_vars.js

	private $data = array();
	private $filename = null;
	private $Hash = null;

	function __construct() {
		$this->filename = realpath(SLFILE_DATA_PATH) . '/' . self::base_vars;
		if (file_exists($this->filename)) {
			ini_set('auto_detect_line_endings', true);
			$arr = @file($this->filename, FILE_SKIP_EMPTY_LINES && FILE_IGNORE_NEW_LINES);
			if ($arr === false) {
				trigger_error('Die Datei ' . $this->filename . ' kann nicht gelesen werden', E_USER_ERROR);
			} else {//Daten aus Datei einlesen
				foreach ($arr as $value) {
					$w = explode('=', $value);
					if (count($w) == 2) {
						$this->data[trim($w[0])] = trim($w[1]);
					}
				}
			}
		}
		$this->Hash = md5(serialize($this->data));
		$this->data['var SLTyp'] = '" - Emulator by www.photonensammler.eu"';
		$this->data['var DATALOGGER_NAME'] = '"Danfoss2SolarLog by PhotonenSammler"';
		$this->data['var Firmware'] = '"Emulator by photonensammler"';
	}

	/**
	 * setzt den Online-Status des WR in der base_vars.js
	 *
	 * @param boolean $status
	 */
	public function setOnline($status) {
		$this->data['var isOnline'] = $status ? 'true' : 'false';
	}

	/**
	 * setzt die Variable var SLDatum in base_vars.js
	 *
	 * @param string $datum
	 */
	public function setSLDatum($datum) {
		$this->data['var SLDatum'] = '"'.$datum.'"';
	}

	/**
	 * setzt die Variable var SLUhrzeit in base_vars.js
	 *
	 * @param string $uhrzeit
	 */
	public function setSLUhrzeit($uhrzeit) {
		$this->data['var SLUhrzeit'] = '"'.$uhrzeit.'"';
	}

	/**
	 * prüft, ob Daten geändert wurden; sowohl Reihenfolge als auch InHalt der Daten im Array
	 *
	 * @return boolean
	 */
	protected function isChanged() {
		return md5(serialize($this->data)) != $this->Hash;
	}

	/**
	 * falls $this->data geändert wurde, wird die Datei neu geschrieben
	 */
	public function __destruct() {
		if (self::isChanged()) {
			if ($fp = @fopen($this->filename, 'wb')) {
				foreach ($this->data as $value => $data) {
					$line = $value . ' = ' . $data . chr(13);
					if (!fwrite($fp, $line)) {
						trigger_error('Fehler beim Schreiben in Datei ' . $this->filename);
					}
				}
			} else {
				trigger_error('Fehler beim Anlegen der Datei ' . $this->filename);
			}
			fclose($fp);
		}
		unset($this->data);
	}


}

?>
