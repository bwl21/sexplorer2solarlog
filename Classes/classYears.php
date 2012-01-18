<?php

include_once 'config.inc.php';

/**
 * Beschreibung von classYears
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 */
class classYears extends classSLDataFile {
	const years='years.js'; //Dateiname der years.js
	const kennung='ye[yx++]';

	function __construct() {
		parent::__construct(realpath(SLFILE_DATA_PATH) . '/' . self::years, self::kennung);
	}

	/**
	 * erzeugt/ergänzt die Datei years.js
	 */
	public function check() {
		$SLNewestDate = self::getNewestDatum();
		if ($SLNewestDate === false) { //Datei existiert nicht, erzeugen
			self::setWrAnz(CSV_ANZWR); //Anzahl WR setzen
		}
		$year=date('Y', time() - 86400);
		//Alle Montsdateien einlesen
		$SexplorerData=array();
		$aktMonth=date('n', time() - 86400);
		for($i=1;$i<=$aktMonth;$i++){
			$filename=SEXPLORER_DATA_PATH . '/' . CSV_ANLAGEN_NAME . '-' . $year.  str_pad($i, 2,'0',STR_PAD_LEFT) . '.csv';
			$SexplorerData[$i] = new classSExplorerData($filename);
			if($SexplorerData[$i]->getNewestDate()===false){
				unset($SexplorerData[$i]);
			}else{
				$lastIndex=$i;
			}
		}
		if(count($SexplorerData)>0){
			//Jahressumme bilden
			$w = array_fill(0, self::getWrAnz(), 0);
			$SExplNewestDate = $SexplorerData[$lastIndex]->getNewestDate();
			//Eintrag für Datum eventuell löschen
			if($SLNewestDate!==FALSE){//es sind schon Daten vorhanden
				if(($SExplNewestDate!==$SLNewestDate) && (substr($SExplNewestDate,6)==substr($SLNewestDate,6))){
					self::DeleteValue($SLNewestDate); //Eintrag für das Jahr löschen
				}
			}
			foreach($SexplorerData as $SExplData){
				$SExplData->setPointerToDate($SExplData->getOldestDate());
				$werte = $SExplData->getCurrentValues();
				while ($werte !== false) {
					$key = key($werte);
					for ($i = 0; $i < self::getWrAnz(); $i++) {
						$w[$i]+=$werte[$key][$i][classSExplorerData::etag];
					}
					$werte = $SExplData->getPrevValues();
				}
				$this->addData($SExplNewestDate, $w);
				unset($w);
			}
		}
		unset($SexplorerData);
	}


}

?>
