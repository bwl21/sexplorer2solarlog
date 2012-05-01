<?php

/**
 *
 * Interface class for convert datafiles from solar-inverters into the SolarLog dataformat
 * @author PhotonenSammler <photonensammler@freenet.de>
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * written and tested with PHP 5.4
 */
  /**
  * $Date: 2012/05/01 10:31:52 $
 * $Id: classInverterDataInterface.php cc60d8753ddd 2012/05/01 10:31:52 Bernhard $
 * $LocalRevision: 144 $
 * $Revision: cc60d8753ddd $
 */
/*
  Diese Datei ist ein Teil von InverterData2SolarLog.

  InverterData2SolarLog ist Freie Software: Sie können es unter den Bedingungen
  der GNU General Public License, wie von der Free Software Foundation,
  Version 3 der Lizenz oder jeder späteren veröffentlichten Version,
  weiterverbreiten und/oder modifizieren.

  InverterData2SolarLog wird in der Hoffnung, dass es nützlich sein wird, aber
  OHNE JEDE GEWAERHRLEISTUNG, bereitgestellt; sogar ohne die implizite
  Gewaehrleistung der MARKTFAEHIGKEIT oder EIGNUNG FUER EINEN BESTIMMTEN ZWECK.
  Siehe die GNU General Public License für weitere Details.

  <http://www.gnu.org/licenses/>

 * ********************************************
  This file is a part of Convert2SolarLog.

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

/* general information
 *
 * Return parameters when running for example as cmd-process in Windows
 * and critical errors that lead to program termination
 * 0 -> everything flawlessly executed, any non-critical errors have been logged in the error logfile
 * 1 -> the error logfile could not be opened
 * 2 -> data cannot be written into the error logfile
 * 3 -> error in the constructor (see in error logfile)
 * 4 -> error in the constructor (see in error logfile)
 * 5 -> error in the constructor (see in error logfile)
 * 6 -> fatal error when connecting to FTP-server
 * 7 -> fatal error at login on FTP-server
 * 8 -> fatal error when turning on the passive mode
 * 9 -> fatal error when switching FTP-directorie
 * 10 -> fatal error when redaing files in FTP directory
 * 11 -> fatal error when setting FTP-timeout value
 * All other error messages are logged in the error logfile
 *
 * Note that error log file is specified in config.general.php (ERROR_LOG_FILE)
 */



interface classInverterDataInterface {
	
	public function __construct();

	/**
	 * This is the construcotr. It loads all data which is <e>newer</e> than $startdate. <br>
	 * Note that data for $startdate is not  returned !!! <br>
	 * If $startdate===0 then all available data is loaded. <br>
	 * If solarlog files are available the provided $startDate is the date+time of the latest available value in solarlog files. <br>
	 *
	 * @param timestamp||string||0 $startDate YYYY-MM-DD HH:II:SS date('Y-m-d H:i:s',...) or timestamp or 0
	 * @todo: what is "string" here?
	 */
	public function loadData($startDate = 0);

	/**
	 * This method returns the count of inverters determined from inverter data. <br>
	 * If no data is available then the method shall return "false". <br>
	 * The method ist called only if the number of inverters is not known (in particular no solarlog files are available). <br>
	 * If soloarlog files are available, then number of inverters is determined by the number of inverters in solarlog files <br>
	 * and the method is not called. <br>
	 *
	 * @return integer||false
	 */
	public function getInverterCount();

	/**
	 * This method returns an array with all available data for the day denoted by the parameter $date. <br>
	 * Note that time parts in $date are be ignored. In other words for each day all data of the day shall be provided <br>
	 * Available data is determined according to the $startDate in the constructor (see the description for __construct($startDate) ). <br>
	 *
	 * The returned array shall be sorted descending - the newest data comes in index 0, first and the oldest data  last. <br>
	 * If no data is available the method shall return "false". <br>
	 *
	 * The array elements shall have the following structure: <br>
	 * You have to use the array index names 'P_AC', 'E_DAY', 'T_WR'. 'U_DC', 'P_DC' <br>
	 * and the inverter numbers in the array index from 0..n for inverter number 1..n-1. <br>
	 * The same applies to the number of strings from 0..m for string number 1..m-1. <br>
	 *
	 *  @todo: describe the data structure more precisely. Is it an associative array. Why shall it be ordered if there are access-keys.
	 *
	 * 	'2012-03-12 15:20:00' =>						//Date Time of value <br>
	 * 		array
	 * 			0 =>													//Inverter 1	<br>
	 * 				array	<br>
	 * 					'P_AC' => integer 3417				//PAC power in W must be present	<br>
	 * 					'E_DAY' => integer 26				//yield of day at the date/time in Wh must be present <br>
	 * 					'T_WR' => integer 37				//Temperature inverter 1 in °C if present otherwise omit <br>
	 * 					0 =>											//Inverter 1 String 1 must be present because a inverter has at least one string <br>
	 * 						array <br>
	 * 							'U_DC' => integer 428		//UDC of String 1 in V must be present, can also be 0 <br>
	 * 							'P_DC' => integer 1739		//PDC power of String 1 in W must be present <br>
	 * 					1 =>											//Inverter 1 String 2 if present, otherwise omit <br>
	 * 						array <br>
	 * 							'U_DC' => integer 427		//in V <br>
	 * 							'P_DC' => integer 1727		//in W <br>
	 * 					2 =>											//inverter 1 string 3 if present, otherwise omit <br>
	 * 						array <br>
	 * 							'U_DC' => integer 0 <br>
	 * 							'P_DC' => integer 0 <br>
	 * 			1 =>													//Inverter 2 if present, otherwise omit - see description of inverter 1 <br>
	 * 				array <br>
	 * 					'P_AC' => integer 3417 <br>
	 * 					'E_DAY' => integer 26 <br>
	 * 					'T_WR' => integer 37 <br>
	 * 					0 =>											//inverter 2 String 1 <br>
	 * 						array <br>
	 * 							'U_DC' => integer 428 <br>
	 * 							'P_DC' => integer 1739 <br>
	 * 					1 =>											//inverter 2 String 2 <br>
	 * 						array <br>
	 * 							'U_DC' => integer 427 <br>
	 * 							'P_DC' => integer 1727 <br>
	 * 					2 =>											//inverter 2 String 3 <br>
	 * 						array <br>
	 * 							'U_DC' => integer 0 <br>
	 * 							'P_DC' => integer 0 <br>
	 * 	'2012-03-12 15:15:00' =>						//Date Time of the previous value <br>
	 * 		array <br>
	 * 			0 =>													//Inverter 1 <br>
	 * 				array <br>
	 * 					'P_AC' => integer 3417				//in W must be present <br>
	 * 					'E_DAY' => integer 26				//in Wh must be present <br>
	 * 									. <br>
	 * 									. <br>
	 * 									. <br>
	 * 									. <br>
	 * 									. <br>
	 *
	 * @param string||timestamp $date $date as YYYY-MM-DD HH:II:SS date('Y-m-d H:i:s',...)or YYYY-MM-DD date('Y-m-d',...)or timestamp
	 * the time in $date will be ignored
	 *
	 * @return array||false
	 */
	public function getDailyData($date);

	/**
	 * This method returns the latest date/time available in the data in format DD.MM.YY HH:II:SS date('d.m.y H:i:s',...). <br>
	 * The date format shall be according to the date format in the solarlog files <br>
	 * in german date format Day.Month.Year (all double-digit). <br>
	 * If no data is available the method shall return false. <br>
	 * Otherwise the value returned is the date for the newest available data.
	 *
	 * @return string||false
	 */
	public function getNewestDate();

	/**
	 * Return the value of the newest date/time in data in format DD.MM.YY HH:II:SS date('d.m.y H:i:s',...). <br>
	 * The date format is determined by the used date format in the solarlog files <br>
	 * in german date format Day.Month.Year (all double-digit). <br>
	 * If no data available return the function false. <br>
	 * Usually the returned value ist the date of the next value after the data provided in the in the constructor <br>
	 * because the data for the $startdate provided in constructor is not loaded. The oldest data which is loded is the data
	 * of the day after $startdate provided in constructor.
	 *
	 * @return string||false
	 */
	public function getOldestDate();
}

?>
