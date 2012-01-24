<?php

/**
 * allgemeine Klasse für Solarlog-Datendateien
 * min_day.js, minYYMMDD.js, days_hist.js, months.js, years.js
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 */
class classSLDataFile {

	private $filename = null; //Dateiname der Solarlog-Daten-Datei
	private $kennung = null; //Kennung der Daten 'm[mi++]','da[dx++]','mo[mx++]' oder 'ye[yx++]'
	private $WrAnz = null; //Anzahl erkannter WR in der Datei
	private $data = array(); //Die Daten aus der Datei
	private $Hash = null; //Checksumme des Daten_Arrays nach dem constructor

	/**
	 * der constructor liest die Datei ein wenn sie existiert und legt die Daten auf $this->data ab
	 * wenn die Datei nicht existiert, wird die übergebene Kennung zum Erzeugen der Datei verwendet
	 * falls die Daten geändert wurden
	 *
	 * @param string $filename
	 * @param string $kennung
	 */

	public function __construct($filename, $kennung = null) {
		$this->filename = $filename;
		if (@file_exists($this->filename)) {//Wenn Datei da ist, öffnen und einlesen
			ini_set('auto_detect_line_endings', true);
			$arr = @file($this->filename, FILE_SKIP_EMPTY_LINES && FILE_IGNORE_NEW_LINES);
			if ($arr === false) {
				trigger_error('Die Datei ' . $this->filename . ' kann nicht gelesen werden', E_USER_ERROR);
			} else {//Daten aus Datei einlesen
				foreach ($arr as $line) {
					$line = str_replace('"', '', trim($line));
					if (preg_match('/^(m\[mi|da\[dx|mo\[mx|yr\[yx)\+{2}\]=\d{2}\.\d{2}\.\d{2}(\s|\|)/', $line)) {
						$matches = explode('=', $line);
						$this->kennung = $matches[0];
						if (count($matches) != 2) {
							trigger_error('Ungültige Zeile "' . $line . '" in Datei ' . $this->filename);
						} else {
							$matches = explode('|', $matches[1]);
							$datum = $matches[0];
							unset($matches[0]);
							$wrAnz = count($matches);
							if (is_null($this->WrAnz)) {
								$this->WrAnz = $wrAnz;
							} else {
								if ($wrAnz != $this->WrAnz) {
									trigger_error('Die Anzahl von Wechselrichtern ist geändert in Zeile "' . $line . '" in Datei ' . $this->filename);
								}
							}
							$wr = 0;
							foreach ($matches as $line1) {
								$wr++;
								$this->data[$datum][$wr] = explode(';', $line1);
							}
						}
					}
				}
			}
		} else {
			if (is_null($kennung)) {
				trigger_error('Die Datei "' . $this->filename . '" existiert nicht und der Parameter $kennung ist auch leer - Parameter $kennung muss bei neuen Dateien angegeben werden !');
			} else {
				$this->kennung = $kennung;
			}
		}
		$this->Hash = md5(serialize($this->data));
	}

	/**
	 * gibt das jüngste (neueste) Datum in den Daten zurück
	 * sind keine Daten enthalten, gibt die Funktion false zurück
	 *
	 * @return string|false
	 */
	public function getNewestDatum() {
		self::sort();
		if (count($this->data) > 0) {
			reset($this->data);
			return key($this->data);
		}
		return false;
	}

	/**
	 * löscht den zum Datum gehörenden Eintrag aus den Daten
	 *
	 * @param string $forDate
	 */
	public function DeleteValue($forDate) {
		if (key_exists($forDate, $this->data)) {
			unset($this->data[$forDate]);
		}
	}

	/**
	 * Gibt den Wert zum Datum zurück
	 * @param string $forDate 
	 */
	public function getValue($forDate){
		return key_exists($forDate, $this->data)?$this->data[$forDate]:null;
	}
	
	/**
	 * gibt das Array mit den Daten zurück
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * setzt das Array mit den Daten
	 * @param array
	 */
	public function setData($data) {
		$this->data = $data;
	}

	/**
	 *
	 * @param string $datum
	 * @param array $werte
	 */
	public function addData($datum, $werte) {
		$this->data[$datum] = $werte;
	}

	/**
	 * Gibt die Anzahl gefundener WR in der Datei zurück
	 * @return integer
	 */
	public function getWrAnz() {
		return $this->WrAnz;
	}

	/**
	 * setzt die Anzahl WR - ist sie schon gesetzt, wird eine Fehlermeldung generiert
	 *
	 * @param integer $wrAnz
	 */
	public function setWrAnz($wrAnz) {
		if ($wrAnz < 1) { //ungültige Anzahl WR
			trigger_error('Die übergebene Anzahl WR ist ungültig $wrAnz=' . $wrAnz);
		}
		if (is_null($this->WrAnz)) {
			$this->WrAnz = $wrAnz;
		} elseif ($this->WrAnz != $wrAnz) { //Ist schon gesetzt und ungleich
			trigger_error('Die $uuml;bergebene Anzahl $wrAnz=' . $wrAnz . ' unterscheidet sich von der schon gesetzten Anzahl $this->WrAnz=' . $this->WrAnz);
		}
	}

	/**
	 * gibt die Kennung der Datei zurück
	 * @return string
	 */
	public function getKennung() {
		return $this->kennung;
	}

	/**
	 * setzt die Kennung
	 * @param string $kennung
	 */
	protected function setKennung($kennung) {
		$this->kennung = $kennung;
	}

	/**
	 * prüft, ob Daten geändert wurden; sowohl Reihenfolge als auch InHalt der Daten im Array
	 *
	 * @return boolean
	 */
	protected function isChanged() {
		return md5(serialize($this->data)) != $this->Hash;
	}

	/**
	 * sortiert $this->$data absteigend - neuestes Datum zuerst
	 * ist $updateHash==true wird auch der hash erneuert
	 *
	 * @param boolean $updateHash
	 */
	protected function sort($updateHash = false) {
		if ((count($this->data) > 0) && (self::isChanged())) {
			uksort($this->data, array($this, "cmp"));
			if ($updateHash)
				self::setHash();
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
	 * 	setzt $this->filename
	 * @param string $filename
	 */
	protected function setFilename($filename) {
		$this->filename = $filename;
	}

	/**
	 * 	gibt den Dateinamen zurück
	 * @return string
	 */
	protected function getFilename() {
		return $this->filename;
	}

	/**
	 * berechnet den Hash-Wert und setzt ihn
	 */
	protected function setHash() {
		$this->Hash = md5(serialize($this->data));
	}

	/**
	 *
	 * @return string
	 */
	protected function getHash() {
		return $this->Hash;
	}

	/**
	 * falls $this->data geändert wurde, wird die Datei neu geschrieben
	 */
	function __destruct() {
		self::sort();
		if (self::isChanged()) {
			if ($fp = @fopen($this->filename, 'wb')) {
				foreach ($this->data as $datum => $data) {
					$line = $this->kennung . '="' . $datum;
					foreach ($data as $wrdata) {
						$line.='|';
						$count = 0;
						if (is_array($wrdata)) {
							foreach ($wrdata as $d) {
								$line.=$d;
								$count++;
								if ($count < count($wrdata)) {
									$line.=';';
								}
							}
						} else {
							$line.=$wrdata;
						}
					}
					$line.='"' . chr(13);
					if (!fwrite($fp, $line)) {
						trigger_error('Fehler beim Schreiben in Datei ' . $this->filename);
					}
				}
				unset($data);
			} else {
				trigger_error('Fehler beim Anlegen der Datei ' . $this->filename);
			}
			fclose($fp);
		}
		unset($this->data);
	}

}

?>
