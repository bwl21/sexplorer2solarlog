<?php
/**
 * Interface class for convert datafiles from solar-inverters into the SolarLog dataformat
 * @author PhotonenSammler <photonensammler@freenet.de>
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * written and tested with PHP 5.4
 */
 
 /**
 * $RCSfile: classMinCur.php $
 * $Date: 2012/05/01 12:34:06 $
 * $Id: classMinCur.php 2169596c5230 2012/05/01 12:34:06 Bernhard $
 * $LocalRevision: 146 $
 * $Revision: 2169596c5230 $
 */
 
/*
  Diese Datei ist ein Teil von InverterData2SolarLog.

  InverterData2SolarLog ist Freie Software: Sie können es unter den Bedingungen
  der GNU General Public License, wie von der Free Software Foundation,
  Version 3 der Lizenz oder jeder späteren veröffentlichten Version,
  weiterverbreiten und/oder modifizieren.

  InverterData2SolarLog wird in der Hoffnung, dass es nützlich sein wird, aber
  OHNE JEDE GEWAERHRLEISTUNG, bereitgestellt; sogar ohne die implizite
  Gewaehrleistung der MARKTFAEHIGKEIT oder EIGNUNG FUER EINEN BESTIMMTEN ZWECK.
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
//include_once 'config.general.php';


class classMinCur {

	private $filename = null;
	private $data = array();
	private $hash = null;

	const minCur = 'min_cur.js'; //Dateiname der days.js

	/**
	 * schreibt/verwaltet die Datei min_cur.js
	 *
	 */

	function __construct($countInverters=1) {
		ini_set('date.timezone', TIMEZONE);
		$this->filename = realpath(SLFILE_DATA_PATH) . '/' . self::minCur;
		if (@file_exists($this->filename)) {
			ini_set('auto_detect_line_endings', true);
			if ($arr = @file($this->filename, FILE_SKIP_EMPTY_LINES && FILE_IGNORE_NEW_LINES)) {
				foreach ($arr as $line) {
					$line = explode('=', $line);
					if (count($line) == 2) {
						$this->data[trim($line[0])] = trim($line[1]);
					}
				}
			}
		}
		$this->hash = md5(serialize($this->data));
		//prüfen, ob alle Einträge da sind
		//var Datum="24.01.12"
		if (!key_exists('var Datum', $this->data)) {
			self::setDatum(time());
		}
		//var Uhrzeit="11:30:00"
		if (!key_exists('var Uhrzeit', $this->data)) {
			self::setUhrzeit(time());
		}
		//var Pac=579
		if (!key_exists('var Pac', $this->data)) {
			self::setPac(0);
		}
		//var aPdc=new Array(315,307,0)
		if (!key_exists('var aPdc', $this->data)) {
			self::setaPdc();
		}
		//var curStatusCode = new Array(x) prüfen
		if (!key_exists('var curStatusCode', $this->data)) {
			$this->data['var curStatusCode'] = 'new Array(' . $countInverters . ')';
			for ($wr = 0; $wr < $countInverters; $wr++) {
				$this->data['curStatusCode[' . $wr . ']'] = 0;
			}
		}
		//var curFehlerCode = new Array(x) prüfen
		if (!key_exists('var curFehlerCode', $this->data)) {
			$this->data['var curFehlerCode'] = 'new Array(' . $countInverters . ')';
			for ($wr = 0; $wr < $countInverters; $wr++) {
				$this->data['curFehlerCode[' . $wr . ']'] = 0;
			}
		}
	}

	/**
	 * Ändert den Eintrag 'var Datum' in der min_cur.js
	 * Das Datum kann als Timestamp, YYYY-MM-DD oder DD.MM.YY übergeben werden
	 *
	 * @param string||timestamp $datum
	 */
	public function setDatum($datum) {
		if (is_integer($datum)) { //Timestamp
			$datum = date('"d.m.y"', $datum);
		} elseif (preg_match('/^\d{2}\.\d{2}\.\d{2}/', $datum)) { //Form DD.MM.YY
			$datum = '"' . substr($datum, 0, 8) . '"';
		} elseif (preg_match('/^\d{4}-\d{2}-\d{2}/', $datum)) { //Form YYYY-MM-DD
			$d = explode('-', substr($datum, 0, 10));
			$datum = '"' . $d[2] . '.' . $d[1] . substr($d[0], 2, 2) . '"';
			unset($d);
		} else { //ungültiges Format
			trigger_error('Ung&uuml;ltiges Datumsformat ' . $datum . ' wurde übergeben');
		}
		$this->data['var Datum'] = $datum;
	}

	/**
	 * Ändert den Eintrag 'var Uhrzeit' in der min_cur.js
	 * Die Uhrzeit kann als timestamp oder in der Form HH:MM:SS übergeben werden
	 *
	 * @param string||timestamp $uhrzeit
	 */
	public function setUhrzeit($uhrzeit) {
		if (is_integer($uhrzeit)) { //Timestamp
			$uhrzeit = date('"h:i:s"', $uhrzeit);
		} else { //Form HH:MM:SS
			$uhrzeit = '"' . $uhrzeit . '"';
		}
		$this->data['var Uhrzeit'] = $uhrzeit;
	}

	/**
	 * Ändert den Eintrag 'var Pac' in der Datei min_cur.js
	 *
	 * @param integer $pac
	 */
	public function setPac($pac) {
		if (is_integer($pac)) {
			$this->data['var Pac'] = $pac;
		} else {
			trigger_error('Ungültiges Format f&uuml;r Pac "' . $pac . '" wurde übergeben');
		}
	}

	/**
	 * Ändert den Eintrag 'var aPdc' in der Datei min_cur.js
	 *
	 * @param array $aPdc1
	 */
	public function setaPdc($aPdc=array(0,0,0)) {
		while(count($aPdc)<3){
			$aPdc[count($aPdc)]=0;
		}
		$this->data['var aPdc'] = 'new Array('.implode(',', $aPdc).')';
	}

	/**
	 * Setzt den Eintrag curStatusCode in der min_cur.js
	 * $wr = 0..COUNT_INVERTERS-1
	 *
	 * @param integer $wr
	 * @param integer $code
	 */
	public function setStatusCode($wr, $code) {
		$this->data['curStatusCode[' . $wr . ']'] = $code;
	}

	/**
	 * Setzt den Eintrag curFehlerCode in der min_cur.js
	 * $wr = 0..COUNT_INVERTERS-1
	 *
	 * @param integer $wr
	 * @param integer $code
	 */
	public function setFehlerCode($wr, $code) {
		$this->data['curFehlerCode[' . $wr . ']'] = $code;
	}

	/**
	 * schreibt die Datei bei Veränderungen neu
	 */
	public function __destruct() {
		if (md5(serialize($this->data)) != $this->hash) {//Daten wurden geändert
			if ($fp = fopen($this->filename, 'wb')) {
				foreach ($this->data as $key => $value) {
					$line = $key . ' = ' . $value;
					if (!@fwrite($fp, $line . chr(13))) {
						trigger_error('Fehler beim Schreiben in Datei ' . $this->filename);
					}
				}
			} else {
				trigger_error('Fehler beim &ouml;ffnen der Datei ' . $this->filename . ' zum Schreiben.');
			}
		}
		unset($this->data);
	}

}

?>
