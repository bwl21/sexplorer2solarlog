<?php

/**
 * Beschreibung von $RCSfile: classErrorLog.php $
 *
 * Klasse zum Error-Logging
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 *
 * © PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * 
 *
 * $Date: 2012/01/28 18:08:16 $
 * $Id: classErrorLog.php fa10176932de 2012/01/28 18:08:16 Bernhard $
 * $LocalRevision: 89 $
 * $Revision: fa10176932de $
 */



include_once 'config.inc.php';


class classErrorLog {

	/**
	 * @param string||array $msg
	 */
	static function LogError($msg) {
		if (!($fp = @fopen(ERROR_LOG_FILE, 'ab'))) {
			echo('Fehler beim Öffnen der Datei ' . ERROR_LOG_FILE . '<br>');
			die(1);
		}
		if (is_array($msg)) {
			$msg = implode(chr(10) . chr(13), $msg);
		}
		if (!fwrite($fp, $msg . chr(13))) {
			echo('Fehler beim Schreiben in die Datei ' . ERROR_LOG_FILE . '<br>');
			die(2);
		}
		fclose($fp);
	}

}

?>
