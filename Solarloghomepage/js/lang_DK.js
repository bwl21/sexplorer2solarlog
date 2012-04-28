//Javascript-Ressource-Bundle - DANISH v1

var rb = new Array(
"ydelse",  // 0-LBL_ERTRAG
"Udc",     // 1-LBL_UDC
"kWp",     // 2-LBL_KWP
"ydelses kurve",     // 3-LBL_ERTRAGSLINIE
"indgangs sp&aelig;nding", // 4-LBL_EINGANGSSPANNUNG
"Y-skala i<br>specifik effekt", // 5-LBL_SPEZLSTG
"&deg;C",   // 6-LBL_GRADC
"temperatur<br>indeni inverter",    // 7-LBL_ITEMP
"ALLE", // 8-LBL_ALLEWR
"INV",   // 9-LBL_WR ("WR=Wechselrichter")
"generator effekt",    // 10-LBL_GENLSTG
"fra hele systemet",  // 11-LBL_PRZT_GESLSTG
"kun inverter",  // 12-LBL_ALLEWR2
"total generator effekt", // 13-LBL_GESLSTG
"overblik dagligt",  // 14-LBL_TAGHEADER
"overblik m&aring;nedligt", // 15-LBL_MONATHEADER
"overblik &aring;rligt", // 16-LBL_JAHRHEADER
"overblik alle &aring;r", // 17-LBL_GESAMTHEADER
"dag",  // 18-LBL_TAG
"m&aring;ned",    // 19-LBL_MONAT
"&aring;r", // 20-LBL_JAHR
"total",   // 21-LBL_GESAMT
"Januar","Februar","Marts","April","Maj","Juni","Juli","August","September","Oktober","November","December",
"daglig sat v&aelig;rdi",    // 34-LBL_TAGESSOLL
"prognose",     // 35-LBL_PROGNOSE
"sat v&aelig;rdi", // 36-LBL_SOLL
"nuv&aelig;rende", // 37-LBL_MOMENTAN
"f&oslash;de effekt Pac",    // 38-LBL_EINSPEISELSTG
"generator",    // 39-LBL_GENERATOR
"inverter effektivitet &eta;",    // 40-LBL_WG
"specifik ydelse",    // 41-LBL_ERTRAG_SPEZ
"status",    // 42-LBL_STATUS
"maximal v&aelig;rdi",    // 43-LBL_MAXIMALWERT
"fejl",    // 44-LBL_FEHLER
"kumulativ",    // 45-LBL_AUFLAUFEND
"aktuel", // 46-LBL_IST
"sparet CO<sub>2</sub>-udledning totalt", // 47-LBL_CO2
"info system", // 48-LBL_ANLAGENINFO
"overblik total", // 49-LBL_GESAMTHEADER2
"PDA visning", // 50-LBL_PDA
"h&aelig;ndelses liste", // 51-LBL_EREIGNIS
"styret af", // 52-LBL_POWEREDBY
"h&aelig;ndelse fra", // 53-LBL_EVENTVON
"h&aelig;ndelse til", // 54-LBL_EVENTBIS
"lokation", // 55-LBL_STANDORT
"moduler", // 56-LBL_MODULE
"inverter", // 57-LBL_WECHSELRICHTER
"installeret effekt", // 58-LBL_LEISTUNG
"ibrugtagning", // 59-LBL_INBETRIEB
"orientering", // 60-LBL_AUSRICHTUNG
"system overv&aring;gning", // 61-LBL_ANLAGEN
"system operat&oslash;r", // 62-LBL_BETRIEBER
"jan,feb,mar,apr,maj,jun,jul,aug,sep,okt,nov,dec,", // 63-LBL_MONKURZ
"online data fra", // 64-LBL_ONLINEVON
"nuv&aelig;rende", // 65-LBL_AKTUELL
"idag", // 66-LBL_HEUTE
"INV &eta;", // 67-LBL_WGKURZ
"spec", // 68-LBL_SPEZ  (aus PALM: "Espez")
"Values", // 69-LBL_VISU_WERTE
"Show values", // 70-LBL_VISU_WERTE_ANZEIGEN
"Compare to past", // 71-LBL_VERGLEICHHEADER
"years", // 72-LBL_VERGLEICHHEADER_JAHRE
"Daily values:", // 73-LBL_VISU_TAGESWERTE
"Date", // 74-LBL_VISU_DATUM
"Yield total", // 75-LBL_TOP12_ERTRAG_ABSOLUT
"Yield specific", // 76-LBL_TOP12_ERTRAG_SPEZIFIFISCH
"Yield target", // 77-LBL_TOP12_ERTRAG_SOLL
"Yield/Target", // 78-LBL_VISU_ERTRAG_SOLL_IST
"Sum", //79-LBL_VISU_SUMME
"Monthly values:", // 80-LBL_VISU_MONATSWERTE
"Yearly values:", // 81-LBL_VISU_JAHRESWERTE
"S0 ", // 82-LBL_S0  //S0-IN
//"Alle Jahre<br>Liniendiagramm", // 83-LBL_VISU_GESAMT_LINIE
"Line overview years", // 83-LBL_VISU_GESAMT_LINIE
//"Alle Jahre<br>Balkendiagramm", // 84-LBL_VISU_GESAMT_BALKEN
"Chart overview years", // 84-LBL_VISU_GESAMT_BALKEN
"Trend in first year", // 85-LBL_VERLAUFHEADER
"Events starting - until", // 86-LBL_EVENT_VONBIS
"Hour", // 87-LBL_HOUR
"Solar", // 88-LBL_IRR ("Solar-Irradiation)
"Mod-T", // 89-LBL_MODULTEMP
"Env-T", // 90-AMBTEMP
"Wind", // 91-WIND
"Solar irradiation sensor", // 92-LBL_IRR2 ("Solar-Irradiation)
"Module temperature", // 93-LBL_MODULTEMP2
"Outside temperature", // 94-AMBTEMP2
"Wind speed", // 95-WIND2
"String overview", // 96-LBL_STRINGHEADER
"Balance", //97-LBL_FLAG_EEG
"Energy balance with own consumption", // 98-LBL_FLAG_EEG_BEZ
"Energy balance with own consumption calculation", // 99-LBL_EEG_BILANZ
"Own consumption rate", // 100-LBL_EEG_QUOTE
"All", // 101-LBL_ALLE
"days", // 102-LBL_TAGE
"Plant groups", // 103-LBL_GRUPPEN
"Status", // 104-LBL_STATI
"Plant group", // 105-LBL_ANLAGENGRP
"Event from - until", // 106-LBL_EVENT_VONBIS
"Reset selection", // 107-LBL_CLEARLIST
" additional lines available, but too many data", // 108-LBL_MOREROWS
"Total consumption.", // 109-LBL_VERBRAUCH_GES
"Produced at same time", // 110-LBL_Z_ERZEUGT
"24-hour-scale", // 111-LBL_24H_SCALE
"No data available", // 112-LBL_NO_DATA
"Analog", // 113-LBL_ANALOG
"Digital", // 114-LBL_DIGITAL
"Varistor", // 115-LBL_STATUS_VARISTOR
"Isolation", // 116-LBL_STATUS_VARISTOR
"SCB", // 117-LBL_SCB ("String Connection Box")
"Current data", // 118-LBL_CURRENT_DATA
"String", // 119-LBL_STRING
"0=String not connected,1=String connected and generating, 2=String connected and not generating, 3=String current out of range, 4=Alarm of positive pole fuse, 5=Alarm of negative pole fuse", // 120-LBL_SCB_SIAC_LEGEND
"Sensor for Module field: ", // 121-LBL_SENSOR_FOR_MODULFIELD
"Check Modul field configuration! ", // 122-LBL_CONSISTENCY_MODULFIELD
"Total Current", // 123-LBL_SUM_IDC
"Total Voltage",// 124-LBL_SUM_UDC
"Alarm Line",//125-LBL_ALARM_LINE
"Release",//126-LBL_RELEASE
"States",//127-LBL_STATES
"Door",//128-LBL_DOOR
"Overvoltage",//129-LBL_OVERVOLTAGE
"Inside temperature",//130-LBL_TMP_INSIDE
"Irradiation",//131-LBL_IRRADIATION
"0=Invalid, 1= Short-circuit, 2=Alarm1, 3=Normal, 4=Alarm2, 5=Break",//132-LBL_SCB_KNB_LEGEND1
"1,3,5=exact, 2,4=diffuse",//133-LBL_SCB_KNB_LEGEND2
"Sensorbox",//134-LBL_SENSOR_ON_KNB
"Deactivated",//135-LBL_WLAN_DEACTIVATED
"Initializing",//136-LBL_WLAN_DEACTIVATED
"Not connected",//137-LBL_WLAN_DEACTIVATED
"Connected",//138-LBL_WLAN_DEACTIVATED
"Connecting...",//139-LBL_WLAN_DEACTIVATED
"SL Offline",//140-LBL_WLAN_DEACTIVATED
"Reading configuration...",//141-LBL_WLAN_READCONFIG
"Reading data...",//142-LBL_WLAN_READDATA
"Searching for Networks...",//143-LBL_WLAN_SEARCHNW
"WPS-Pushbutton Configuration<br>Please activate WPS on your WIFI router now.",//144-LBL_WLAN_PUSHPROGRESS
" ... refreshing",//145-LBL_WLAN_REFRESHING
"String-Power not configured!",//146-LBL_STRING_POWER_UNDEFINED
"Overall yield meter", //147-LBL_COUNTER_YIELD
"")

var LBL_COUNTER_YIELD=147;
var LBL_STRING_POWER_UNDEFINED=146;
var LBL_WLAN_REFRESHING=145;
var LBL_WLAN_PUSHPROGRESS=144;
var LBL_WLAN_SEARCHNW=143;
var LBL_WLAN_READDATA=142;
var LBL_WLAN_READCONFIG=141;
var LBL_WLAN_SLOFFLINE=140;
var LBL_WLAN_CONNECTING=139;
var LBL_WLAN_CONNECTED=138;
var LBL_WLAN_NOTCONNECTED=137;
var LBL_WLAN_INITIALIZING=136;
var LBL_WLAN_DEACTIVATED=135;

var LBL_SENSOR_ON_KNB=134;
var LBL_SCB_KNB_LEGEND2=133;
var LBL_SCB_KNB_LEGEND1=132;
var LBL_IRRADIATION=131;
var LBL_TMP_INSIDE=130;
var LBL_OVERVOLTAGE=129;
var LBL_DOOR=128;
var LBL_STATES=127;
var LBL_RELEASE=126;
var LBL_ALARM_LINE=125;
var LBL_SUM_UDC=124;
var LBL_SUM_IDC=123;
var LBL_CONSISTENCY_MODULFIELD = 122;
var LBL_SENSOR_FOR_MODULFIELD = 121;
var LBL_SCB_SIAC_LEGEND = 120;
var LBL_STRING = 119;
var LBL_CURRENT_DATA = 118;
var LBL_SCB = 117;
var LBL_ISOLATION = 116;
var LBL_VARISTOR = 115;
var LBL_DIGITAL = 114;
var LBL_ANALOG = 113;
var LBL_NO_DATA = 112;
var LBL_24H_SCALE = 111;
var LBL_Z_ERZEUGT = 110;
var LBL_VERBRAUCH_GES = 109;
var LBL_MOREROWS = 108;
var LBL_CLEARLIST = 107;
var LBL_EVENT_VONBIS_1 = 106;
var LBL_ANLAGENGRP = 105;
var LBL_STATI = 104;
var LBL_GRUPPEN = 103;
var LBL_TAGE = 102;
var LBL_ALLE = 101;

var LBL_EEG_QUOTE = 100;
var LBL_EEG_BILANZ = 99;
var LBL_FLAG_EEG_BEZ = 98;
var LBL_FLAG_EEG = 97;
var LBL_STRINGHEADER = 96;
var LBL_WIND2 = 95
var LBL_AMBTEMP2 = 94
var LBL_MODULTEMP2 = 93
var LBL_IRR2 = 92
var LBL_WIND = 91
var LBL_AMBTEMP = 90
var LBL_MODULTEMP = 89
var LBL_IRR = 88
var LBL_HOUR = 87
var LBL_EVENT_VONBIS = 86
var LBL_VERLAUFHEADER = 85
var LBL_VISU_GESAMT_BALKEN = 84
var LBL_VISU_GESAMT_LINIE = 83
var LBL_S0 = 82
var LBL_VISU_JAHRESWERTE = 81
var LBL_VISU_MONATSWERTE = 80
var LBL_VISU_SUMME = 79
var LBL_VISU_ERTRAG_SOLL_IST = 78
var LBL_TOP12_ERTRAG_SOLL = 77
var LBL_TOP12_ERTRAG_SPEZIFISCH = 76
var LBL_TOP12_ERTRAG_ABSOLUT = 75
var LBL_VISU_DATUM = 74
var LBL_VISU_TAGESWERTE = 73
var LBL_VERGLEICHHEADER_JAHRE = 72
var LBL_VERGLEICHHEADER = 71
var LBL_VISU_WERTE_ANZEIGEN = 70
var LBL_VISU_WERTE = 69
var LBL_SPEZ = 68
var LBL_WGKURZ = 67
var LBL_HEUTE = 66
var LBL_AKTUELL = 65
var LBL_ONLINEVON = 64
var LBL_MONKURZ = 63
var LBL_BETRIEBER = 62
var LBL_ANLAGEN = 61
var LBL_AUSRICHTUNG = 60
var LBL_INBETRIEB = 59
var LBL_LEISTUNG = 58
var LBL_WECHSELRICHTER = 57
var LBL_MODULE = 56
var LBL_STANDORT = 55
var LBL_EVENTBIS = 54
var LBL_EVENTVON = 53
var LBL_POWEREDBY = 52
var LBL_EREIGNIS = 51
var LBL_PDA = 50
var LBL_GESAMTHEADER2 = 49
var LBL_ANLAGENINFO = 48
var LBL_CO2 = 47
var LBL_IST = 46
var LBL_AUFLAUFEND = 45
var LBL_FEHLER = 44
var LBL_MAXIMALWERT = 43
var LBL_STATUS = 42
var LBL_ERTRAG_SPEZ = 41
var LBL_WG = 40
var LBL_GENERATOR = 39
var LBL_EINSPEISELSTG = 38
var LBL_MOMENTAN = 37

var LBL_ERTRAG  =0
var LBL_UDC     =1
var LBL_KWP     =2
var LBL_ERTRAGSLINIE     =3
var LBL_EINGANGSSPANNUNG   =4
var LBL_SPEZLSTG     =5
var LBL_GRADC     =6
var LBL_ITEMP     =7
var LBL_ALLEWR     =8
var LBL_WR     =9
var LBL_GENLSTG     =10
var LBL_PRZT_GESLSTG     =11
var LBL_ALLEWR2     =12
var LBL_GESLSTG     =13
var LBL_TAGHEADER     =14
var LBL_MONATHEADER     =15
var LBL_JAHRHEADER     =16
var LBL_GESAMTHEADER     =17
var LBL_TAG     =18
var LBL_MONAT     =19
var LBL_JAHR     =20
var LBL_GESAMT     =21
var LBL_MON01 = 22
var LBL_MON02 = 23
var LBL_MON03 = 24
var LBL_MON04 = 25
var LBL_MON05 = 26
var LBL_MON06 = 27
var LBL_MON07 = 28
var LBL_MON08 = 29
var LBL_MON09 = 30
var LBL_MON10 = 31
var LBL_MON11 = 32
var LBL_MON12 = 33
var LBL_TAGESSOLL = 34
var LBL_PROGNOSE = 35
var LBL_SOLL = 36

//***********************************
function getText(index) {
return rb[index]
}