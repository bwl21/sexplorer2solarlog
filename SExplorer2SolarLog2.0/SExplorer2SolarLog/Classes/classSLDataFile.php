<?php

/**
 * Interface class for convert datafiles from solar-inverters into the SolarLog dataformat
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


class classSLDataFile {

	private $filename = null; //Dateiname der Solarlog-Daten-Datei
	private $kennung = null; //Kennung der Daten 'm[mi++]','da[dx++]','mo[mx++]' oder 'ye[yx++]'
	private $WrAnz = null; //Anzahl erkannter WR in der Datei

	/* Format von $this-data für min_day.js oder minYYMMDD.js - Datei
	 * Aus jedem Datumseintrag wrd eine Dateizeile gebildet
	 * Beispiel
	 * m[mi++]="18.03.12 13:25:00|3198;1724;1579;17833;359;320;47|3777;2010;1889;17566;365;331;30"
	 *
	 * für die anderen Dateien (days_hist.js, months.js, years.js usw.) sieht das Array ähnlich aus
	 * Die Werte im Array sind von den Einträgen in den Dateien abhängig
	 *
	 * array (size=86)
	 *  '18.03.12 13:25:00' =>							// Datum Zeit
	 *   array (size=2)
	 *    0 =>								// WR 1
	 *     array
	 * 			0 => int 3198 		// PAC		[W]
	 * 			1 => int 1724 		// PDC 1 [W]
	 * 			2 => int 1579 		// PDC 2 [W]	falls vorhanden, sonst weglassen
	 * 			3 => int 17833		// Etag [Wh]
	 * 			4 => int 359			// UDC 1 [V]
	 * 			5 => int 320			// UDC 2 [V] falls vorhanden, sonst weglassen
	 * 			6 => int 47				// WR-Temperatur falls vorhanden, sonst weglassen
	 *    1 =>								// WR 2
	 *     array
	 * 			0 => int 3777
	 * 			1 => int 2010
	 * 			2 => int 1889
	 * 			3 => int 17566
	 * 			4 => int 365
	 * 			5 => int 331
	 * 			6 => int 30
	 *  '18.03.12 13:15:00' =>
	 *   array (size=1)
	 *    0 =>
	 *     array
	 * 			0 => int 4088
	 * 			1 => int 2177
	 * 			2 => int 2039
	 * 			3 => int 17256
	 * 			4 => int 361
	 * 			5 => int 335
	 * 			6 => int 991
	 * '18.03.12 13:10:00' =>
	 *  array (size=1)
	 *   1 =>
	 *    array
	 * 			0 => int 4094
	 * 			1 => int 2180
	 * 			2 => int 2040
	 * 			3 => int 16920
	 * 			4 => int 367
	 * 			5 => int 255
	 * 			6 => int 122	 */
	private $data = null; //Die Daten aus der Datei
	private $Hash = null; //Checksumme des Daten-Arrays, bei Veränderung wird gespeichert

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
					$line = str_replace(chr(9),' ',str_replace('"', '', trim($line)));
					if (preg_match('/^((m\[mi)|(da\[dx)|(mo\[mx)|(ye\[yx))\+{2}\]=\d{2}\.\d{2}\.\d{2}(\s|\|)/', $line)) {
						$matches = explode('=', $line);
						$this->kennung = $matches[0];
						if (count($matches) != 2) {
							trigger_error('Ungültige Zeile "' . $line . '" in Datei ' . $this->filename);
						} else {
							$matches = explode('|', $matches[1]);
							$datum = trim($matches[0]);
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
								$this->data[$datum][$wr] = explode(';', trim($line1));
								$wr++;
							}
							unset($matches);
						}
					}
				}
				unset($arr);
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
	 * gibt das jüngste (neueste) Datum in den Daten in der Form 2012-05-21 07:50:00 zurück
	 * sind keine Daten enthalten, gibt die Funktion false zurück
	 *
	 * @return string|false
	 */
	public function getNewestDatum() {
		self::sort();
		if (count($this->data) > 0) {
			reset($this->data);
			$d = key($this->data);
			return '20' . substr($d, 6, 2) . '-' . substr($d, 3, 2) . '-' . substr($d, 0, 2). substr($d, 8);
		}
		return false;
	}

	/**
	 * gibt das älteste  Datum in den Daten in der Form 2012-05-21 07:50:00 zurück
	 * sind keine Daten enthalten, gibt die Funktion false zurück
	 *
	 * @return string|false
	 */
	public function getOldestDatum() {
		self::sort();
		if (count($this->data) > 0) {
			end($this->data);
			$d = key($this->data);
			return '20' . substr($d, 6, 2) . '-' . substr($d, 3, 2) . '-' . substr($d, 0, 2). substr($d, 8);
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
	public function getValue($forDate) {
		return (is_null($this->data)) ? null : key_exists($forDate, $this->data) ? $this->data[$forDate] : null;
	}

	/**
	 * gibt das Array mit den Daten zurück
	 * @return array
	 */
	public function getData() {
		self::sort();
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
		foreach ($werte as $wr => $value) {
			foreach ($value as $key => $wert) {
				if (is_numeric($wert)) {
					$werte[$wr][$key] = intval($wert);
				}
			}
		}
		$this->data[$datum] = $werte;
	}

	/**
	 * Gibt die Anzahl gefundener WR in den Daten zurück
	 * @return integer||null
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
			trigger_error('Die übergebene Anzahl WR ist ungültig $wrAnz=' . $wrAnz. ' in '.__FILE__. ' Line='.__LINE__);
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
	public function __destruct() {
		if (!is_null($this->data)) {
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

}

?>
