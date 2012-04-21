<?php

class InverterDataFactory {


	private function __construct(){}
	private function __clone(){}

	static function getInverter($inverterType){
		
        print $inverterType;

	    $inverter = null;

		switch ($inverterType) {

			case "SunnyExplorer":
				include_once 'classInverterDataSunnyExplorer.php';
				$inverter = new classSunnyExplorerData();
				print "loading sunny Explorer\n";
				break;
					
			case "Danfoss":
				include_once 'classInverterDataDannfoss.php';
				$inverter = new classDannfossData();
				print "loading sunny Explorer\n";
				break;
				 
			default:
				print('Fatal error: unsupported inverter "' . $inverterType . '" in ' . __METHOD__ . ' line ' . __LINE__) ;
		}

		return $inverter;

	}
}



?>
