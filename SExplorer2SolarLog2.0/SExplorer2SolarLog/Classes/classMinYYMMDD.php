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

class classMinYYMMDD extends classSLDataFile {

	const kennung = 'm[mi++]';

	/**
	 * Das Datum kann in der Form DD.MM.YY oder YYYY-MM-DD oder als Timestamp übergeben werden
	 *
	 * @param string|timestamp $datum
	 */
	function __construct($datum) {
		if (is_integer($datum)) { //Timestamp
			$filename = date('ymd', $datum);
		} elseif (preg_match('/^\d{2}\.\d{2}\.\d{2}/', $datum)) { //Form DD.MM.YY
			$fn = explode('.', $datum);
			$filename = $fn[2] . $fn[1] . $fn[0];
			unset($fn);
		} elseif (preg_match('/^\d{4}-\d{2}-\d{2}/', $datum)) { //Form YYYY-MM-DD
			$fn = explode('-', $datum);
			$filename = substr($fn[0], 2, 2) . $fn[1] . substr($fn[2],0,2);
			unset($fn);
		} else { //ungültiges Format
			trigger_error('Ungültiges Datumsformat ' . $datum . ' wurde übergeben');
		}
		parent::__construct(realpath(SLFILE_DATA_PATH) . '/min' . $filename . '.js', self::kennung);
	}

}

?>
