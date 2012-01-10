<?php

/*
 * © PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * 2012-07-01
 *
 * Konfigurationseinstellungen zum Script SExplorer2SLog.php
 * 	Konvertierung von SunnyExplorer-Dateien in das Solarlog-Datenformat
 */

/* allgemeine Hinweise
 *
 * Rückgabeparameter beim Ausführen z.B. als cmd-Prozess in Windows
 * 0 -> alles fehlerfrei abgearbeitet, eventuelle Fehler wurden in Datei geloggt
 * 1 -> Die Datei zum Logging der Fehlermeldungen kann nicht geöffnet werden
 * 2 -> In die Datei zum Logging der Fehlermeldungen kann nicht geschrieben werden
 *
 * Ansonsten werden alle Fehlermeldungen in der Datei geloggt
 */


/*
 * Startdatum in der Form yyyy-mm-dd, zu dem die erste csv-Datei mit Erträgen vorliegt
 */
define('START_DATUM', '2011-12-01');

/*
 * Pfad zu den SunnyExplorer-Dateien:
 * lokales Verzeichnis oder URL ohne abschließenden Slash oder Backslash
 * z.B. C:/daten/SunnyExplorer
 */
define("SEXPLORER_DATA_PATH", "../SunnyExplorer");

/*
 * Pfad, in dem die Solarlog-Dateien abgelegt werden ohne abschließenden Slash oder Backslash
 * z.B. C:/SolarLog
 */
define('SLFILE_DATA_PATH', '../SolarLog');

/*
 * Name der Anlage, aus dem der Name der csv-Datei gebildet wird
 * ein csv-Dateiname besteht aus Anlagenname+Datum+.csv
 */
define('CSV_ANLAGEN_NAME', 'Anlage1-');

/*
 * Kopfzeile in der täglich angelegten csv-Datei ab der die Daten beginnen
 */
define('CSV_HEAD_LINE_DAILY', 'dd.MM.yyyy HH:mm;kWh;kW');

/*
 * Kopfzeile in der monatlich angelegten csv-Datei ab der die Daten beginnen
 */
define('CSV_HEAD_LINE_MONTHLY', 'dd.MM.yyyy;kWh;kWh');

/*
 * Formate von Tag,Monat,Jahr,Stunde und Minute in den Kopfzeilen
 */
define('CSV_HEAD_DAY', 'dd');
define('CSV_HEAD_MONTH', 'MM');
define('CSV_HEAD_YEAR', 'yyyy');
define('CSV_HEAD_HOUR', 'HH');
define('CSV_HEAD_MINUTE', 'mm');

/*
 * Pfad und name einer lokalen Datei, in der Fehlermeldungen geloggt werden
 * z.B. C:/Php-Fehlermeldungen/scriptfehler.txt
 */
define("ERROR_LOG_FILE", "SolarLog/errors.txt");

/*
 * Trennzeichen in den zu konvertierenden csv-dateien
 */
define('CSV_DELIMITER', ';')
?>



