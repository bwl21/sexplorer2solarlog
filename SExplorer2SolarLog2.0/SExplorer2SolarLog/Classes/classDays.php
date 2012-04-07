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


include_once 'config.solarlog.php';


class classDays {

	const days = 'days.js'; //Dateiname der days.js
	const kennung = 'da[dx++]';

	/**
	 * schreibt die Datei days.js
	 * übergeben wird das Datum in der Form DD.MM.YY und ein Array mit einem Eintrag für jeden WR mit Etag und PACmax
	 *
	 * @param string $datum
	 * @param array $data
	 */
	public function __construct($datum, $data) {
		$filename = realpath(SLFILE_DATA_PATH) . '/' . self::days;
		if ($fp = @fopen($filename, 'wb')) {
			$line = self::kennung . '="' . $datum;
			foreach ($data as $value) {
				$line.='|' . $value['E_DAY'] . ';' . $value['P_MAX'];
			}
			$line.='"' . chr(13);
			if (!@fwrite($fp, $line)) {
				trigger_error('Fehler beim Schreiben in die Datei ' . $filename);
			}
			@fclose($fp);
		} else {
			trigger_error('Fehler beim Öffnen der Datei ' . $filename);
		}
	}

}

?>
