<?php
var_dump($argv);
$version = $argv[1];
system("hg update ".$version);
system('zip -r -p Deploy/SExplore2Slog-'.$version.'.zip *.php Classes readme.txt');
?>