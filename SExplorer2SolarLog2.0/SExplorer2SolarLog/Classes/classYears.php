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
include_once 'classSLDataFile.php';

class classYears extends classSLDataFile {

	const years = 'years.js'; //Dateiname der years.js
	const kennung = 'ye[yx++]';

	private $months = null;

	/**
	 *
	 * @param classMonths $months
	 */
	function __construct($months = null) {
		$this->months = $months;
		if (!is_null($this->months)) {
			ini_set('date.timezone', TIMEZONE);
			parent::__construct(realpath(SLFILE_DATA_PATH) . '/' . self::years, self::kennung);
			self::update();
		}
	}

	/**
	 * erzeugt/ergänzt die Datei years.js
	 */
	public function update() {
//		array (size=2)
//			'20.03.12' =>   //Datum letzter bearbeiteter Tag (ohne aktuellen Tag!) in 2012
//				array (size=2)
//					0 => int 749379 //eYear in Wh WR 0
//					1 => int 665221 //eYear in Wh WR 1
//			'31.12.11' =>		//Datum letzter bearbeiteter Tag in 2011
//				array (size=2)
//					0 => int 12749379 //eYear in Wh WR 0
//					1 => int 21665221 //eYear in Wh WR 1
		$eYear = $this->months->getEYear();
		if (!is_null($eYear)) {
			$dates = is_null(self::getData()) ? array() : array_keys(self::getData());
			foreach ($eYear as $date => $value) {
				foreach ($dates as $datum) {
					if (substr($date, 6) == substr($datum, 6)) {
						self::DeleteValue($datum);
						break;
					}
				}
				$w=array();
				foreach ($value as $wr => $wert) {
					$w[$wr][0] = $wert;
				}
				self::addData($date, $w);
				unset($w);
			}
			unset($dates, $value, $eYear);
			self::sort();
		}
	}

}

?>
