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
	const kennung='mo[mx++]=';

	/**
	 *
	 * @param array $data
	 */
	function __construct($data) {
		$this->data=$data;
		if($this->data[classSExplorerData::type]!==classSExplorerData::monthly){
			classErrorLog::LogError(date('Y-m-d H:i:s', time()) . ' - Der Typ der übergebenen Daten ist ungültig in ' . __METHOD__);
			die(5);
		}
		unset($this->data[classSExplorerData::type]);
	}



}

?>
