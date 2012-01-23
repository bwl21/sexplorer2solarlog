<?php

/*
 * © PhotonenSammler <photonensammler@freenet.de> <http://www.photonensammler.eu>
 */
include_once 'config.inc.php';

/**
 * erzeugt die SL-Datei days.js - die Datei dient zur Anzeige des aktuellen Tages in der Monatsansicht der SL-Homepage
 *
 * @author PhotonenSammler <photonensammler@freenet.de>
 */
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
	public function __construct($datum,$data){
		$filename=realpath(SLFILE_DATA_PATH) . '/' . self::days;
		if($fp=@fopen($filename,'wb')){
			$line=self::kennung.'="'.$datum;
			foreach($data as $value){
				$line.='|'.$value[classSExplorerData::etag].';'.$value[classSExplorerData::p];
			}
			$line.='"'.chr(13);
			if(!@fwrite($fp,$line)){
				classErrorLog::LogError('Fehler beim Schreiben in die Datei '.$filename);
			}
			@fclose($fp);
		}else{
			classErrorLog::LogError('Fehler beim Öffnen der Datei '.$filename);
		}
	}
}

?>
