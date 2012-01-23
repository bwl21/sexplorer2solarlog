<?php

/*
 * © PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 */
include_once 'config.inc.php';

/**
 * Verwaltung der Solarlog-Datei base_vars.js
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 */
class classBaseVars {

	const base_vars = 'base_vars.js'; //Dateiname der base_vars.js

	private $data = array();
	private $filename = null;
	private $Hash = null;

	function __construct() {
		$this->filename = realpath(SLFILE_DATA_PATH) . '/' . self::base_vars;
		if (file_exists($this->filename)) {
			ini_set('auto_detect_line_endings', true);
			$arr = @file($this->filename, FILE_SKIP_EMPTY_LINES && FILE_IGNORE_NEW_LINES);
			if ($arr === false) {
				trigger_error('Die Datei ' . $this->filename . ' kann nicht gelesen werden', E_USER_ERROR);
			} else {//Daten aus Datei einlesen
				foreach ($arr as $value) {
					$w = explode('=', $value);
					if (count($w) == 2) {
						$this->data[trim($w[0])] = trim($w[1]);
					}
				}
			}
		}
		$this->Hash = md5(serialize($this->data));
	}

	/**
	 * Gibt den Online-Status des WR aus dem Eintrag 'isOnlie' aus der base_vars.js zurück
	 *
	 * @return boolean
	 */
	public function isOnline(){
		return $this->data['isOnline']=='true'?true:false;
	}


	/**
	 * setzt den Online-Status des WR in der base_vars.js
	 *
	 * @param boolean $status
	 */
	public function setOnline($status){
		$this->data['isOnline']=$status?'true':'false';
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
	 * falls $this->data geändert wurde, wird die Datei neu geschrieben
	 */
	function __destruct() {
		if (self::isChanged()) {
			if ($fp = @fopen($this->filename, 'wb')) {
				foreach ($this->data as $value => $data) {
					$line = $value . ' = ' . $data . chr(13);
					if (!fwrite($fp, $line)) {
						trigger_error('Fehler beim Schreiben in Datei ' . $this->filename);
					}
				}
			} else {
				trigger_error('Fehler beim Anlegen der Datei ' . $this->filename);
			}
			fclose($fp);
		}
		unset($this->data);
	}

}

?>
