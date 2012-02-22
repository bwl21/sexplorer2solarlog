<?php

/*
 * © PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * 2012-07-01
 *
 * Konvertierung von SunnyExplorer-Dateien in das Solarlog-Datenformat
 * Testdateien unter http://www.weichel21.de/SunnyExplorer
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
 * ###########################################################################
 * ab hier ist die Konfiguration an die Anlage anzupassen
 * ###########################################################################
 */

/*
 * Startdatum in der Form yyyy-mm-dd, zu dem die erste Tages-csv-Datei mit Erträgen vorliegt
 */
define('START_DATUM', '2012-02-16');

/*
 * Name der Anlage zur Bildung des csv-Dateinamens
 */
define('CSV_ANLAGEN_NAME', 'AnlageLog');

/*
 * Pfad zu den SunnyExplorer-Dateien:
 * lokales Verzeichnis ohne abschließenden Slash oder Backslash
 * z.B. C:/daten/SunnyExplorer
 */
define('SEXPLORER_DATA_PATH', '../SExplorerData');

/*
 * Pfad angeben, in dem die Solarlog-Dateien abgelegt werden ohne abschließenden Slash oder Backslash
 * Auf dieses Verzeichnis muss das Script Schreibrechte haben z.B. C:/SolarLog
 */
define('SLFILE_DATA_PATH', '..');

/*
 * Pfad und name einer lokalen Datei, in der Fehlermeldungen geloggt werden
 * z.B. C:/Php-Fehlermeldungen/scriptfehler.txt
 */
define('ERROR_LOG_FILE', 'errors/errors.txt');

/*
 * Anzahl von Wechselrichtern, deren Daten in den csv-Dateien gespeichert sind
 */
define('CSV_ANZWR', 2);

/*
 * ###########################################################################
 * ab hier sind nur in Ausnahmefällen Änderungen der Eintragungen erforderlich
 * ###########################################################################
 */

/*
 * Zeitzone des Anlagenstandorts (siehe http://php.net/manual/en/datetime.configuration.php )
 */
define('TIMEZONE',"Europe/Berlin");

/*
 * Trennzeichen zwischen den einzelnen Werten in den csv-dateien
 */
define('CSV_DELIMITER', ';');

/*
 * Verwendeter Dezimalpunkt in den numerischen Werten in den csv-dateien
 */
define('CSV_DECIMALPOINT', ',');

/*
 * Eintrag muss nur bei mehr als 4 WR oder Änderung des csv-Datenformats geändert/ergänzt werden
 * Spalte, in der in der Tagesdatei der Gesamtertrag steht
 * Die Spaltenzählung beginnt bei 1 mit der Spalte Datum
 * für 2 WR könnte der Eintrag folgendermaßen aussehen
 * define('CSV_DAILY_YIELDSUM_COLUMN','2,4'));
 */
define('CSV_DAILY_YIELDSUM_COLUMN', '2,4,6,8');

/*
 * Eintrag muss nur bei mehr als 4 WR oder Änderung des csv-Datenformats geändert/ergänzt werden
 * Spalte, in der in der Monatsdatei der Tagesertrag steht
 * Die Spaltenzählung beginnt bei 1 mit der Spalte Datum
 * für 2 WR könnte der Eintrag folgendermaßen aussehen
 * define('CSV_MONTHLY_DAYSUM_COLUMN','3,5');
 */
define('CSV_MONTHLY_DAYSUM_COLUMN', '3,5,7,9');

/*
 * Eintrag muss nur bei mehr als 4 WR oder Änderung des csv-Datenformats geändert/ergänzt werden
 * Spalte, in der in der Tagesdatei die Leistung steht
 * Die Spaltenzählung beginnt bei 1 mit der Spalte Datum
 * für 2 WR könnte der Eintrag folgendermaßen aussehen
 * define('CSV_DAILY_POWER_COLUMN','3,5');
 */
define('CSV_DAILY_POWER_COLUMN', '3,5,7,9');

/*
 * Eintrag muss nur bei mehr als 4 WR oder Änderung des csv-Datenformats geändert/ergänzt werden
 * Spalte, in der in der Monatsdatei der Gesamtertrag steht
 * Die Spaltenzählung beginnt bei 1 mit der Spalte Datum
 * für 2 WR könnte der Eintrag folgendermaßen aussehen
 * define('CSV_MONTHLY_MONTHSUM_COLUMN','2,4');
 */
define('CSV_MONTHLY_MONTHSUM_COLUMN', '2,4,6,8');
?>



