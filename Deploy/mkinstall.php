<?php

// $Id: mkinstall.php dfffcb4bfaa1 2012/01/21 12:03:42 Bernhard $

// $Author: Bernhard $
// $Date: 2012/01/21 12:03:42 $
// $Header: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall.php,v dfffcb4bfaa1 2012/01/21 12:03:42 Bernhard $
// $Id: mkinstall.php dfffcb4bfaa1 2012/01/21 12:03:42 Bernhard $
// $LTag: Version-01 $
// $LocalRevision: 54 $
// $RCSfile: mkinstall.php $
// $Revision: dfffcb4bfaa1 $
// $Source: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall.php $



var_dump($argv);
$version = $argv[1];
system("hg update ".$version);
system('zip -r -p Deploy/SExplore2Slog-'.$version.'.zip *.php Classes readme.txt');
?>