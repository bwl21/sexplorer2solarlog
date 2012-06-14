<?php

/**
 * Program to convert datafiles from Solar-inverters into the SolarLog dataformat
 * @author PhotonenSammler <photonensammler@freenet.de>
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * written and tested with PHP 5.4
 */
/*
  Diese Datei ist ein Teil von InverterData2SolarLog.

  InverterData2SolarLog ist Freie Software: Sie k�nnen es unter den Bedingungen
  der GNU General Public License, wie von der Free Software Foundation,
  Version 3 der Lizenz oder jeder sp�teren ver�ffentlichten Version,
  weiterverbreiten und/oder modifizieren.

  InverterData2SolarLog wird in der Hoffnung, dass es n�tzlich sein wird, aber
  OHNE JEDE GEW�HELEISTUNG, bereitgestellt; sogar ohne die implizite
  Gew�hrleistung der MARKTF�HIGKEIT oder EIGNUNG F�R EINEN BESTIMMTEN ZWECK.
  Siehe die GNU General Public License f�r weitere Details.

  <http://www.gnu.org/licenses/>

 * ********************************************
  This file is a part of InverterData2SolarLog.

  InverterData2SolarLog is free software: you can use it under the terms of
  the GNU General Public License as published by the Free Software Foundation;
  Version 3 of the License, or any later versions published,
  and distribute / or modify it.

  InverterData2SolarLog was programmed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY provided, without even the implied
  Warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  See the GNU General Public License for more details.

  <http://www.gnu.org/licenses/>
 */

/*
 * you must execute the script update.php in a cronjob whenever new inverter data available
 * this script converts the inverter data into solarlog format
 * for more information visit http://photonensammler.homedns.org/Danfoss2SolarLog
 */ 
    require_once 'configs.test/config.general.php';
    require_once 'configs.test/config.sexplorer.php';
    require_once 'configs.test/config.solarlog.php';
    
	require_once 'Classes/update.php';
	
?>