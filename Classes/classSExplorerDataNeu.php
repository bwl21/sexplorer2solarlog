<?php

/*
 * © PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 */

include_once 'config.inc.php';
include_once 'Classes/classErrorLog.php';

/**
 * @version 0.4
 * Beschreibung von classSExplorerData
 * Klasse zum Einlesen von csv-Dateien des Sunny-Explorers
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 */
class classSExplorerData {

/* Format von $this->data bei einer Tagesdatei und 2 WR
array
  'TYPE' => string 'DAILY' (length=5)
  '14.01.12 08:30:00' => //Datum und Zeit
    array  0 =>	//WR-Nummer
				  array	'ETag' => integer 3 //Tagesertrag wr1
								'P' => integer 36 //Leistung wr1
					1 =>	//WR-Nummer
					array	'ETag' => integer 4 //Tagesertrag wr2
								'P' => ineteger 25 //Leistung wr2
*/
	private $data = array();

	const type='TYPE';
	const daily='DAILY';
	const monthly='MONTHLY';
	const p='P';
	const etag='ETag';
	const eges='EGes';
	const min_day='min_day.js';
	const kennung='m[mi++]=';

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
		$min = array();//Anfangswert des Tages/Monats
		$lines = 0;
		$pos = null;
		if($this->data[self::type]==self::daily){
			$spalte1=explode(',', CSV_DAILY_YIELDSUM_COLUMN);
			$spalte2=explode(',',CSV_DAILY_POWER_COLUMN);
			$suchZeile=trim(CSV_HEAD_LINE_DAILY);
		}else{
			$spalte1=explode(',',CSV_MONTHLY_MONTHSUM_COLUMN);
			$spalte2=explode(',',CSV_MONTHLY_DAYSUM_COLUMN);
			$suchZeile=trim(CSV_HEAD_LINE_MONTHLY);
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
			} elseif (!is_null($pos) && $timestamp = strtotime(substr($data[0], $pos['year'], strlen(CSV_HEAD_YEAR)) . '-' .
							substr($data[0], $pos['month'], strlen(CSV_HEAD_MONTH)) . '-' .
							substr($data[0], $pos['day'], strlen(CSV_HEAD_DAY)) . ' ' .
							substr($data[0], $pos['hour'], strlen(CSV_HEAD_HOUR)) . ':' .
							substr($data[0], $pos['minute'], strlen(CSV_HEAD_MINUTE)) . ':00')) {
				//Datum(+Zeit?) steht am Anfang der Zeile -> Werte einlesen
				if (count($data) >= 2 * CSV_ANZWR + 1) {//Anzahl folgender Daten stimmt auch mit Anzahl WR überein
					$datum = substr($data[0], $pos['day'], strlen(CSV_HEAD_DAY)) . '.' .
									substr($data[0], $pos['month'], strlen(CSV_HEAD_MONTH)) . '.'.
									substr($data[0], $pos['year'] + strlen(CSV_HEAD_YEAR) - 2, 2);
					if ($pos['hour'] !== false) {
						$datum.=' ' . substr($data[0], $pos['hour'], strlen(CSV_HEAD_HOUR));
						if ($pos['minute'] !== false) {
							$datum.=':' . substr($data[0], $pos['minute'], strlen(CSV_HEAD_MINUTE)) . ':00';
						}
					}
					//Werte für alle WR auslesen und zwischenspeichern - gleich in Wh umrechnen
					$d2sum=0;
					$d1=array();
					$d2=array();
					for ($wr = 0; $wr < CSV_ANZWR; $wr++) {
						if(!isset ($min[$wr])){
							$min[$wr]=$data[$spalte1[$wr]-1];
						}
						$d1[$wr]=$data[$spalte1[$wr]-1];
						$d2[$wr]=$data[$spalte2[$wr]-1]*1000;
						$d2sum+=$d2[$wr];
					}
					//Werte in $this->data eintragen
					for ($wr = 0; $wr < CSV_ANZWR; $wr++) {
						if($this->data[self::type]==self::daily){
							if($d2sum>0){
								$this->data[$datum][$wr]=array(self::etag => (int)round(($d1[$wr]-$min[$wr])*1000),self::p => (int)$d2[$wr]);
							}
						}else{
							$this->data[$datum][$wr] = array(self::eges => (int)round($d1[$wr]*1000), self::etag => (int)round($d2[$wr]));
						}
					}
					$lines++;
				}
			}
		}
//		var_dump($this->data);
		if (count($this->data) == 0) {
			classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Die Datei ' . $SExplorerFile . ' enthält keine gültigen Daten in ' . __METHOD__);
			$this->data = null;
		} elseif (count($this->data) < 2) {
			if ($lines == 0) {
				classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Die Datei ' . $SExplorerFile . ' enthält keine Erträge in ' . __METHOD__);
			}
			$this->data = null;
		} else {
			$this->sort();
			if ($this->data[self::type] == self::daily) {//min_day.js erzeugen
				$this->createMin_day();
			} else {
				//days_hist und months.js müssen extern erzeugt werden
				//monthly
			}
		}
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
			return 1;
		} elseif ($b == self::type) {
			return -1;
		}
		$a = explode('.', $a);
		$a1 = substr($a[2], 3);
		$a[2] = substr($a[2], 0, 2);
		$b = explode('.', $b);
		$b1 = substr($b[2], 3);
		$b[2] = substr($b[2], 0, 2);
		return strtotime('20' . $b[2] . '-' . $b[1] . '-' . $b[0] . ' ' . $b1) - strtotime('20' . $a[2] . '-' . $a[1] . '-' . $a[0] . ' ' . $a1);
	}

	public function getData() {
		return $this->data;
	}

	/**
	 * @version 0.3
	 * erzeugt eine Datei min_day.js und die zugehörige Tagesdatei
	 */
	private function createMin_day() {
		$filename = SLFILE_DATA_PATH . '/' . self::min_day;
		if (file_exists($filename)) {
			@unlink($filename);
		}
		$minYYMMDDFilename = null;
		if (!is_null($this->data)) {
			$fp = @fopen($filename, 'wb');
			if ($fp === false) {
				classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Fehler beim Erzeugen der Datei ' . $filename . ' in ' . __METHOD__);
			} else {
				foreach ($this->data as $datum => $value) {
					if ($datum !== self::type) {
						if (is_null($minYYMMDDFilename)) {
							$minYYMMDDFilename = SLFILE_DATA_PATH . '/min' . substr($datum, 6, 2) . substr($datum, 3, 2) . substr($datum, 0, 2) . '.js';
						}
						if (!fwrite($fp, self::kennung . '"' . $datum . '|' . $value[self::p] . ';' . $value[self::p] . ';' . $value[self::etag] . ';0"' . chr(13))) {
							classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Fehler beim Schreiben in die Datei ' . $filename . ' in ' . __METHOD__);
						}
					}
				}
				@fclose($fp);
				if (!@copy($filename, $minYYMMDDFilename)) {
					classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Fehler beim Erzeugen der Datei ' . $minYYMMDDFilename . ' in ' . __METHOD__);
				}
			}
		}
	}

}

?>
