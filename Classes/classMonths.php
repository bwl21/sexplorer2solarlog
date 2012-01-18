<?php

/**
 * Klasse zur Verwaltung/Erzeugung der months.js Datei des Solarlog
 *
 * @version 0.1
 * @author PhotonenSammler <photonensammler@freenet.de>
 */
include_once 'config.inc.php';

class classMonths extends classSLDataFile {
	const months='months.js'; //Dateiname der months.js
	const kennung='mo[mx++]';

	function __construct() {
		parent::__construct(realpath(SLFILE_DATA_PATH) . '/' . self::months , self::kennung);
	}

	/**
	 * erzeugt/ergänzt die Datei months.js
	 */
	public function check() {
		$SLNewestDate = self::getNewestDatum();
		if ($SLNewestDate === false) { //Datei existiert nicht, erzeugen
			self::setWrAnz(CSV_ANZWR); //Anzahl WR setzen
		}
		//Dateinamen der csv-Datei für den Vortag ermitteln und Datei öffnen
		$SexplorerData = new classSExplorerData(SEXPLORER_DATA_PATH . '/' . CSV_ANLAGEN_NAME . '-' . date('Ym', time() - 86400) . '.csv');
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
				$key=key($werte);
				for ($i = 0; $i < self::getWrAnz(); $i++) {
					$w[$i]+=$werte[$key][$i][classSExplorerData::etag];
				}
				$werte = $SexplorerData->getPrevValues();
			}
			$this->addData($SExplNewestDate, $w);
			unset($w);
		}
		unset($SexplorerData);
	}

}

?>
