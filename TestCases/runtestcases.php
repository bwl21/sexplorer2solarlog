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

$testcases = array("01_SunnyExplorer_ein-wr",
                  );
                  
                  
foreach($testcases as $testcase) {
    perform($testcase);
}

?>

