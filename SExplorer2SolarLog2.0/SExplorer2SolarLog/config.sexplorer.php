<?php

/**
 * Program to convert datafiles from SExplorer TLX Solar-inverters into the SolarLog dataformat
 * @author PhotonenSammler <photonensammler@freenet.de>
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * written and tested with PHP 5.4
 */
/*
  Diese Datei ist ein Teil von SExplorer2SolarLog.

  SExplorer2SolarLog ist Freie Software: Sie können es unter den Bedingungen
  der GNU General Public License, wie von der Free Software Foundation,
  Version 3 der Lizenz oder jeder späteren veröffentlichten Version,
  weiterverbreiten und/oder modifizieren.

  SExplorer2SolarLog wird in der Hoffnung, dass es nützlich sein wird, aber
  OHNE JEDE GEWÄHELEISTUNG, bereitgestellt; sogar ohne die implizite
  Gewährleistung der MARKTFÄHIGKEIT oder EIGNUNG FÜR EINEN BESTIMMTEN ZWECK.
  Siehe die GNU General Public License für weitere Details.

  <http://www.gnu.org/licenses/>

 * ********************************************
  This file is a part of SExplorer2SolarLog.

  SExplorer2SolarLog is free software: you can use it under the terms of
  the GNU General Public License as published by the Free Software Foundation;
  Version 3 of the License, or any later versions published,
  and distribute / or modify it.

  SExplorer2SolarLog was programmed in the hope that it will be useful,
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

/*
 * ###########################################################################
 * the following is the configuration adapt to the system
 * ###########################################################################
 */

/*
 * true if using a FTP-server where the Inverter-files can be found
 * if USE_FTP == true:   the following values FTP_SERVER, FTP_USERNAME and FTP_PASSWORD shall be defined
 * if USE_FTP == false:  a local source directory is used for the files (see LOCAL_INVERTER_DATA_PATH)
 */
define('USE_FTP', false);
define('FTP_SERVER', 'ftp.myftpserver.com');
define('FTP_USERNAME', 'myFTPUsername');
define('FTP_PASSWORD', 'MyFTPPassword');
define('FTP_PORT', 21);
/*
 * FTP-Timeout in seconds for slow connections higher value( >90 s), for fast connections lower value (usually 90 s)
 */
define('FTP_TIMEOUT', 90);

/* only relevant if USE_FTP == true
 * ftp directory where the data files be found; specified without terminal slash e.g. 'daten' or 'daten/InverterData'
 * FTP-root => is denoted as '.'
 */
define('FTP_INVERTER_DATA_PATH', '.');

/*
 * only relevant if USE_FTP == true
 * a local directory for downloaded temp files; if LOCAL_TEMP_DIR == null the system tempdir is used
 * The temporary directory is determined by the PHP-function sys_get_temp_dir()
 * Please eighth under unix on large-and small letters
 * the path is specified relative to the update-script
 * The script needs permission to read/write in this directory
 */
define('LOCAL_TEMP_DIR', '.');

/*
 * only relevant if USE_FTP == false
 * the local Path to the inverter data files:
 * local directory; specified without terminal slash e.g. 'C:/daten/InverterData'
 * or '../InvertarData'
 * Please eighth under unix on large-and small letters
 * the path is specified relative to the update-script
 */
define('LOCAL_INVERTER_DATA_PATH', '../InverterData');

/*
 * string definition for each inverter
 * for each inverter create an own definition
 * e.g. define('USED_STRINGS_1','1,2'); - on inverter 1 strings 1 and 2 are used
 * e.g. define('USED_STRINGS_2','1,2,3'); - on inverter 2 strings 1, 2 and 3 are used
 * e.g. define('USED_STRINGS_3','1,3'); - on inverter 3 strings 1 and 3 are used
 * the inverters are numbered from 1 to countOfInverters
 */
define('USED_STRINGS_1', '1,2');

/*
 * first part of data filenames (plant name) without the appended date
 * e.g. if the filenames are such as 'AnlageLog-20120218.csv' SEXPLORER_PLANT_NAME shall be 'AnlageLog-'
 */
define('SEXPLORER_PLANT_NAME', 'AnlageLog-');

/*
 * ###########################################################################
 * from here are changes of entries required only in exceptional cases
 * ###########################################################################
 */

 /*
 * the file extension of the SunyyExplorer Data
 * e.g. if the filenames are such as 'AnlageLog-20120218.csv' SEXPLORER_FILE_EXT shall be 'csv'
 */
define('SEXPLORER_FILE_EXT', 'csv');

 /*
 * the decimalpoint used in numerical values in the SunnyExplorer data files
 */
define('SEXPLORER_DECIMALPOINT', ',');

/*
 * Spalte, in der Datum und Uhrzeit in den Dateien steht
 */
define('SEXPLORER_DATE_COLUMN', 1);

/*
 * Datum Zeit Format String der Datumsspalte (Spaltenüberschrift der Spalte)
 */
define('SEXPLORER_DATE_FORMAT', 'dd.MM.yyyy HH:mm');

/*
 * Eintrag muss nur bei mehr als 10 WR oder Änderung des csv-Datenformats geändert/ergänzt werden
 * Nummer der Spalte, in der in der Tagesdatei der Gesamtertrag steht
 * Die Spaltenzählung beginnt bei 1 mit der Spalte Datum
 * für 2 WR könnte der Eintrag folgendermaßen aussehen
 * define('SEXPLORER_DAILY_YIELDSUM_COLUMN','2,4'));
 */
define('SEXPLORER_YIELDSUM_COLUMN', '2,4,6,8,10,12,14,16,18,20');

/*
 * Eintrag muss nur bei mehr als 4 WR oder Änderung des csv-Datenformats geändert/ergänzt werden
 * Spalte, in der in der Tagesdatei die Leistung steht
 * Die Spaltenzählung beginnt bei 1 mit der Spalte Datum
 * für 2 WR könnte der Eintrag folgendermaßen aussehen
 * define('SEXPLORER_DAILY_POWER_COLUMN','3,5');
 */
define('SEXPLORER_POWER_COLUMN', '3,5,7,9,11,13,15,17,19,21');

/*
 * delimiter between data in Inverter data files - usually no need to change this value
 */
define('DELIMITER', ';');
?>



