<?php

/**
 * Klasse zur Verwaltung/Erzeugung von minxxxx.js files des Solarlog
 * (min_day.js und minYYMMDD.js)
 * @version 0.1
 * @author PhotonenSammler <photonensammler@freenet.de>
 */

include_once 'config.inc.php';
include_once 'Classes/classErrorLog.php';
include_once 'Classes/classSExplorerDataNeu.php';

class classMin_File {

	private $data=array();

	const min_day='min_day.js'; //Dateiname der min_day.js
	const kennung='m[mi++]=';
	const fileTypeMin_day='min_day';
	const fileTypeMinYYMMDD='minYYMMDD';


	/**
	 *
	 * @param array $data
	 */
	function __construct($data) {
		$this->data=$data;
		if($this->data[classSExplorerData::type]!==classSExplorerData::daily){
			classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Der Typ der übergebenen Daten ist ungültig in ' . __METHOD__);
			die(5);
		}
		unset($this->data[classSExplorerData::type]);
	}



	/**
	 * Erzeugt eine min_day.js oder minYYMMDD.js Datei
	 * @param string $fileType Typ der zu erzeugenden Datei
	 * @return boolean
	 */
	private function createSLFile($fileType=self::fileTypeMin_day){
		if(count($this->data)>0){
			self::sort();
			reset($this->data);
			$aktdate=substr(key($this->data),0,8);
			if($fileType==self::fileTypeMin_day){ //min_day.js erzeugen
				$filename = SLFILE_DATA_PATH . '/' . self::min_day;
			}elseif($fileType==self::fileTypeMinYYMMDD){ //minYYMMDD.js erzeugen
				//Dateiname der Datei aus dem Datum in den Daten ermitteln
				$filename = SLFILE_DATA_PATH . '/min' .strrev(str_replace('.','', $aktdate)).'.js';
			}else{ //ungültiger Dateityp
				classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Ungültiger Dateityp ' . $filenType . ' übergeben in ' . __METHOD__);
				return false;
			}
			//Datei erzeugen
			$fp = @fopen($filename, 'wb');
			if ($fp === false) {
				classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Fehler beim Erzeugen der Datei ' . $filename . ' in ' . __METHOD__);
			} else {
				foreach ($this->data as $datum => $value) {
					if(substr($datum,0,8)==$aktdate){//Datum prüfen
						$line=self::kennung . '"' . $datum;
						for($i=0;$i<CSV_ANZWR;$i++){
							$line.='|' . $value[$i][classSExplorerData::p] . ';' . $value[$i][classSExplorerData::p] . ';' . $value[$i][classSExplorerData::etag] . ';0"';
						}
						$line.=chr(13);
						if (!fwrite($fp,$line)) {
							classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Fehler beim Schreiben in die Datei ' . $filename . ' in ' . __METHOD__);
							return false;
						}
					}
				}
				@fclose($fp);
			}
		}else{
			return false;
		}
		return true;
	}


	/**
	 *	 erzeugt aus den Daten die Solarlog-Datei min_day.js
	 * @return boolean
	 */
	public function createMin_day() {
		return self::createSLFile(self::fileTypeMin_day);
	}


	/**
	 *	 erzeugt aus den Daten die Solarlog-Datei minYYMMDD.js
	 * @return boolean
	 */
	public function createMinYYMMDD() {
		return self::createSLFile(self::fileTypeMinYYMMDD);
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
		$a = explode('.', $a);
		$a1 = substr($a[2], 3);
		$a[2] = '20' . substr($a[2], 0, 2);
		$b = explode('.', $b);
		$b1 = substr($b[2], 3);
		$b[2] = '20' . substr($b[2], 0, 2);
		return strtotime($b[2] . '-' . $b[1] . '-' . $b[0] . ' ' . $b1) -
						strtotime($a[2] . '-' . $a[1] . '-' . $a[0] . ' ' . $a1);
	}


}

?>
