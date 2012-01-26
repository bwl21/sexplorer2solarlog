<?php

/*
 * © PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 */

include_once 'config.inc.php';

/**
 * Klasse zum Einlesen von csv-Dateien des Sunny-Explorers
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 */
class classSExplorerData {

// Format von $this->data bei einer Tagesdatei und 2 WR
// array
//  'TYPE' => string 'DAILY' (length=5)  //Typ der Daten (Tagesdatei|Monatsdatei)
//  '14.01.12 08:30:00' => //Datum und Zeit
//    array  0 =>	//WR-Nummer
//				  array	'ETag' => integer 3 //Tagesertrag wr1
//								'P' => integer 36 //Leistung wr1
//					1 =>	//WR-Nummer
//					array	'ETag' => integer 4 //Tagesertrag wr2
//								'P' => ineteger 25 //Leistung wr2
//
// Das Array ist sortiert, die jüngsten (neuesten) Daten stehen an erster Stelle
	private $data = array();
	private $DataType = null;
	private $SerNoWR = array(); //Seriennummern der WR
	private $wrAnz = null; //Anzahl WR aus den Seriennummern der WR ermittelt
	private $isOnline = false; //Online-Status des WR
	private $pmax = array();

	const daily = 'DAILY';
	const monthly = 'MONTHLY';
	const p = 'P';
	const etag = 'ETag';
	const eges = 'EGes';

	function __construct($SExplorerFile) {
		$this->pmax = array_fill(0, CSV_ANZWR, 0);
		//Dateinamen vom Pfad abtrennen
		if (!preg_match('/\w+-\d{6,8}\.csv/', $SExplorerFile, $matches)) {
			trigger_error(date('Y-m-d H:i:s') . ' - unbekanntes Format des Dateinamens ' . $SExplorerFile);
			die(3);
		}
		//Datum und Dateinamen trennen
		$Name_Date = explode('-', $matches[0]);
		preg_match('/\d{6,8}/', $Name_Date[1], $matches);
		$Name_Date[1] = $matches[0];
		unset($matches);
		//Dateityp bestimmen (Tages- oder Monatsdatei)
		$this->DataType = strlen($Name_Date[1]) == 8 ? self::daily : self::monthly;
		unset($Name_Date);
		//Datei einlesen
		ini_set('auto_detect_line_endings', true);
		if (file_exists($SExplorerFile)) {
			if ($inhalt = @file($SExplorerFile, FILE_SKIP_EMPTY_LINES)) {
				//Kopfzeile in den Daten suchen, nach der die Werte beginnen
				$min = array(); //Anfangswert des Tages/Monats
				$lines = 0;
				if ($this->DataType == self::daily) {
					$spalte1 = explode(',', CSV_DAILY_YIELDSUM_COLUMN);
					$spalte2 = explode(',', CSV_DAILY_POWER_COLUMN);
				} else {
					$spalte1 = explode(',', CSV_MONTHLY_MONTHSUM_COLUMN);
					$spalte2 = explode(',', CSV_MONTHLY_DAYSUM_COLUMN);
				}
				$pmax = array();
				foreach ($inhalt as $zeile) {
					$zeile = str_replace(CSV_DECIMALPOINT, '.', trim($zeile)); //Dezimalpunkt in numerischen Werten setzen
					$data = explode(CSV_DELIMITER, $zeile);
					//Seriennummer der WR ermitteln Format für 2WR:    ;SN: 2100071167;SN: 2100071167;SN: 2130002605;SN: 2130002605
					if (count($this->SerNoWR) == 0) {
						$snFound = true;
						if (count($data) > 2) {
							for ($i = 1; $i < count($data); $i++) {
								$snFound = $snFound && preg_match('/^SN:\s+\d+$/', $data[$i]);
								if (!$snFound)
									break;
							}
							if ($snFound) {
								$this->wrAnz = (count($data) - 1) / 2;
								for ($i = 0; $i < $this->wrAnz; $i++) {
									preg_match('/\d+$/', $data[$i + $i + 1], $matches);
									$this->SerNoWR[$i] = $matches[0];
								}
							}
						}
					} elseif (($datum = self::getDateTime($data[0])) !== false) {
						//Datum(+Zeit?) steht am Anfang der Zeile -> Werte einlesen
						if (count($data) > 2) {//mindestens Daten für einen WR müssen enthalten sein
							//Werte für alle WR auslesen und zwischenspeichern - gleich in Wh umrechnen
							$d2sum = 0;
							$d1 = array();
							$d2 = array();
							for ($wr = 0; $wr < $this->wrAnz; $wr++) {
								if (!isset($min[$wr])) {
									$min[$wr] = @$data[$spalte1[$wr] - 1];
								}
								$d1[$wr] = @$data[$spalte1[$wr] - 1];
								$d2[$wr] = @$data[$spalte2[$wr] - 1] * 1000;
								$d2sum+=$d2[$wr]; //WR-Leistung
							}
							//Onlinestatus WR setzen -> Online wenn Leistung >0
							$this->isOnline = $d2sum > 0;
							//Werte in $this->data eintragen
							if ($this->DataType == self::daily) {
								if ($d2sum > 0) {
									for ($wr = 0; $wr < $this->wrAnz; $wr++) {
										$this->data[$datum][$wr] = array(self::etag => (int) round(($d1[$wr] - $min[$wr]) * 1000), self::p => (int) $d2[$wr]);
										if ($this->pmax[$wr] < $d2[$wr]) {
											$this->pmax[$wr] = $d2[$wr];
										}
									}
								}
							} else {
								for ($wr = 0; $wr < $this->wrAnz; $wr++) {
									$this->data[$datum][$wr] = array(self::eges => (int) round($d1[$wr] * 1000), self::etag => (int) round($d2[$wr]));
								}
							}
							$lines++;
						}
					}
				}
				if (count($this->data) > 0) {
					//Fehlerprüfung Anzahl WR und Seriennummern
					if (is_null($this->wrAnz) || ($this->wrAnz != CSV_ANZWR)) { //ermittelte WR-Anz unterscheidet sich von der in config.inc.php
						trigger_error('Die Anzahl Wechselrichter=' . $this->wrAnz . ' in der csv Datei ' . $SExplorerFile . ' unterscheidet sich von der Anzahl Wechselrichter=' . CSV_ANZWR . ' in config.inc.php');
					} elseif (count($this->SerNoWR) != $this->wrAnz) { //Seriennummern konnten nicht ermittelt werden
						trigger_error('Die Anzahl angeschlossener Wechselrichter konnte aus der csv-Datei nicht ermittelt werden');
					}
				}
			}
		}
	}

	/**
	 * Gibt den Online-Status der WR zurück
	 * Ein Funktionsaufruf macht nur sinn, wenn Tagesdaten gespeichert sind.
	 * Deshalb wird ein Fehlereintrag generiert falls die Funktion bei anderen Daten aufgerufen wird
	 *
	 * @return boolean||null
	 */
	public function isOnline() {
		if (($this->DataType == self::daily) || (count($this->data) == 0)) {
			return $this->isOnline;
		} else {
			trigger_error('Die Funktion ' . __FUNCTION__ . ' wurde für nicht-Tagesdaten aufgerufen');
			return null;
		}
	}

	/**
	 * gibt Pmax des Tages zurück wenn es sich um Tagesdaten handelt
	 * @return array
	 */
	public function getPmax() {
		return $this->pmax;
	}

	/**
	 * funktion gibt ein Array mit den Seriennummern der angeschlossenen WR zurück
	 *
	 * @return array
	 */
	public function getSerNoWR() {
		return $this->SerNoWR;
	}

	/**
	 * Die Funktion gibt die Anzahl WR zurück, die aus der csv-Datei ermittelt wurde
	 *
	 * @return integer
	 */
	public function getWrAnz() {
		return $this->wrAnz;
	}

	/**
	 * gibt den Typ der Daten zurück '	'DAILY' oder 'MONTHLY';
	 *
	 * @return string
	 */
	public function getDataType() {
		return $this->DataType;
	}

	/**
	 * gibt das größte (jüngste) Datum der Daten in der Form DD.MM.YY oder DD.MM.YY HH:NN:SS zurück
	 * wenn noch keine Daten vorhanden sind, wird false zurückgegeben
	 *
	 * @return string|false
	 */
	public function getNewestDate() {
		if (count($this->data) > 1) { //Daten sind vorhanden
			self::sort();
			reset($this->data);
			return key($this->data);
		}
		return false;
	}

	/**
	 * funktion gibt den nächsten Wert ausgehend vom gesetzten Arrayzeiger (durch setPointerToDatum() )zurück
	 * bei sortiertem Array ist das der Eintrag mit dem nächstkleineren (älteren) Datum
	 * Ist kein Wert mehr vorhanden wird false zurückgegeben
	 *
	 * @return array
	 */
	function getNextValues() {
		$w = next($this->data);
		if ($w !== false) {
			return array(key($this->data) => $w);
		}
		return false;
	}

	/**
	 * funktion gibt den vorhergehenden Wert ausgehend vom gesetzten Arrayzeiger (durch setPointerToDatum() )zurück
	 * bei sortiertem Array ist das der Eintrag mit dem nächstgrößeren (jüngeren) Datum
	 * Ist kein Wert mehr vorhanden wird false zurückgegeben
	 *
	 * @return array
	 */
	function getPrevValues() {
		$w = prev($this->data);
		if ($w !== false) {
			return array(key($this->data) => $w);
		}
		return false;
	}

	/**
	 * funktion gibt den Wert zurück, auf dem der Arrayzeiger (durch setPointerToDatum() gesetzt )zurück
	 * Ist kein Wert vorhanden wird false zurückgegeben
	 *
	 * @return array
	 */
	function getCurrentValues() {
		$w = current($this->data);
		if ($w !== false) {
			return array(key($this->data) => $w);
		}
		return false;
	}

	/**
	 * Setzt den Arrayzeiger auf das Element, dessen key übergebene Datum ist
	 * Die Funktion wird zur Vorbereitung von getNextValues() und getPrvValues() bebnötigt
	 *
	 * @param string $datum
	 * @return boolean
	 */
	function setPointerToDate($datum) {
		reset($this->data);
		$c = 0;
		$l = count($this->data);
		while (key($this->data) != $datum) { // jeden Key überprüfen
			if (++$c >= $l) {
				return false; // Array-Ende erreicht
			}
			next($this->data); // Pointer um 1 verschieben
		}
		return true; // Key gefunden
	}

	/**
	 * gibt das kleinste (älteste) Datum der Daten in der Form DD.MM.YY oder DD.MM.YY HH:NN:SS zurück
	 * wenn noch keine Daten vorhanden sind, wird false zurückgegeben
	 *
	 * @return string|false
	 */
	public function getOldestDate() {
		if (count($this->data) > 0) {
			self::sort();
			end($this->data);
			return key($this->data);
		}
		return false;
	}

	/**
	 * Hilfsfunktion zum Prüfen, ob es sich um ein Datum handelt
	 * gibt false oder das Datum in der Form DD.MM.YY oder DD.MM.YY HH:NN:SS zurück
	 *
	 * @param string $data
	 * @return false|string
	 */
	private function getDateTime($DateTime) {
		$format = 'd.m.y';
		//kürzest mögliches Datum angenommen 2.1.12 1:25
		//längstes Datum z.B. 2012-22-01
		if (strlen($DateTime) > 10) {
			$format.=' H:i:s';
		}
		$datum = strtotime($DateTime);
		return $datum === false ? false : date($format, $datum);
	}

	/**
	 * sortiert self::$data absteigend - neuestes Datum zuerst
	 */
	private function sort() {
		if (count($this->data) > 1) {
			uksort($this->data, array($this, "cmp"));
		}
	}

	/**
	 * Hilfsfunktion zum Sortieren des Arrays
	 * @param type $a
	 * @param type $b
	 */
	private function cmp($a, $b) {
		$a = explode('.', $a);
		$a1 = substr($a[2], 3);
		$a[2] = '20' . substr($a[2], 0, 2);
		$b = explode('.', $b);
		$b1 = substr($b[2], 3);
		$b[2] = '20' . substr($b[2], 0, 2);
		return strtotime($b[2] . '-' . $b[1] . '-' . $b[0] . ' ' . $b1) -
						strtotime($a[2] . '-' . $a[1] . '-' . $a[0] . ' ' . $a1);
	}

	/**
	 *
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	function __destruct() {
		unset($this->data);
	}

}

?>
