<?php

/**
 * verwaltet die Solarlog-Datei days_hist.js
 * @version 0.5
 * @author PhotonenSammler <photonensammler@freenet.de>
 */
class classDaysHist {

	private $data = array();

	const Days_hist='days_hist.js'; //Dateiname days_hist.js
	const DaysHistKennung='da[dx++]=';

	/**
	 *
	 * @param array $data
	 */
	function __construct($data) {
		$this->data = $data;
	}

	/**
	 * @version 0.3
	 * erzeugt die Datei days_hist.js
	 *
	 * @param array $data
	 */
	function createDays_hist() {
		self::sort();
		$filename = SLFILE_DATA_PATH . '/' . self::Days_hist;
		$fp = @fopen($filename, 'wb');
		if ($fp === false) {
			classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Fehler beim Öffnen von ' . $filename);
		} else {
			foreach ($this->data as $datum => $value) {
				$line = self::DaysHistKennung . '"' . $datum;
				for ($wr = 0; $wr < CSV_ANZWR; $wr++) {
					$line.='|' . $value[$wr][classSExplorerData::etag] . ';0';
				}
				$line.='"' . chr(13);
				if (!fwrite($fp, $line)) {
					classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Fehler beim Schreiben in ' . $filename);
				}
			}
			@fclose($fp);
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
