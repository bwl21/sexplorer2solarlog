<?php

// $Id: mkinstall2.php fe085cd71ab1 2012/05/07 19:37:47 Bernhard $


// $Date: 2012/05/07 19:37:47 $
// $Id: mkinstall2.php fe085cd71ab1 2012/05/07 19:37:47 Bernhard $
// $LTag: Version-04 $
// $LocalRevision: 149 $
// $RCSfile: mkinstall2.php $
// $Revision: fe085cd71ab1 $
// $Source: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall2.php $


$version = $argv[1];
system("hg update ".$version);

$curdir = getcwd();

chdir ('SExplorer2Solarlog2.0/SExplorer2SolarLog');


system('zip -r -p ../../Releases/inverterData2SolarLog-'.$version.'.zip configExamples Classes eradme.txt');
?>
