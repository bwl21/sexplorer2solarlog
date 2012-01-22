<?php

/**
 * Klasse zur Verwaltung/Erzeugung von min_day.js files des Solarlog
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 */

include_once 'config.inc.php';

class classMin_day extends classSLDataFile{

	private $isNewDay=false;//True wenn ein neuer Tag

	const min_day='min_day.js'; //Dateiname der min_day.js
	const kennung='m[mi++]';

	function __construct() {
		parent::__construct(realpath(SLFILE_DATA_PATH).'/'.self::min_day,self::kennung);
	}

	/**
	 * gibt True zurück wenn ein neuer Tag verarbeitet wurde
	 *
	 * @return boolean
	 */
	public function isNewDay(){
		return $this->isNewDay;
	}


	/**
	 * prüft die Aktualität der min_day.js und erzeugt bei Bedarf auch die min YYMMDD.js
	 */
	public function check(){
		$NewestDatum=self::getNewestDatum();
		$endDate=time();
		if($NewestDatum===false){ //Datei existiert nicht, erzeugen
			self::setWrAnz(CSV_ANZWR);//Anzahl WR setzen
			$this->isNewDay=true;
			$startDate=strtotime(START_DATUM);
		}else{
			$startDate=$endDate;
		}
		while($startDate<=$endDate){
			//Dateinamen der csv-Datei für aktuelles Datum ermitteln und Datei öffnen
			$SexplorerData=new classSExplorerData(SEXPLORER_DATA_PATH.'/'.CSV_ANLAGEN_NAME.'-'.date('Ymd',$startDate).'.csv');
			$SExplorerNewestDatum=$SexplorerData->getNewestDate();
			if($SExplorerNewestDatum!==false){ //Es sind Daten vorhanden
				if($NewestDatum===false){
					$NewestDatum=$SexplorerData->getOldestDate();
				}else{
					//Wenn das neueste Datum im min-File älter als das Datum der csv-Datei ist -> neuer Tag
					//die Datei minYYMMDD mit den Daten aus der alten min-Datei erzeugen
					//Dadurch wird gewährleistet, dass die Datei vom Vortag erst am nächsten Morgen bei
					//Inbetriebnahmeder WR erzeugt wird
					if($this->isNewDay=(substr($NewestDatum,0,8)!=substr($SExplorerNewestDatum,0,8))){//Datei Vortag erzeugen
						$min=new classMinYYMMDD(substr($NewestDatum,0,8));
						$min->setData(self::getData());
						unset($min);
						self::setData(null);
						$NewestDatum=$SexplorerData->getOldestDate();
					}
				}
				if($NewestDatum!=$SExplorerNewestDatum){ //Neue Daten vorhanden
					$SexplorerData->setPointerToDate($NewestDatum);
					$wrAnz=self::getWrAnz();
					$werte=$SexplorerData->getCurrentValues();
					while($werte!==false){
						$datum=key($werte);
						$w=array();
						for($i=0;$i<$wrAnz;$i++){
							$w[$i][]=$werte[$datum][$i][classSExplorerData::p]; //PAC
							$w[$i][]=$werte[$datum][$i][classSExplorerData::p]; //PDC
							$w[$i][]=$werte[$datum][$i][classSExplorerData::etag]; //ETag
							$w[$i][]=0; //UDC
						}
						self::addData($datum,$w);
						$werte=$SexplorerData->getPrevValues();
					}
					unset($werte);
				}
			}
			unset($SexplorerData);
			$startDate+=86400;
		}

	}




}

?>
