<?php

/**
 * Klasse zur Verwaltung/Erzeugung der months.js Datei des Solarlog
 *
 * @version 0.1
 * @author PhotonenSammler <photonensammler@freenet.de>
 */

include_once 'config.inc.php';

class classMonths extends classSLDataFile{

	const months='months.js'; //Dateiname der months.js
	const kennung='mo[mx++]';

	function __construct() {
		parent::__construct(SLFILE_DATA_PATH.'/'.self::months.js,self::kennung);
	}




}

?>
