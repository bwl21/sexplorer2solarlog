<?php

/**
 * Interface class for convert datafiles from solar-inverters into the SolarLog dataformat
 * @author PhotonenSammler <photonensammler@freenet.de>
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * written and tested with PHP 5.4
 */
 
 /**
 * $RCSfile$
 * $Date$
 * $Id$
 * $LocalRevision$
 * $Revision$
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
//include_once 'classSLDataFile.php';
//include_once 'classInverterData.php';
//include_once 'classMinYYMMDD.php';
//include_once 'classDays.php';
//include_once 'InverterDataFactory.php';

class classMin_day extends classSLDataFile {

	private $isNewDay = false; //True wenn ein neuer Tag
	private $pMax = null; //pMax aller bearbeiteten Tage auf einem Array


	/* Format $this-Y>eTag Array mit den Tageserträgen aller bearbeiteten Tage einschließlich dem aktuellen tag
	 *
	 * array (size=4)
	 *  '18.03.12' =>
	 *    array (size=2)
	 *      0 => int 4221		//WR 1 Tagesertrag in Wh
	 *      1 => int 3998		//WR 2 Tagesertrag in Wh
	 *  '19.03.12' =>
	 *    array (size=2)
	 *      0 => int 37306		//WR 1 Tagesertrag in Wh
	 *      1 => int 34221		//WR 2 Tagesertrag in Wh
	 *  '20.03.12' =>
	 *    array (size=2)
	 *      0 => int 8418		//WR 1 Tagesertrag in Wh
	 *      1 => int 5776		//WR 2 Tagesertrag in Wh
	 *  '21.03.12' =>
	 *    array (size=2)
	 *      0 => int 36321		//WR 1 Tagesertrag in Wh
	 *      1 => int 42215		//WR 2 Tagesertrag in Wh	 */
	private $eTag = null; //eTag aller bearbeiteten Tage auf einem Array

	const min_day = 'min_day.js'; //Dateiname der min_day.js
	const kennung = 'm[mi++]';

	function __construct() {
		ini_set('date.timezone', TIMEZONE);
		$x = SLFILE_DATA_PATH;
		parent::__construct(realpath(SLFILE_DATA_PATH) . '/' . self::min_day, self::kennung);
		self::update();
	}

	/**
	 * gibt True zurück wenn ein neuer Tag verarbeitet wurde
	 *
	 * @return boolean
	 */
	public function isNewDay() {
		return $this->isNewDay;
	}

	/**
	 * gibt PMax für alle erzeugten Dateien auf einem Array zurück ohne aktuellem Tag
	 *
	 * @return array
	 */
	public function getPMax() {
		return $this->pMax;
	}

	public function getEDay() {
		return $this->eTag;
	}

	/**
	 * gibt die aktuellen Momentanleistungen PDC der Strings der WR auf einem Array zurück
	 * wenn es keine Veränderung gibt (keine neuen Werte vorhanden) wird null zurückgegeben
	 *
	 * array (size=3)
	 * 		'datum_zeit' => string '29.03.12 20:24:00'
	 * 		0 => int 0  //String1 Summe aller PDC
	 * 		1 => int 0	//String2 Summe aller PDC
	 *
	 * @return array||null array(SumPDC1,..,..,SumPDCx)
	 */
	public function getaPdc() {
		$data = self::getData();
		if (is_null($data)) {
			return null; //keine Veränderung im Status, keine neuen Daten
		}
		$w = reset($data);
		$ret['datum_zeit'] = key($data);
		unset($data);
		foreach ($w as $wr => $werte) {
			$strings = floor((count($werte) - 2) / 2); //Anzahl Strings
			for ($string = 0; $string < $strings; $string++) {
				if (isset($ret[$string])) {
					$ret[$string] +=$werte[$string + 1]; //PDC zum String
				} else {
					$ret[$string] = $werte[$string + 1]; //PDC zu String
				}
			}
		}
		return $ret;
	}

	/**
	 * gibt die aktuellen Momentanleistungen PAC aller WR auf einem Array zurück
	 * wenn es keine Veränderung gibt (keine neuen Werte vorhanden) wird null zurückgegeben
	 *
	 * array (size=1)
	 *		0 => int 1200	 //WR1 PAC
	 *		1 => int 255	 //WR2 PAC
	 *
	 * @return array||null
	 */
	public function getaPac() {
		$d = self::getData();
		if (is_null($d)) {
			return null; //keine Veränderung im Status, keine neuen Daten
		}
		$w = reset($d);
		unset($d);
		$ret = array();
		foreach ($w as $wr => $werte) {
			$ret[$wr] = $werte[0]; //PAC zum WR
		}
		return $ret;
	}

	/**
	 * prüft die Aktualität der min_day.js und erzeugt bei Bedarf auch die minYYMMDD.js
	 */
	private function update() {
		$st_createMinForDate = self::getNewestDatum();
		if ($st_createMinForDate === false) {
			$st_inverter_aktDate = 0;
		} else {
			$st_inverter_aktDate = $st_createMinForDate;
		}
		$inverterData = InverterDataFactory::getInverter(INVERTER_TYPE); // new classInverterData($st_inverter_aktDate);
		$inverterData -> loadData($st_inverter_aktDate);
		$st_inverter_aktDate = substr($inverterData->getOldestDate(), 0, 10);
		if ($st_inverter_aktDate !== false) {//ansosntern keine neuen Daten
			$st_inverter_endDate = $inverterData->getNewestDate();
			if ($st_createMinForDate === false) {
				$st_createMinForDate = $st_inverter_aktDate;
				$this->isNewDay = true;
			} else {
				$this->isNewDay = substr($st_createMinForDate, 0, 10) < substr($st_inverter_endDate, 0, 10);
			}
			$st_createMinForDate = substr($st_createMinForDate, 0, 10); //Nur das Datum
			if (is_null(self::getWrAnz())) {
				self::setWrAnz($inverterData->getInverterCount());
			}
			$this->pMax = array();
			$this->eTag = array();
			while ($st_inverter_aktDate <= $st_inverter_endDate) {
				$gerDate = substr(self::makeGerDate($st_inverter_aktDate), 0, 8);
				$werte = $inverterData->getDailyData($st_inverter_aktDate);
				if ($werte !== false) { //problem, es gibt keinen 29.2.2012 in den Daten, auch könnte mal ein Tag fehlen
					foreach ($werte as $datum => $values) {
						$w = array();
						foreach ($values as $wr => $value) { //WR
							$w[$wr][] = $value['P_AC']; //PAc
							//Strings PDC auswerten
							foreach ($value as $string => $messwert) {
								if (is_numeric($string)) {
									$w[$wr][] = $messwert['P_DC']; //PDC
								}
							}
							$w[$wr][] = $value['E_DAY']; //E Tag
							//Strings UDC auswerten
							foreach ($value as $key => $messwert) {
								if (is_numeric($key)) {
									$w[$wr][] = $messwert['U_DC']; //UDC
								}
							}
							if (isset($value['T_WR'])) {
								$w[$wr][] = $value['T_WR']; //Wr-temperatur falls vorhanden
							}
						}
						if ($st_inverter_aktDate > $st_createMinForDate) {
							//min_day.js speichern als minyymmdd.js
							self::sort();
							//pmax und etag für den Tag auf ein array legen
							$gerDate = substr(self::makeGerDate($st_createMinForDate), 0, 8);
							$this->pMax[$gerDate] = self::searchPmax($gerDate);
							$this->eTag[$gerDate] = self::searchEday($gerDate);
							$min = new classMinYYMMDD($st_createMinForDate);
							$min->setData(self::getData());
							unset($min);
							self::setData(null);
							$st_createMinForDate = $st_inverter_aktDate;
						}

						self::addData(self::makeGerDate($datum), $w);
					}
				}
				$st_inverter_aktDate = date('Y-m-d', strtotime('+ 1 day', strtotime($st_inverter_aktDate)));
			}
			self::sort();
			$datum = substr(self::makeGerDate($datum), 0, 8);
			//pmax und etag für alle bearbeiteten Tage auf ein array legen
			$this->pMax[$datum] = self::searchPmax($datum);
			$this->eTag[$datum] = self::searchEday($datum);
			$w = array();
			if (!is_null($this->pMax)) {
				foreach ($this->pMax[$datum] as $wr => $value) {
					$w[$wr]['P_MAX'] = $value;
					$w[$wr]['E_DAY'] = $this->eTag[$datum][$wr];
				}
				//days.js erzeugen
				$days = new classDays($datum, $w);
				unset($days);
			}
			unset($w);
		} else {
			self::setData(null); //keine neuen Daten
		}
	}

	/**
	 * 	Gibt ein Array mit PMax des angegebenen Tages zurück
	 *
	 * @param string $date Datum der Form dd.mm.yy
	 * @return array
	 */
	private function searchPmax($date) {
		$d = self::getData();
		$pmax = array_fill(0, self::getWrAnz(), 0);
		foreach ($d as $datum => $value) {
			$dat1 = substr($datum, 0, 8);
			if ($dat1 < $date) {
				break;
			} elseif ($dat1 == $date) {
				for ($wr = 0; $wr < count($value); $wr++) {
					if (isset($value[$wr])) {//bei mehreren WR kommen die Daten für alle zeiten nicht immer bei allen WR
						if ($pmax[$wr] < $value[$wr][0]) {
							$pmax[$wr] = $value[$wr][0];
						}
					}
				}
			}
		}
		return $pmax;
	}

	/**
	 * 	gibt ein Array mit dem Tagesertrag des angegebenen Tages zurück
	 *
	 * @param string $date Datum der Form dd.mm.yy
	 * @return array
	 */
	private function searchEday($date) {
		$d = self::getData();
		$eday = array_fill(0, self::getWrAnz(), 0);
		foreach ($d as $datum => $value) {
			$dat1 = substr($datum, 0, 8);
			if ($dat1 < $date) {
				break;
			} elseif ($dat1 == $date) {
				for ($wr = 0; $wr < count($value); $wr++) {
					$eday[$wr] = $value[$wr][floor(count($value[$wr]) / 2)];
				}
				break;
			}
		}
		return $eday;
	}

	private function makeGerDate($date) {
		if (is_string($date)) {
			$date = strtotime($date);
		}
		return Date('d.m.y H:i:s', $date);
	}

	public function __destruct() {
		parent::__destruct();
		unset($this->pMax, $this->eTag);
	}

}

?>
