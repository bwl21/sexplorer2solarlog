<?php

/**
 * Beschreibung von $RCSfile: classDaysHist.php $
 *
 * verwaltet die Solarlog-Datei days_hist.js
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 *
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * 
 *
 * $Date: 2012/01/28 18:48:21 $
 * $Id: classDaysHist.php 6e4ce5cea10f 2012/01/28 18:48:21 Bernhard $
 * $LocalRevision: 90 $
 * $Revision: 6e4ce5cea10f $
 */

/*

    Diese Datei ist Teil von SExplore2SlLog.

    SExplore2SlLog ist Freie Software: Sie können es unter den Bedingungen
    der GNU General Public License, wie von der Free Software Foundation,
    Version 3 der Lizenz oder jeder späteren veröffentlichten Version, 
    weiterverbreiten und/oder modifizieren.

    FuSExplore2SlLog wird in der Hoffnung, dass es nützlich sein wird, aber
    OHNE JEDE GEWÄHELEISTUNG, bereitgestellt; sogar ohne die implizite
    Gewährleistung der MARKTFÄHIGKEIT oder EIGNUNG FÜR EINEN BESTIMMTEN ZWECK.
    Siehe die GNU General Public License für weitere Details.
    
    <http://www.gnu.org/licenses/>

*/


include_once 'config.inc.php';


class classDaysHist extends classSLDataFile{

	private $pMax=array();

	const days_hist='days_hist.js'; //Dateiname der days_hist.js
	const kennung='da[dx++]';

	/**
	 * übergeben wird ein Array mit allen Pmax zu den Tagen, die in classMinDay erzeugt wurden
	 *
	 * @param array $pmax
	 */
	function __construct($pmax=array()) {
		ini_set('date.timezone', TIMEZONE);
		parent::__construct(realpath(SLFILE_DATA_PATH).'/'.self::days_hist,self::kennung);
		$this->pMax=$pmax;
	}

	/**
	 * gibt Pmax zum Datum zurück
	 * Falls der Eintrag nicht existiert, wird 0 zurückgegeben
	 *
	 * @param string $datum
	 * @param integer $wr
	 * @return integer
	 */
	private function getPmax($datum,$wr){
		if(key_exists($datum, $this->pMax)){
			return key_exists($wr, $this->pMax[$datum])?$this->pMax[$datum][$wr]:0;
		}else{
			return 0;
		}
	}

/**
 * erzeugt/ergänzt die Datei days_hist.js
 */
	public function check(){
		$NewestDatum=self::getNewestDatum();
		$endDate=time()-86400;
		if($NewestDatum===false){ //Datei existiert nicht, erzeugen
			self::setWrAnz(CSV_ANZWR);//Anzahl WR setzen
			$startDate=strtotime(START_DATUM);
		}else{
			$startDate=$endDate;
		}
		while($startDate<=$endDate){
			//Dateinamen der csv-Datei für den Vortag ermitteln und Datei öffnen
			$SexplorerData=new classSExplorerData(realpath(SEXPLORER_DATA_PATH).'/'.CSV_ANLAGEN_NAME.'-'.date('Ym',$startDate).'.csv');
			$SExplorerNewestDatum=$SexplorerData->getNewestDate();
			if($SExplorerNewestDatum!==false){ //Es sind Daten vorhanden
				if($NewestDatum===false){
					$NewestDatum=$SexplorerData->getOldestDate();
				}
				if($NewestDatum!=$SExplorerNewestDatum){ //Neue Daten vorhanden
					$SexplorerData->setPointerToDate($NewestDatum);
					$wrAnz=self::getWrAnz();
					$werte=$SexplorerData->getCurrentValues();
					while($werte!==false){
						$datum=key($werte);
						$w=array();
						for($i=0;$i<$wrAnz;$i++){
							$w[$i][]=$werte[$datum][$i][classSExplorerData::etag]; //ETag
							$w[$i][]=self::getPmax($datum, $i); //PAC max
						}
						self::addData($datum,$w);
						$werte=$SexplorerData->getPrevValues();
					}
					unset($werte);
				}
			}
			unset($SexplorerData);
			$startDate=  strtotime("+1 month", $startDate);
		}
	}


}

?>
