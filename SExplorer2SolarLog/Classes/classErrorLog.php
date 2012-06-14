<?php

/**
 * Beschreibung von $RCSfile$
 *
 * Klasse zum Error-Logging
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 *
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * 
 *
 * $Date$
 * $Id$
 * $LocalRevision$
 * $Revision$
 */

/*

    Diese Datei ist Teil von SExplore2SlLog.

    SExplore2SlLog ist Freie Software: Sie können es unter den Bedingungen
    der GNU General Public License, wie von der Free Software Foundation,
    Version 3 der Lizenz oder jeder späteren veröffentlichten Version, 
    weiterverbreiten und/oder modifizieren.

    FuSExplore2SlLog wird in der Hoffnung, dass es nützlich sein wird, aber
    OHNE JEDE GEWÄHELEISTUNG, bereitgestellt; sogar ohne die implizite
    Gewährleistung der MARKTFÄHIGKEIT oder EIGNUNG FÜR EINEN BESTIMMTEN ZWECK.
    Siehe die GNU General Public License für weitere Details.
    
    <http://www.gnu.org/licenses/>

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
