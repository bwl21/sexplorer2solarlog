<?php

// $Id: runtestcases.php e56f31998f01 2012/06/22 09:08:00 Bernhard $


// $Date: 2012/06/22 09:08:00 $
// $Id: runtestcases.php e56f31998f01 2012/06/22 09:08:00 Bernhard $
// $LTag: Version-04 $
// $LocalRevision: 162 $
// $RCSfile: runtestcases.php $
// $Revision: e56f31998f01 $
// $Source: /Users/beweiche/02_svn-sandbox/SExplore2Slog/TestCases/runtestcases.php $



function perform($case){
    
    echo "\n--------------------------------------------" ,
         "\nperforming " . $case ,
         "\n";
    
    chdir($case);
    system("rm 02_SolarLog/*");
    system('php index.php');
    chdir("..");
    }

$testcases = array("01_SunnyExplorer_ein-wr",                   "02_SunnyExplorer_zwei-wr",                   "03_Danfoss",                   "04_SunnyExplorer_ein-wr_ftp",                   "05_SunnyExplorer_zwei-wr_ftp"
                  );
                  
                  
foreach($testcases as $testcase) {
    perform($testcase);
}

?>


