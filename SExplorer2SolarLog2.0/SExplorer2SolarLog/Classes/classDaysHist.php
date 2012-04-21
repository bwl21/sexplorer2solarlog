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
//include_once 'classSLDataFile.php';

class classDaysHist extends classSLDataFile {

	private $min_day = null;

	/* Format $this->eMonth, array mit den Monatserträgen aller berabeiteten Monate (ohne den aktuellen Tag !!!)
	 *
	 * array (size=3)
	 *  '31.01.12' =>			//Januar 2012
	 *    array (size=2)
	 *      0 => int 23587		//Monatsertrag WR 1 in Wh
	 *      1 => int 19956		//Monatsertrag WR 2 in Wh
	 *  '28.02.12' =>
	 *    array (size=2)
	 *      0 => int 368771	//Monatsertrag WR 1 in Wh
	 *      1 => int 235875	//Monatsertrag WR 2 in Wh
	 *  '20.03.12' =>
	 *    array (size=2)
	 *      0 => int 357021	//Monatsertrag WR 1 in Wh
	 *      1 => int 299887	//Monatsertrag WR 2 in Wh */
	private $eMonth = null;

	const days_hist = 'days_hist.js'; //Dateiname der days_hist.js
	const kennung = 'da[dx++]';

	/**
	 *
	 * @param classMin_day $min_day
	 */
	function __construct($min_day = null) {
		$this->min_day = $min_day;
		if (!is_null($this->min_day)) {
			self::setWrAnz($this->min_day->getWrAnz());
			ini_set('date.timezone', TIMEZONE);
			parent::__construct(realpath(SLFILE_DATA_PATH) . '/' . self::days_hist, self::kennung);
			self::update();
		}
	}

	/**
	 * erzeugt/ergänzt die Datei days_hist.js
	 */
	private function update() {
		$etag = $this->min_day->getEDay();
		if (!is_null($etag)) {
			$count = count($etag);
			$j = 1;
			$pmax = $this->min_day->getPMax();
			$months = array();
			foreach ($etag as $date => $values) {
				if ($j < $count) {//bis auf den letzten Tag alle werte in days_hist.js speichern
					$w = self::getValue($date);
					$data = array();
					foreach ($values as $wr => $e_day) {
						$i = 0;
						$data[$wr][$i++] = $e_day; //etag
						if (isset($w[$date][$wr][$i])) {
							if ($w[$date][$wr][$i] < $pmax[$date][$wr]) {
								$data[$wr][$i++] = $pmax[$date][$wr];
							} else {
								$data[$wr][$i++] = $w[$date][$wr][$i];
							}
						} else {
							$data[$wr][$i++] = $pmax[$date][$wr];
						}
					}
					self::addData($date, $data);
					unset($data);
					//alle Monat.Jahr merken, die bearbeitet wurden
					$date = substr($date, 2);
					if (!in_array($date, $months)) {
						$months[] = $date;
					}
					unset($w);
				}
				$j++;
			}
			unset($pmax, $values, $etag);
			self::sort();
			//Monatserträge der bearbeiteten Monate auf Array legen
			foreach ($months as $datum) {
				$startDate = strtotime('20' . substr($datum, 4, 2) . '-' . substr($datum, 1, 2) . '-01');
				$endDate = strtotime('20' . substr($datum, 4, 2) . '-' . substr($datum, 1, 2) . '-' . date('t', $startDate));
				$wr = self::getWrAnz();
				$summe[$datum] = array_fill(0, $wr, 0);
				while ($startDate <= $endDate) {
					$w = self::getValue(date('d.m.y', $startDate));
					if (!is_null($w)) {
						$summe['maxDay'] = date('d.m.y', $startDate);
						for ($i = 0; $i < $wr; $i++) {
							if(isset($w[$i])){
								$summe[$datum][$i] +=$w[$i][0]; //Etag
							}
						}
					}
					$startDate +=86400;
				}
				for ($i = 0; $i < $wr; $i++) {
					$this->eMonth[$summe['maxDay']][$i] = $summe[$datum][$i];
				}
				unset($summe);
			}
			unset($months);
		}
	}

	public function getEMonths() {
		return $this->eMonth;
	}

	public function __destruct() {
		parent::__destruct();
		unset($this->eMonth, $this->min_day);
	}

}

?>
