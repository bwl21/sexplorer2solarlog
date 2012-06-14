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
 * $RCSfile$
 * $Date$
 * $Id$
 * $LocalRevision$
 * $Revision$
 */
 

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