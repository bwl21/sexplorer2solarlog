<?php

/*
 * © PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 */

include_once 'config.inc.php';
include_once 'Classes/classErrorLog.php';

/**
 * @version 0.4
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

	const type='TYPE';
	const daily='DAILY';
	const monthly='MONTHLY';
	const p='P';
	const etag='ETag';
	const eges='EGes';

	function __construct($SExplorerFile) {
		//Dateinamen vom Pfad abtrennen
		if (!preg_match('/\w+-\d{6,8}\.csv/', $SExplorerFile, $matches)) {
			classErrorLog::LogError(date('Y-m-d H:i:s') . ' - unbekanntes Format des Dateinamens ' . $SExplorerFile . ' in ' . __METHOD__);
			die(3);
		}
		//Datum und Dateinamen trennen
		$Name_Date = explode('-', $matches[0]);
		preg_match('/\d{6,8}/', $Name_Date[1], $matches);
		$Name_Date[1] = $matches[0];
		unset($matches);
		//Dateityp bestimmen (Tages- oder Monatsdatei)
		$this->data[self::type] = strlen($Name_Date[1]) == 8 ? self::daily : self::monthly;
		$date = substr($Name_Date[1], 0, 4) . '-' . substr($Name_Date[1], 4, 2) . '-' . substr($Name_Date[1], 6, 2);
		unset($Name_Date);
		//Datei einlesen
		ini_set('auto_detect_line_endings', true);
		if (!($inhalt = @file($SExplorerFile, FILE_SKIP_EMPTY_LINES))) {
			classErrorLog::LogError(date('Y-m-d H:i:s') . ' - Fehler beim Öffnen von ' . $SExplorerFile . ' in ' . __METHOD__);
			die(4);
		}
		//Kopfzeile in den Daten suchen, nach der die Werte beginnen
		$min = array(); //Anfangswert des Tages/Monats
		$lines = 0;
		$pos = null;
		if ($this->data[self::type] == self::daily) {
			$spalte1 = explode(',', CSV_DAILY_YIELDSUM_COLUMN);
			$spalte2 = explode(',', CSV_DAILY_POWER_COLUMN);
			$suchZeile = trim(CSV_HEAD_LINE_DAILY);
		} else {
			$spalte1 = explode(',', CSV_MONTHLY_MONTHSUM_COLUMN);
			$spalte2 = explode(',', CSV_MONTHLY_DAYSUM_COLUMN);
			$suchZeile = trim(CSV_HEAD_LINE_MONTHLY);
		}
		foreach ($inhalt as $zeile) {
			$zeile = str_replace(CSV_DECIMALPOINT, '.', trim($zeile)); //Dezimalpunkt in numerischen Werten setzen
			$data = explode(CSV_DELIMITER, $zeile);
			if ($zeile == $suchZeile) {
				//Positionen von Tag,Monat,Jahr,Stunde,Minute bestimmen
				$pos = array();
				$pos['day'] = strpos($data[0], CSV_HEAD_DAY);
				$pos['month'] = strpos($data[0], CSV_HEAD_MONTH);
				$pos['year'] = strpos($data[0], CSV_HEAD_YEAR);
				$pos['hour'] = strpos($data[0], CSV_HEAD_HOUR);
				$pos['minute'] = strpos($data[0], CSV_HEAD_MINUTE);
			} elseif ($datum=self::is_time($pos, $data)) {
				//Datum(+Zeit?) steht am Anfang der Zeile -> Werte einlesen
				if (count($data) > 2) {//mindestens Daten für einen WR müssen enthalten sein
					//Werte für alle WR auslesen und zwischenspeichern - gleich in Wh umrechnen
					$d2sum = 0;
					$d1 = array();
					$d2 = array();
					for ($wr = 0; $wr < CSV_ANZWR; $wr++) {
						if (!isset($min[$wr])) {
							$min[$wr] = @$data[$spalte1[$wr] - 1];
						}
						$d1[$wr] = @$data[$spalte1[$wr] - 1];
						$d2[$wr] = @$data[$spalte2[$wr] - 1] * 1000;
						$d2sum+=$d2[$wr];
					}
					//Werte in $this->data eintragen
					if ($this->data[self::type] == self::daily) {
						if($d2sum > 0){
							for ($wr = 0; $wr < CSV_ANZWR; $wr++) {
								$this->data[$datum][$wr] = array(self::etag => (int) round(($d1[$wr] - $min[$wr]) * 1000), self::p => (int) $d2[$wr]);
							}
						}
					}else{
						for ($wr = 0; $wr < CSV_ANZWR; $wr++) {
							$this->data[$datum][$wr] = array(self::eges => (int) round($d1[$wr] * 1000), self::etag => (int) round($d2[$wr]));
						}
					}
					$lines++;
				}
			}
		}
//		var_dump($this->data);
		if (count($this->data) == 0) {
			classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Die Datei ' . $SExplorerFile . ' enthält keine gültigen Daten in ' . __METHOD__);
			$this->data = array();
		}
	}

	/**
	 * Hilfsfunktion zum Prüfen, ob es sich um ein Datum handelt
	 * gibt false oder das Datum in der Form DD.MM.YY oder DD.MM.YY HH:NN:SS zurück
	 *
	 * @param array $pos
	 * @param array $data
	 * @return false|string
	 */
	private function is_time($pos,$data){
		if(is_null($pos)){
			return false;
		}
		$datum=substr($data[0], $pos['year'], strlen(CSV_HEAD_YEAR)) . '-' .
							substr($data[0], $pos['month'], strlen(CSV_HEAD_MONTH)) . '-' .
							substr($data[0], $pos['day'], strlen(CSV_HEAD_DAY));
		if($pos['hour']){
			$datum.=' '.substr($data[0], $pos['hour'], strlen(CSV_HEAD_HOUR));
			if($pos['minute']){
				$datum.=':'.substr($data[0], $pos['minute'], strlen(CSV_HEAD_MINUTE)).':00';
			}else{
				$datum.=':00:00';
			}
		}
		return strtotime($datum)==false?false:substr($datum,8,2).'.'.substr($datum,5,2).'.'.substr($datum,2,2). substr($datum,10);
	}

	/**
	 * sortiert self::$data absteigend - neuestes Datum zuerst
	 */
	private function sort() {
		if (!is_null($this->data)) {
			uksort($this->data, array($this, "cmp"));
		}
	}

	/**
	 * @version 0.3
	 * Hilfsfunktion zum Sortieren des Arrays
	 * @param type $a
	 * @param type $b
	 */
	private function cmp($a, $b) {
		if ($a == self::type) {
			return -1;
		} elseif ($b == self::type) {
			return 1;
		}
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
