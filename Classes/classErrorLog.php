<?php

/*
 * © PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 */

include_once 'config.inc.php';

/**
 * @version 0.3
 * @author PhotonenSammler <photonensammler@freenet.de>
 */
class classErrorLog {

	/**
	 * @version 0.3
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
