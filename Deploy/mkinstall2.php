<?php

// $Id: mkinstall2.php e56f31998f01 2012/06/22 09:08:00 Bernhard $


// $Date: 2012/06/22 09:08:00 $
// $Id: mkinstall2.php e56f31998f01 2012/06/22 09:08:00 Bernhard $
// $LTag: Version-04 $
// $LocalRevision: 162 $
// $RCSfile: mkinstall2.php $
// $Revision: e56f31998f01 $
// $Source: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall2.php $


$version = $argv[1];
system("hg update ".$version);

$curdir = getcwd();
$pathinfo = pathinfo(__FILE__ );
$rootdir = realpath($pathinfo['dirname'] . "/../");


echo "---\n" ;
echo $rootdir, "\n" ;
echo "---\n" ;

chdir($rootdir . "/InverterData2SolarLog");


system('zip -r -p ../Releases/InverterData2SolarLog-'.$version.'.zip configExamples Classes readme.txt');
?>
