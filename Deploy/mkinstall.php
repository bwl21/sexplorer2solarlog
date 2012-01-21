<?php

// $Id: mkinstall.php 5376e40b8d28 2012/01/21 12:27:59 Bernhard $

// $Author: Bernhard $
// $Date: 2012/01/21 12:27:59 $
// $Header: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall.php,v 5376e40b8d28 2012/01/21 12:27:59 Bernhard $
// $Id: mkinstall.php 5376e40b8d28 2012/01/21 12:27:59 Bernhard $
// $LTag: Version-01 $
// $LocalRevision: 55 $
// $RCSfile: mkinstall.php $
// $Revision: 5376e40b8d28 $
// $Source: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall.php $


$version = $argv[1];
system("hg update ".$version);
system('zip -r -p Deploy/SExplore2Slog-'.$version.'.zip *.php Classes readme.txt');
?>