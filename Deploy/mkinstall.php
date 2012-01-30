<?php

// $Id: mkinstall.php 85e2422f3df6 2012/01/30 23:07:10 Bernhard $

// $Author: Bernhard $
// $Date: 2012/01/30 23:07:10 $
// $Header: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall.php,v 85e2422f3df6 2012/01/30 23:07:10 Bernhard $
// $Id: mkinstall.php 85e2422f3df6 2012/01/30 23:07:10 Bernhard $
// $LTag: Version-03 $
// $LocalRevision: 96 $
// $RCSfile: mkinstall.php $
// $Revision: 85e2422f3df6 $
// $Source: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall.php $


$version = $argv[1];
system("hg update ".$version);


system('zip -r -p Deploy/SExplore2Slog-'.$version.'.zip index.php config.inc.php_example readme.txt base_vars.js_example Classes localFiles');
?>
