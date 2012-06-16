<?php

/**
 *
 * Factory class to select the Inverter frontend solar-inverters to be converted into the SolarLog dataformat
 * @author Bernhard Weichel (www.weichel21.de)
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 *                Bernhard Weichel <http://www.weichel21.de>
 * written and tested with PHP 5.4
 */
 
 /**
 * $RCSfile: classInverterDataFactory.php $
 * $Date: 2012/06/16 17:58:52 $
 * $Id: classInverterDataFactory.php 4cdc2d05a44c 2012/06/16 17:58:52 Bernhard $
 * $LocalRevision: 160 $
 * $Revision: 4cdc2d05a44c $
 */
 

class InverterDataFactory {


	private function __construct(){}
	private function __clone(){}

	static function getInverter($inverterType){
		

	    $inverter = null;

		switch ($inverterType) {

			case "SunnyExplorer":
				include_once realpath(dirname(__FILE__)).'/classInverterDataSunnyExplorer.php';
				$inverter = new classInverterDataSunnyExplorer();
				break;
					
			case "Danfoss":
				include_once realpath(dirname(__FILE__)).'/classInverterDataDanfoss.php';
				$inverter = new classDanfossData();
				break;
				 
			default:
				trigger_error('Fatal error: unsupported inverter "' . $inverterType . '" in ' . __METHOD__ . ' line ' . __LINE__, E_ERROR) ;
		}

		trigger_error("loading ".$inverterType , E_USER_NOTICE);
		
		return $inverter;

	}
}



?>
