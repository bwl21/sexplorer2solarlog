<?php

/**
 * Klasse zur Verwaltung/Erzeugung der months.js Datei des Solarlog
 *
 * @version 0.1
 * @author PhotonenSammler <photonensammler@freenet.de>
 */

include_once 'config.inc.php';
include_once 'Classes/classErrorLog.php';
include_once 'Classes/classSExplorerDataNeu.php';

class classMonths_File {

	private $data=array();

	const months='months.js'; //Dateiname der months.js
	const MonthsKennung='mo[mx++]=';

	/**
	 *
	 * @param array $data
	 */
	function __construct($data) {
		$this->data=$data;
	}


/**
 * erzeugt die Datei months.js
 *
 * @param array $data
 */
	function createMonths() {
		self::sort();
		$filename = SLFILE_DATA_PATH . '/'.self::months;
		$fp = @fopen($filename, 'wb');
		if ($fp === false) {
			classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Fehler beim Öffnen von ' . $filename);
		} else {
			reset($this->data);
			$aktdate = key($this->data);
			$aktmonth = substr($aktdate, 3);
			$summe = array();
			for($wr=0;$wr<CSV_ANZWR;$wr++){
				$summe[$wr]=0;
			}
			foreach ($this->data as $datum => $value) {
				if (substr($datum, 3) == $aktmonth) {
					for($wr=0;$wr<CSV_ANZWR;$wr++){
						$summe[$wr]+=$value[$wr][classSExplorerData::etag];
					}
				} else {
					if (!fwrite($fp,self::getLine($aktdate, $summe))) {
						classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Fehler beim Schreiben in ' . $filename);
					}
					$aktdate = $datum;
					$aktmonth = substr($aktdate, 3);
					for($wr=0;$wr<CSV_ANZWR;$wr++){
						$summe[$wr]=$value[$wr][classSExplorerData::etag];
					}
				}
			}
			if ($summe > 0) {
				if (!fwrite($fp, self::getLine($aktdate, $summe))) {
					classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Fehler beim Schreiben in ' . $filename);
				}
			}
			@fclose($fp);
		}
	}


	/**
	 * Hilfsfunktion zum Erzeugen einer Zeile zum Eintrag in months.js
	 *
	 * @param string $aktdate
	 * @param array $data
	 * @return string
	 */
	private function getLine($datum,$summe){
		$line=self::MonthsKennung. '"' . $datum;
		for($i=0;$i<CSV_ANZWR;$i++){
			$line.='|'.$summe[$i];
		}
		return $line.'"'.chr(13);
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
