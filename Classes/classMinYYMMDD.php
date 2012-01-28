<?php

/**
 * Beschreibung von $RCSfile: classMinYYMMDD.php $
 *
 * Klasse zur Verwaltung/Erzeugung von minYYMMDD.js files des Solarlog
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 *
 * © PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 * 
 *
 * $Date: 2012/01/28 18:08:16 $
 * $Id: classMinYYMMDD.php fa10176932de 2012/01/28 18:08:16 Bernhard $
 * $LocalRevision: 89 $
 * $Revision: fa10176932de $
 */


include_once 'config.inc.php';

class classMinYYMMDD extends classSLDataFile {

	const kennung = 'm[mi++]';

	/**
	 * Das Datum kann in der Form DD.MM.YY oder YYYY-MM-DD oder als Timestamp übergeben werden
	 *
	 * @param string|timestamp $datum
	 */
	function __construct($datum) {
		if (is_integer($datum)) { //Timestamp
			$filename = date('ymd', $datum);
		} elseif (preg_match('/^\d{2}\.\d{2}\.\d{2}/', $datum)) { //Form DD.MM.YY
			$fn = explode('.', $datum);
			$filename = $fn[2] . $fn[1] . $fn[0];
			unset($fn);
		} elseif (preg_match('/^\d{4}-\d{2}-\d{2}/', $datum)) { //Form YYYY-MM-DD
			$fn = explode('-', $datum);
			$filename = substr($fn[0], 2, 2) . $fn[1] . $fn[2];
			unset($fn);
		} else { //ungültiges Format
			trigger_error('Ungültiges Datumsformat ' . $datum . ' wurde übergeben');
		}
		parent::__construct(realpath(SLFILE_DATA_PATH) . '/min' . $filename . '.js', self::kennung);
	}

}

?>
