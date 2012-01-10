<?php

/*
 * © PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 */

include_once 'config.inc.php';
include_once 'classErrorLog.php';

/**
 * @version 0.3
 * Beschreibung von classSExplorerData
 * Klasse zum Einlesen von csv-Dateien des Sunny-Explorers
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 */
class classSExplorerData {

	private $data = null;

	const type='TYPE';
	const daily='DAILY';
	const monthly='MONTHLY';
	const p='P';
	const etag='ETag';
	const eges='EGes';
	const min_day='min_day.js';
	const kennung='m[mi++]=';

	function __construct($SExplorerFile) {
		ini_set('auto_detect_line_endings', true);
		if (!($inhalt = @file($SExplorerFile, FILE_SKIP_EMPTY_LINES))) {
			classErrorLog::LogError(date('Y-m-d H:i:s') . ' - Fehler beim Öffnen von ' . $SExplorerFile . ' in ' . __METHOD__);
			return null;
		}
		$min = null;
		//Kopfzeile suchen
		$this->data = array();
		$lines = 0;
		foreach ($inhalt as $zeile) {
			$zeile = trim($zeile);
			$data = explode(CSV_DELIMITER, $zeile);
			if (!isset($this->data[self::type])) {
				if ($zeile == CSV_HEAD_LINE_DAILY) {
					$this->data[self::type] = self::daily;
				} elseif ($zeile == CSV_HEAD_LINE_MONTHLY) {
					$this->data[self::type] = self::monthly;
				}
				if (isset($this->data[self::type])) {
					//Positionen von Tag,Monat,Jahr,Stunde,Minute bestimmen
					$pos['day'] = strpos($zeile, CSV_HEAD_DAY);
					$pos['month'] = strpos($zeile, CSV_HEAD_MONTH);
					$pos['year'] = strpos($zeile, CSV_HEAD_YEAR);
					$pos['hour'] = strpos($zeile, CSV_HEAD_HOUR);
					$pos['minute'] = strpos($zeile, CSV_HEAD_MINUTE);
				}
			} else {
				if (count($data) == 3) {
					$d2 = str_replace(',', '.', trim($data[2]));
					$datum = substr($data[0], $pos['day'], strlen(CSV_HEAD_DAY)) . '.' .
									substr($data[0], $pos['month'], strlen(CSV_HEAD_MONTH)) . '.';
					$yearOffset = strlen(CSV_HEAD_YEAR) - 2;
					$datum.=substr($data[0], $pos['year'] + $yearOffset, 2);
					if ($pos['hour'] !== false) {
						$datum.=' ' . substr($data[0], $pos['hour'], strlen(CSV_HEAD_HOUR));
						if ($pos['minute'] !== false) {
							$datum.=':' . substr($data[0], $pos['minute'], strlen(CSV_HEAD_MINUTE)) . ':00';
						}
					}
					if ($this->data[self::type] == self::daily) {
						if ($d2 > 0) {
							$d1 = str_replace(',', '.', trim($data[1]));
							if (is_null($min)) {
								$min = $d1;
							}
							$this->data[$datum] = array(self::etag => round(($d1 - $min) * 1000), self::p => $d2 * 1000);
						}
						$lines++;
					} else {
						$d1 = str_replace(',', '.', trim($data[1]));
						$this->data[$datum] = array(self::eges => round(($d1 - $min) * 1000), self::etag => $d2 * 1000);
					}
				}
			}
		}
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
	 * erzeugt eine Datei min_day.js
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
							$minYYMMDDFilename = SLFILE_DATA_PATH . '/min' . substr($datum, 6, 2) . substr($datum, .3, 2) . substr($datum, 0, 2) . '.js';
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
