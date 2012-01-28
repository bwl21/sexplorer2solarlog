<?php

/**
 * Beschreibung von $RCSfile: classMin_day.php $
 *
 * Klasse zur Verwaltung/Erzeugung von min_day.js files des Solarlog
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 *
 * © PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * 
 *
 * $Date: 2012/01/28 18:08:16 $
 * $Id: classMin_day.php fa10176932de 2012/01/28 18:08:16 Bernhard $
 * $LocalRevision: 89 $
 * $Revision: fa10176932de $
 */


include_once 'config.inc.php';

class classMin_day extends classSLDataFile {

	private $isNewDay = false; //True wenn ein neuer Tag
	private $isOnline = false; //Online-Status des WR
	private $pMax = array(); //Array mit allen PMax der erzeugten Dateien
	private $p = array(); //Array mit den aktuellen Leistungen der Wr

	const min_day = 'min_day.js'; //Dateiname der min_day.js
	const kennung = 'm[mi++]';

	function __construct() {
		ini_set('date.timezone', TIMEZONE);
		parent::__construct(realpath(SLFILE_DATA_PATH) . '/' . self::min_day, self::kennung);
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
	 * Funktion gibt den aktuellen Onlinestatus der WR (ermittelt aus der csv-Datei) zurück
	 *
	 * @return boolean
	 */
	public function isOnline() {
		return $this->isOnline;
	}

	/**
	 * gibt PMax für alle erzeugten Dateien auf einem Array zurück
	 *
	 * @return array
	 */
	public function getPMax() {
		return $this->pMax;
	}

	/**
	 * gibt die aktuellen Momentanleistungen der WR auf einem Array zurück
	 *
	 * @return array
	 */
	public function getP() {
		return $this->p;
	}

	/**
	 * prüft die Aktualität der min_day.js und erzeugt bei Bedarf auch die minYYMMDD.js
	 */
	public function check() {
		$NewestDatum = self::getNewestDatum();
		$endDate = time();
		if ($NewestDatum === false) { //Datei existiert nicht, erzeugen
			self::setWrAnz(CSV_ANZWR); //Anzahl WR setzen
			$this->isNewDay = true;
			$startDate = strtotime(START_DATUM);
		} else {
			$startDate = $endDate;
		}
		$this->p = array_fill(0, self::getWrAnz(), 0);
		$this->p['datum_zeit'] = date('d.m.y H:i:00', time()); //zugehöriges Datum Zeit merken
		while ($startDate <= $endDate) {
			//Dateinamen der csv-Datei für aktuelles Datum ermitteln und Datei öffnen
			$SexplorerData = new classSExplorerData(realpath(SEXPLORER_DATA_PATH) . '/' . CSV_ANLAGEN_NAME . '-' . date('Ymd', $startDate) . '.csv');
			$this->isOnline = $SexplorerData->isOnline(); //aktuellen Onlinestatus des WR erfragen
			$SExplorerNewestDate = $SexplorerData->getNewestDate();
			if ($SExplorerNewestDate !== false) { //Es sind Daten vorhanden
				if ($NewestDatum === false) {
					$NewestDatum = $SexplorerData->getOldestDate();
				} else {
					//Wenn das neueste Datum im min-File älter als das Datum der csv-Datei ist -> neuer Tag
					//die Datei minYYMMDD mit den Daten aus der alten min-Datei erzeugen
					//Dadurch wird gewährleistet, dass die Datei vom Vortag erst am nächsten Morgen bei
					//Inbetriebnahmeder WR erzeugt wird
					if ($this->isNewDay = (substr($NewestDatum, 0, 8) != substr($SExplorerNewestDate, 0, 8))) {//Datei Vortag erzeugen
						$min = new classMinYYMMDD(substr($NewestDatum, 0, 8));
						$min->setData(self::getData());
						unset($min);
						self::setData(null);
						$NewestDatum = $SexplorerData->getOldestDate();
					}
				}
				if ($NewestDatum != $SExplorerNewestDate) { //Neue Daten vorhanden
					$etag = array();
					$etag = array_fill(0, self::getWrAnz(), 0);
					$SexplorerData->setPointerToDate($NewestDatum);
					$wrAnz = self::getWrAnz();
					$werte = $SexplorerData->getCurrentValues();
					while ($werte !== false) {
						$datum = key($werte);
						$w = array();
						for ($i = 0; $i < $wrAnz; $i++) {
							$this->p[$i] = $werte[$datum][$i][classSExplorerData::p]; //Momentanleistungen speichern
							$this->p['datum_zeit'] = $datum; //zugehöriges Datum Zeit merken
							$w[$i][] = $werte[$datum][$i][classSExplorerData::p]; //PAC
							$w[$i][] = $werte[$datum][$i][classSExplorerData::p]; //PDC
							$w1 = $werte[$datum][$i][classSExplorerData::etag]; //ETag
							$w[$i][] = $w1;
							if ($etag[$i] < $w1) {
								$etag[$i] = $w1; //größten eTag pro WR für days.js speichern
								$dat = substr($datum, 0, 8);
							}
							$w[$i][] = 0; //UDC
						}
						self::addData($datum, $w);
						$werte = $SexplorerData->getPrevValues();
					}
					$werte = array();
					$pmax = $SexplorerData->getPmax();
					$this->pMax[substr($datum, 0, 8)] = $pmax;
					for ($i = 0; $i < $wrAnz; $i++) {
						$werte[$i] = array(classSExplorerData::etag => $etag[$i], classSExplorerData::p => $pmax[$i]);
					}
					//datei days.js erzeugen
					$days = new classDays($dat, $werte);
					unset($werte, $days, $etag);
				}
			}
			if (!self::isOnline()) {
				//wenn WR offline sind, als letzten (neuesten) Wert noch 0 für p eintragen
				$datum = self::getNewestDatum();
				$werte = self::getValue($datum);
				$w = 0;
				if (!is_null($werte)) {
					for ($i = 0; $i < self::getWrAnz(); $i++) {
						$w+=$werte[$i][0] + $werte[$i][1]; //Summe bilden zum Prüfen, ob schon 0 drinsteht
						$werte[$i][0] = 0; //PAC
						$werte[$i][1] = 0; //PDC
					}
					if ($w > 0) {
						//Zeit+5 Minuten auf 0 setzen
						self::addData(date('d.m.y H:i:s', strtotime('20' . substr($datum, 6, 2) . '-' . substr($datum, 3, 2) . '-' . substr($datum, 0, 2) . substr($datum, 8)) + 300), $werte);
					}
					unset($werte);
				}
				for ($i = 0; $i < self::getWrAnz(); $i++) {
					$this->p[$i] = 0; //Momentanleistungen 0 speichern
				}
			}
			unset($SexplorerData);
			$startDate+=86400;
		}
	}

	public function __destruct() {
		parent::__destruct();
		unset($this->pMax);
	}

}

?>
