<?php

/**
 * Beschreibung von $RCSfile$
 *
 * Klasse zur Verwaltung/Erzeugung der months.js Datei des Solarlog
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 *
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * 
 *
 * $Date$
 * $Id$
 * $LocalRevision$
 * $Revision$
 */

/*

    Diese Datei ist Teil von SExplore2SlLog.

    SExplore2SlLog ist Freie Software: Sie können es unter den Bedingungen
    der GNU General Public License, wie von der Free Software Foundation,
    Version 3 der Lizenz oder jeder späteren veröffentlichten Version, 
    weiterverbreiten und/oder modifizieren.

    FuSExplore2SlLog wird in der Hoffnung, dass es nützlich sein wird, aber
    OHNE JEDE GEWÄHELEISTUNG, bereitgestellt; sogar ohne die implizite
    Gewährleistung der MARKTFÄHIGKEIT oder EIGNUNG FÜR EINEN BESTIMMTEN ZWECK.
    Siehe die GNU General Public License für weitere Details.
    
    <http://www.gnu.org/licenses/>

*/


include_once 'config.inc.php';

class classMonths extends classSLDataFile {

	const months = 'months.js'; //Dateiname der months.js
	const kennung = 'mo[mx++]';

	function __construct() {
		ini_set('date.timezone', TIMEZONE);
		parent::__construct(realpath(SLFILE_DATA_PATH) . '/' . self::months, self::kennung);
	}

	/**
	 * erzeugt/ergänzt die Datei months.js
	 */
	public function check() {
		$SLNewestDate = self::getNewestDatum();
		$endDate = time() - 86400;
		if ($SLNewestDate === false) { //Datei existiert nicht, erzeugen
			self::setWrAnz(CSV_ANZWR); //Anzahl WR setzen
			$startDate = strtotime(START_DATUM);
		} else {
			$startDate = $endDate;
		}
		while ($startDate <= $endDate) {
			//Dateinamen der csv-Datei für den Vortag ermitteln und Datei öffnen
			$SexplorerData = new classSExplorerData(realpath(SEXPLORER_DATA_PATH) . '/' . CSV_ANLAGEN_NAME . '-' . date('Ym', $startDate) . '.csv');
			$SExplNewestDate = $SexplorerData->getNewestDate();
			if ($SExplNewestDate !== false) { //Es sind Daten vorhanden
				$SExplOldestDate = $SexplorerData->getOldestDate();
				if ($SLNewestDate !== false) {
					if (($SExplNewestDate != $SLNewestDate) && (substr($SExplNewestDate, 2) == substr($SLNewestDate, 2))) {
						self::DeleteValue($SLNewestDate); //Eintrag für den Monat löschen wenn das Monatsende noch nicht erreicht ist
					}
				}
				$SexplorerData->setPointerToDate($SExplOldestDate);
				$w = array_fill(0, self::getWrAnz(), 0);
				$werte = $SexplorerData->getCurrentValues();
				//Summe über alle WR ETag des Monats bilden
				while ($werte !== false) {
					$key = key($werte);
					for ($i = 0; $i < self::getWrAnz(); $i++) {
						$w[$i]+=$werte[$key][$i][classSExplorerData::etag];
					}
					$werte = $SexplorerData->getPrevValues();
				}
				self::addData($SExplNewestDate, $w);
				unset($w);
			}
			unset($SexplorerData);
			$startDate = strtotime("+1 month", $startDate);
		}
	}

}

?>
