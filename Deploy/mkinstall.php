<?php

// $Id: mkinstall.php 908d08dfe8e4 2012/01/22 18:47:01 Bernhard $

// $Author: Bernhard $
// $Date: 2012/01/22 18:47:01 $
// $Header: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall.php,v 908d08dfe8e4 2012/01/22 18:47:01 Bernhard $
// $Id: mkinstall.php 908d08dfe8e4 2012/01/22 18:47:01 Bernhard $
// $LTag: Version-01 $
// $LocalRevision: 70 $
// $RCSfile: mkinstall.php $
// $Revision: 908d08dfe8e4 $
// $Source: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall.php $


$version = $argv[1];
system("hg update ".$version);


system('zip -r -p Deploy/SExplore2Slog-'.$version.'.zip index.php config.inc.php_example Classes readme.txt');
?>
