<?php

/**
 * Beschreibung von $RCSfile: classDays.php $
 *
 * erzeugt die SL-Datei days.js - die Datei dient zur Anzeige des aktuellen Tages in der Monatsansicht der SL-Homepage
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 *
 * Copyright 2012 PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * 
 *
 * $Date: 2012/01/28 18:48:21 $
 * $Id: classDays.php 6e4ce5cea10f 2012/01/28 18:48:21 Bernhard $
 * $LocalRevision: 90 $
 * $Revision: 6e4ce5cea10f $
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


class classDays {

	const days = 'days.js'; //Dateiname der days.js
	const kennung = 'da[dx++]';

	/**
	 * schreibt die Datei days.js
	 * übergeben wird das Datum in der Form DD.MM.YY und ein Array mit einem Eintrag für jeden WR mit Etag und PACmax
	 *
	 * @param string $datum
	 * @param array $data
	 */
	public function __construct($datum, $data) {
		$filename = realpath(SLFILE_DATA_PATH) . '/' . self::days;
		if ($fp = @fopen($filename, 'wb')) {
			$line = self::kennung . '="' . $datum;
			foreach ($data as $value) {
				$line.='|' . $value[classSExplorerData::etag] . ';' . $value[classSExplorerData::p];
			}
			$line.='"' . chr(13);
			if (!@fwrite($fp, $line)) {
				trigger_error('Fehler beim Schreiben in die Datei ' . $filename);
			}
			@fclose($fp);
		} else {
			trigger_error('Fehler beim Öffnen der Datei ' . $filename);
		}
	}

}

?>
