<?php

// $Id: mkinstall.php 405fe2807fa2 2012/01/22 18:22:55 Bernhard $

// $Author: Bernhard $
// $Date: 2012/01/22 18:22:55 $
// $Header: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall.php,v 405fe2807fa2 2012/01/22 18:22:55 Bernhard $
// $Id: mkinstall.php 405fe2807fa2 2012/01/22 18:22:55 Bernhard $
// $LTag: Version-01 $
// $LocalRevision: 65 $
// $RCSfile: mkinstall.php $
// $Revision: 405fe2807fa2 $
// $Source: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall.php $


$version = $argv[1];
system("hg update ".$version);


system('zip -r -p Deploy/SExplore2Slog-'.$version.'.zip index.php conf.inc.php_example Classes readme.txt');
?>
