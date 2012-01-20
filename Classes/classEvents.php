<?php

/**
 * classEvents Klasse zum verwalten der Datei events.js des Solarlog
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 */
class classEvents extends classSLDataFile {
	/* Datenformat von $this->data
	 *
	 * array
	 *  '20.01.12 15:52:15' => //Uhrzeit von
	 *    array
	 *      0 => //Wr-Nummer (WR1)
	 *        array
	 *          0 => string '20.01.12 15:52:44' (length=17) //Uhrzeit bis
	 *          1 => string '6' (length=1) //statuscode
	 *          2 => string '0' (length=1) //Fehlercode
	 *      1 => //Wr-Nummer (WR2)
	 *        array
	 *          0 => string '20.01.12 15:52:14' (length=17) //Uhrzeit bis
	 *          1 => string '3' (length=1)		//Statuscode
	 *          2 => string '0' (length=1)		//Fehlercode
	 */

	/**
	 * der constructor liest die Datei ein wenn sie existiert und legt die Daten auf $this->data ab
	 * wenn die Datei nicht existiert, wird die übergebene Kennung zum Erzeugen der Datei verwendet
	 * falls die Daten geändert wurden
	 *
	 * @param string $filename
	 * @param string $kennung
	 */
	function __construct($filename, $kennung=null) {
		self::setFilename($filename);
		if (@file_exists(self::getFilename())) {//Wenn Datei da ist, öffnen und einlesen
			ini_set('auto_detect_line_endings', true);
			$arr = @file(self::getFilename(), FILE_SKIP_EMPTY_LINES && FILE_IGNORE_NEW_LINES);
			if ($arr === false) {
				trigger_error('Die Datei ' . self::getFilename() . ' kann nicht gelesen werden', E_USER_ERROR);
			} else {//Daten aus Datei einlesen
				foreach ($arr as $line) {
					$line = str_replace('"', '', trim($line));
					if (preg_match('/^e\[ev\+{2}\]=\d{2}\.\d{2}\.\d{2}\s\d{2}:\d{2}:\d{2};/', $line)) {
						$matches = explode('=', $line);
						self::setKennung($matches[0]);
						if (count($matches) != 2) {
							trigger_error('Ungültige Zeile "' . $line . '" in Datei ' . $this->filename);
						} else {
							$matches = explode(';', $matches[1]);
							$datum = $matches[0];
							unset($matches[0]);
							if (count($matches) == 4) {
								$wr = $matches[2];
								unset($matches[2]);
								if ((is_null(self::getWrAnz())) || (self::getWrAnz() < $wr + 1)) {
									self::setWrAnz($wr + 1);
								}
								$matches = array_values($matches);
								self::addData($datum, array($wr => $matches));
							} else {
								trigger_error('Ungültige Zeile "' . $line . '" in Datei ' . $this->filename);
							}
						}
					}
				}
			}
		} else {
			if (is_null(self::getKennung())) {
				trigger_error('Die Datei "' . self::getFilename() . '" existiert nicht und der Parameter $kennung ist auch leer - Parameter $kennung muss bei neuen Dateien angegeben werden !');
			} else {
				self::setKennung($kennung);
			}
		}
		self::setHash();
	}

	/**
	 * falls $this->data geändert wurde, wird die Datei neu geschrieben
	 */
	function __destruct() {
		self::sort();
		if (self::isChanged()) {
			if ($fp = @fopen(self::getFilename(), 'wb')) {
				foreach (self::getData() as $datum => $data) {
					$line = self::getKennung() . '="' . $datum . ';';
					foreach ($data as $wr => $wrdata) {
						$line.=$wrdata[0] . ';' . $wr . ';' . $wrdata[1] . ';' . $wrdata[2] . '"' . chr(13);
					}
					if (!fwrite($fp, $line)) {
						trigger_error('Fehler beim Schreiben in Datei ' . self::getFilename());
					}
				}
			} else {
				trigger_error('Fehler beim Anlegen der Datei ' . self::getFilename());
			}
			fclose($fp);
		}
		unset($data);
	}

}

?>
