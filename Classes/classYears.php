<?php

include_once 'config.inc.php';

/**
 * Beschreibung von classYears
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 */
class classYears extends classSLDataFile{

	const years='years.js'; //Dateiname der years.js
	const kennung='ye[yx++]';

	function __construct() {
		parent::__construct(SLFILE_DATA_PATH.'/'.self::years,self::kennung);
	}

}

?>
