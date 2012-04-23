<?php

class InverterDataFactory {


	private function __construct(){}
	private function __clone(){}

	static function getInverter($inverterType){
		

	    $inverter = null;

		switch ($inverterType) {

			case "SunnyExplorer":
				include_once 'classInverterDataSunnyExplorer.php';
				$inverter = new classInverterDataSunnyExplorer();
				break;
					
			case "Danfoss":
				include_once 'classInverterDataDannfoss.php';
				$inverter = new classDannfossData();
				break;
				 
			default:
				trigger_error('Fatal error: unsupported inverter "' . $inverterType . '" in ' . __METHOD__ . ' line ' . __LINE__, E_ERROR) ;
		}

		trigger_error("loading ".$inverterType , E_USER_NOTICE);
		
		return $inverter;

	}
}



?>
