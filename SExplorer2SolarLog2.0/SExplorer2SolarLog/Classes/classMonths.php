<?php

/**
 * Interface class for convert datafiles from solar-inverters into the SolarLog dataformat
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
//include_once 'classSLDataFile.php';

class classMonths extends classSLDataFile {

	const months = 'months.js'; //Dateiname der months.js
	const kennung = 'mo[mx++]';

	private $days_hist = null;

	/* Format $this->eYear array
	 * 		array (size=2)
	 * 			'20.03.12' =>   //Datum letzter bearbeiteter Tag (ohne aktuellen Tag!) in 2012
	 * 				array (size=2)
	 * 					0 => int 749379 //eYear in Wh WR 0
	 * 					1 => int 665221 //eYear in Wh WR 1
	 * 			'31.12.11' =>		//Datum letzter bearbeiteter Tag in 2011
	 * 				array (size=2)
	 * 					0 => int 12749379 //eYear in Wh WR 0
	 * 					1 => int 21665221 //eYear in Wh WR 1 */
	private $eYear = null;

	/**
	 *
	 * @param classDaysHist $daysHist
	 */
	function __construct($daysHist = null) {
		$this->days_hist = $daysHist;
		if (!is_null($this->days_hist)) {
			self::setWrAnz($this->days_hist->getWrAnz());
			ini_set('date.timezone', TIMEZONE);
			parent::__construct(realpath(SLFILE_DATA_PATH) . '/' . self::months, self::kennung);
			self::update();
		}
	}

	/**
	 * erzeugt/ergänzt die Datei months.js
	 */
	public function update() {
		$eMonths = $this->days_hist->getEMonths();
		if (!is_null($eMonths)) {
			$years = array();
			$dates = is_null(self::getData()) ? array() : array_keys(self::getData());
			foreach ($eMonths as $date => $value) {
				if (!in_array(substr($date, 6), $years)) {
					$years[] = substr($date, 6);
				}
				foreach ($dates as $datum) {
					if (substr($date, 3) == substr($datum, 3)) {
						self::DeleteValue($datum);
						break;
					}
				}
				$w = array();
				foreach ($value as $wr => $wert) {
					$w[$wr][0] = $wert;
				}
				self::addData($date, $w);
				unset($w);
			}
			unset($dates, $value, $eMonths);
			self::sort();
			//Jahreserträge auf Array legen
			$dates = is_null(self::getData()) ? array() : array_keys(self::getData());
			$wr = self::getWrAnz();
			foreach ($years as $year) {
				$summe = array_fill(0, $wr, 0);
				foreach ($dates as $date) {
					$w = self::getValue($date);
					if (!is_null($w)) {
						if (substr($date, 6) == $year) {
							foreach ($w as $i => $wert) {
								$summe[$i] +=$wert[0];
							}
							if (!isset($summe['maxDate'])) {
								$summe['maxDate'] = $date;
							}
						} elseif (substr($date, 6) < $year) {
							break;
						}
					}
					unset($w, $wert);
				}
				for ($i = 0; $i < $wr; $i++) {
					$this->eYear[$summe['maxDate']][$i] = $summe[$i];
				}
				unset($summe);
			}
			unset($dates, $eMonths, $years);
		}
	}

	public function getEYear() {
		return $this->eYear;
	}

	public function __destruct() {
		parent::__destruct();
		unset($this->days_hist, $this->eYear);
	}

}

?>
