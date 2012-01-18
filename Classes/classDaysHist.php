<?php

include_once 'config.inc.php';

/**
 * verwaltet die Solarlog-Datei days_hist.js
 * @version 0.5
 * @author PhotonenSammler <photonensammler@freenet.de>
 */
class classDaysHist extends classSLDataFile{

	const days_hist='days_hist.js'; //Dateiname der days_hist.js
	const kennung='da[dx++]';

	function __construct() {
		parent::__construct(SLFILE_DATA_PATH.'/'.self::days_hist,self::kennung);
	}



}

?>
