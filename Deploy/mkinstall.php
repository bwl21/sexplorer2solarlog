<?php

// $Id: mkinstall.php eee27f94be84 2012/02/25 15:17:31 Bernhard $


// $Date: 2012/02/25 15:17:31 $
// $Id: mkinstall.php eee27f94be84 2012/02/25 15:17:31 Bernhard $
// $LTag: Version-03 $
// $LocalRevision: 104 $
// $RCSfile: mkinstall.php $
// $Revision: eee27f94be84 $
// $Source: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall.php $


$version = $argv[1];
system("hg update ".$version);



system('zip -r -p Releases/SExplore2Slog-'.$version.'.zip index.php config.inc.php_example readme.txt base_vars.js_example Classes localFiles');
?>
