<?php

// $Id: mkinstall.php 7fea35d5bf2f 2012/01/30 21:21:29 Bernhard $

// $Author: Bernhard $
// $Date: 2012/01/30 21:21:29 $
// $Header: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall.php,v 7fea35d5bf2f 2012/01/30 21:21:29 Bernhard $
// $Id: mkinstall.php 7fea35d5bf2f 2012/01/30 21:21:29 Bernhard $
// $LTag: Version-02 $
// $LocalRevision: 95 $
// $RCSfile: mkinstall.php $
// $Revision: 7fea35d5bf2f $
// $Source: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall.php $


$version = $argv[1];
system("hg update ".$version);


system('zip -r -p Deploy/SExplore2Slog-'.$version.'.zip index.php config.inc.php_example Classes readme.txt base_vars.js_example');
?>
