$Id: readme.txt fe085cd71ab1 2012/05/07 19:37:47 Bernhard $

Installation:
=============

1. create an installation folder on your web server, e.g. /inverterdata2Solarlog

2. create a configuration folder in <installationFolder> 

3. expand the install-kit in <installationFolder> 

4. determine/create folders for the inverterdata and the solarlog files.

4. create the configuration files config.general.php, config.<inverter>.php, config.solarlog.php; 
   examples for such files is in configExamples 

5. create the main program in <installationFolder> according to the example
   in configExamples/index_example.php. Thereby include the configuration files
   created in Step 4.
   
   don't forget the line 
   require_once 'Classes/update.php';





Change History
==============

Version-04: 2012-02-25

 * bugfixes in multi-inverte support

Version-03: 31.01.2012
 
 * also put Pmax to days.js and days_hist.js
 * create min_cur.js
 * improved handling of timezone

Version-02: 23.01.2012

 * now also create minYYMMDD.js
 * now support two inverters
 * Now creates all SolarLog files
 * bugfixes

Version-01: 10.01.2012

 * initial version

 