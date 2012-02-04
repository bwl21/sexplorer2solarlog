<?php

/**
 * Beschreibung von $RCSfile: classYears.php $
 *
 * erzeugt/ergaenzt die Datei years.js
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 *
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * 
 *
 * $Date: 2012/02/04 20:13:07 $
 * $Id: classYears.php 296a55f69e1a 2012/02/04 20:13:07 WebAdmin $
 * $LocalRevision: 99 $
 * $Revision: 296a55f69e1a $
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


class classYears extends classSLDataFile {

	const years = 'years.js'; //Dateiname der years.js
	const kennung = 'ye[yx++]';

	function __construct() {
		ini_set('date.timezone', TIMEZONE);
		parent::__construct(realpath(SLFILE_DATA_PATH) . '/' . self::years, self::kennung);
	}

	/**
	 * erzeugt/ergänzt die Datei years.js
	 */
	public function check() {
		$SLNewestDate = self::getNewestDatum();
		$endDate = time() - 86400;
		if ($SLNewestDate === false) { //Datei existiert nicht, erzeugen
			self::setWrAnz(CSV_ANZWR); //Anzahl WR setzen
			$startDate = (substr(START_DATUM, 0, 4) < date('Y', $endDate)) ? strtotime(substr(START_DATUM, 0, 4) . '-12-31') : strtotime(START_DATUM);
		} else {
			$startDate = $endDate;
		}
		while ($startDate <= $endDate) {
			$year = date('Y', $startDate);
			//Alle Montsdateien einlesen
			$SexplorerData = array();
			$aktMonth = date('n', $startDate);
			for ($i = 1; $i <= $aktMonth; $i++) {
				$filename = realpath(SEXPLORER_DATA_PATH) . '/' . CSV_ANLAGEN_NAME . '-' . $year . str_pad($i, 2, '0', STR_PAD_LEFT) . '.csv';
				$SexplorerData[$i] = new classSExplorerData($filename);
				if ($SexplorerData[$i]->getNewestDate() === false) {
					unset($SexplorerData[$i]);
				} else {
					$lastIndex = $i;
				}
			}
			if (count($SexplorerData) > 0) {
				//Jahressumme bilden
				$w = array_fill(0, self::getWrAnz(), 0);
				$SExplNewestDate = $SexplorerData[$lastIndex]->getNewestDate();
				//Eintrag für Datum eventuell löschen
				if ($SLNewestDate !== FALSE) {//es sind schon Daten vorhanden
					if (($SExplNewestDate !== $SLNewestDate) && (substr($SExplNewestDate, 6) == substr($SLNewestDate, 6))) {
						self::DeleteValue($SLNewestDate); //Eintrag für das Jahr löschen
					}
				}
				foreach ($SexplorerData as $SExplData) {
					$SExplData->setPointerToDate($SExplData->getOldestDate());
					$werte = $SExplData->getCurrentValues();
					while ($werte !== false) {
						$key = key($werte);
						for ($i = 0; $i < self::getWrAnz(); $i++) {
							$w[$i]+=$werte[$key][$i][classSExplorerData::etag];
						}
						$werte = $SExplData->getPrevValues();
					}
					self::addData($SExplNewestDate, $w);
				}
				unset($w);
			}
			unset($SexplorerData);
			if ((date('Y', $startDate) + 1) == date('Y', $endDate)) {
				$startDate = $endDate;
			} else {
				$startDate = strtotime("+1 year", $startDate);
			}
		}
	}

}

?>
