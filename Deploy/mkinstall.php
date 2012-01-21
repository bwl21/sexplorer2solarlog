<?php

// $Id: mkinstall.php 6cd8d5408e37 2012/01/21 19:22:44 WebAdmin $

// $Author: WebAdmin $
// $Date: 2012/01/21 19:22:44 $
// $Header: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall.php,v 6cd8d5408e37 2012/01/21 19:22:44 WebAdmin $
// $Id: mkinstall.php 6cd8d5408e37 2012/01/21 19:22:44 WebAdmin $
// $LTag: Version-01 $
// $LocalRevision: 60 $
// $RCSfile: mkinstall.php $
// $Revision: 6cd8d5408e37 $
// $Source: /Users/beweiche/02_svn-sandbox/SExplore2Slog/Deploy/mkinstall.php $


$version = $argv[1];
system("hg update ".$version);

system('"C:\Program Files (x86)\WinRAR\RAR.EXE" m -r Deploy/SExplore2Slog-'.$version.'.zip'. ' Classes');

system('zip -r -p Deploy/SExplore2Slog-'.$version.'.zip *.php Classes readme.txt');
?>
