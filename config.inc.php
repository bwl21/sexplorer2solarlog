<?php

/*
 * © PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * 2012-07-01
 *
 * Konvertierung von SunnyExplorer-Dateien in das Solarlog-Datenformat
 */

/* allgemeine Hinweise
 *
 * Rückgabeparameter beim Ausführen z.B. als cmd-Prozess in Windows
 * und kritische Fehler, die zum Programmabbruch führen
 * 0 -> alles fehlerfrei abgearbeitet, eventuelle Fehler wurden in Datei geloggt
 * 1 -> Die Datei zum Logging der Fehlermeldungen kann nicht geöffnet werden
 * 2 -> In die Datei zum Logging der Fehlermeldungen kann nicht geschrieben werden
 * 3 -> Fehler im konstruktor (siehe in Error-Logdatei)
 * 4 -> Fehler im konstruktor (siehe in Error-Logdatei)
 * 5 -> Fehler im konstruktor (siehe in Error-Logdatei)
 * Ansonsten werden alle Fehlermeldungen in der Error-Datei geloggt
 */


/*
 * Startdatum in der Form yyyy-mm-dd, zu dem die erste csv-Datei mit Erträgen vorliegt
 */
define('START_DATUM', '2011-12-01');

/*
 * Name der Anlage zur Bildung des Dateinamens
 */
define('CSV_ANLAGEN_NAME', 'Anlage1');

/*
 * Pfad zu den SunnyExplorer-Dateien:
 * lokales Verzeichnis oder URL ohne abschließenden Slash oder Backslash
 * z.B. C:/daten/SunnyExplorer
 */
define("SEXPLORER_DATA_PATH", "http://www.weichel21.de/SunnyExplorer");

/*
 * Pfad, in dem die Solarlog-Dateien abgelegt werden ohne abschließenden Slash oder Backslash
 * z.B. C:/SolarLog
 */
define('SLFILE_DATA_PATH', 'SLFiles');

/*
 * Trennzeichen zwischen den einzelnen Werten in den csv-dateien
 */
define('CSV_DELIMITER', ';');

/*
 * Verwendeter Dezimalpunkt in den numerischen Werten in den csv-dateien
 */
define('CSV_DECIMALPOINT', ',');

/*
 * Anzahl von Wechselrichtern, deren Daten in den csv-Dateien gespeichert sind
 */
define('CSV_ANZWR', 1);

/*
 * Spalte, in der in der Tagesdatei der Gesamtertrag steht
 * Die Spaltenzählung beginnt bei 1 mit der Spalte Datum
 * für 2 WR könnte der Eintrag folgendermaßen aussehen
 * define('CSV_DAILY_YIELDSUM_COLUMN','2,4'));
 */
define('CSV_DAILY_YIELDSUM_COLUMN', '2');

/*
 * Spalte, in der in der Tagesdatei die Leistung steht
 * Die Spaltenzählung beginnt bei 1 mit der Spalte Datum
 * für 2 WR könnte der Eintrag folgendermaßen aussehen
 * define('CSV_DAILY_POWER_COLUMN','3,5');
 */
define('CSV_DAILY_POWER_COLUMN', '3');

/*
 * Spalte, in der in der Monatsdatei der Gesamtertrag steht
 * Die Spaltenzählung beginnt bei 1 mit der Spalte Datum
 * für 2 WR könnte der Eintrag folgendermaßen aussehen
 * define('CSV_MONTHLY_MONTHSUM_COLUMN','2,4');
 */
define('CSV_MONTHLY_MONTHSUM_COLUMN', '2');

/*
 * Spalte, in der in der Monatsdatei der Tagesertrag steht
 * Die Spaltenzählung beginnt bei 1 mit der Spalte Datum
 * für 2 WR könnte der Eintrag folgendermaßen aussehen
 * define('CSV_MONTHLY_DAYSUM_COLUMN','3,5');
 */
define('CSV_MONTHLY_DAYSUM_COLUMN', '3');

/*
 * Kopfzeile in der täglich angelegten csv-Datei ab der die Daten beginnen
 * diese Definition wird nur benötigt, weil ich nicht weiß, ob international
 * vielleicht z.B. yy/M/D H:mm verwendet wird
 */
define('CSV_HEAD_LINE_DAILY', 'dd.MM.yyyy HH:mm;kWh;kW');

/*
 * Kopfzeile in der monatlich angelegten csv-Datei ab der die Daten beginnen
 * diese Definition wird nur benötigt, weil ich nicht weiß, ob international
 * vielleicht z.B. yy/M/D verwendet wird
 */
define('CSV_HEAD_LINE_MONTHLY', 'dd.MM.yyyy;kWh;kWh');

/*
 * Formate von Tag,Monat,Jahr,Stunde und Minute in den Kopfzeilen
 * aus der Zeile z.B. dd.MM.yyyy HH:mm;kWh;kW
 * Achtung - Groß- und Kleinschreibung beachten !!
 * diese Definition wird nur benötigt, weil ich nicht weiß, ob international
 * vielleicht ein anderes Format verwendet wird
 */
define('CSV_HEAD_DAY', 'dd'); //Bezeichnung für den Tag
define('CSV_HEAD_MONTH', 'MM'); //Bezeichnung für den Monat
define('CSV_HEAD_YEAR', 'yyyy'); //Bezeichnug füe Jahr
define('CSV_HEAD_HOUR', 'HH'); //Bezeichnung für Stunde
define('CSV_HEAD_MINUTE', 'mm'); //Bezeichnung für Minute

/*
 * Pfad und name einer lokalen Datei, in der Fehlermeldungen geloggt werden
 * z.B. C:/Php-Fehlermeldungen/scriptfehler.txt
 */
define("ERROR_LOG_FILE", "errors\errors.txt");
?>



