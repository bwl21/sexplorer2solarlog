// alle Functions
function getEom(dDate) {
var jahr, monat, lDate
jahr=dDate.getFullYear()
monat=dDate.getMonth()
monat++
if( monat>11 ) {
    monat=0
    jahr++
}
lDate=new Date(jahr,monat,1).getTime()
dDate=new Date(lDate-86400000)
return dDate.getDate()
}


function getToken( d, wrI, token ) {
    var pnt1, pnt2;
    pnt1 = 0;
    pnt2 = d.indexOf(token);
    while( wrI>0 ) {
        pnt1 = pnt2+1;
        pnt2 = d.indexOf(token,pnt1);
        wrI--;
        if( pnt2 <= 0 )
            break;
    }
    if( wrI>0 )
        return "";
    if( pnt2<=0 )
        return d.substr(pnt1);
    return d.substr(pnt1,pnt2-pnt1);
}
function getWRToken( d, wrI ) {
    return getToken( d, wrI, "|" )
}
function enumData( d, wrI ) {
    var data=new Array()
    var pnt1, pnt2, s=0
    data[0] = getWRToken( d, 0 );
    d = getWRToken( d, wrI );
    pnt1=0;
    while( true ) {
        pnt2 = d.indexOf(";",pnt1)
        if( pnt2 <= 0 ) {
            data[1+s] = parseInt( d.substr(pnt1),10 );
            break;
        }
        data[1+s] = parseInt( d.substr(pnt1, pnt2-pnt1),10 )
        pnt1 = pnt2+1;
        s++;
    }
    return data
}

function getYDelta( ymax ) {
    var dy=ymax
    var r=1;
    while (Math.abs(dy)>=100) { dy/=10; r*=10; }
    if (Math.abs(dy)>=50) {
        deltay=10*r;
    }
    else {
        if (Math.abs(dy)>=20) {
            deltay=5*r;
        }
        else {
            deltay=2*r;
        }
    }
    return deltay
}

// optimale Y-Skalierung berechnen
function calcYSkal( maxWatt ) {
    var BaseY, deltay
    deltay=getYDelta( maxWatt )
    BaseY=maxWatt/deltay
    if( BaseY != Math.floor(BaseY ) ) {
        BaseY = (Math.floor(BaseY)+1)
        maxWatt = BaseY*deltay
        deltay = maxWatt / BaseY
    }
    else
        maxWatt = BaseY*deltay
    return new Array(maxWatt,deltay)
}


function getGesAnlagenMaxWatt( modus )
{
    var wrI, max
    max=0
    for( wrI=0; wrI<AnzahlWR; wrI++ ) {
        if( typeof WRInfo[wrI][11]=="undefined" || WRInfo[wrI][11]==0 || (WRInfo[wrI][11]==2 && WRInfo[wrI][14]==0) ) {
           max += MaxWRP[wrI][modus]
        }
    }
    return max;
}

function getCurAnlagenKWP() {
    return AnlagenKWP;
}

function fmt00(v)
{
    if( v<10 )
        return "0"+v
    else
        return ""+v
}

function calcStatus() {
var i, i2, f1, f2, cnt=0, found, bez="", bez2="", ret=new Array(2)
// Status
f1=new Array(AnzahlWR)
f2=new Array(AnzahlWR)
for( i=0; i<AnzahlWR; i++ ) {
    found = false
    if(curStatusCode[i]==255)
        bez="Offline"
    else
        bez = getToken(StatusCodes[i],curStatusCode[i],",")
    for( i2=0; i2<cnt; i2++ ) {
        if( f1[i2]==bez ) {
            f2[i2]++
            found = true
            break
        }
    }
    if( !found ) {
        f1[cnt]=bez
        f2[cnt]=1
        cnt++
    }
}
bez2=""
if( cnt>1 ) {
    for( i=0; i<cnt; i++ ) {
        if( i>0 )
            bez2+=", "
        if( f2[i]==1 )
            bez2+=f1[i]
        else
            bez2+=f2[i]+"x"+f1[i]
    }
}
else
    bez2=f1[0]

ret[0]=bez2
// Fehler
found = false
for( i=0; i<AnzahlWR; i++ ) {
    if( curFehlerCode[i]!=0 ) {
        bez = getToken(FehlerCodes[i],curFehlerCode[i],",")
        found = true
    }
}
if( !found )
    bez=""
ret[1]=bez
return ret
}

function printRES_DayHeader( refreshTime, mode, para2 ) {
document.write("<meta http-equiv='refresh' content='"+refreshTime+"; URL=palm.html?"+mode+"&"+para2+"'><meta http-equiv='pragma' content='no-cache' ><meta http-equiv='pragma' content='private' ><meta http-equiv='expires' content='0' >")
}

function printRES_DayHeaderb( typ ) {
document.write("<title>SolarLog"+typ+"<\/title><style type='text/css'>body { }#browseb { position:absolute; top:0px; left:50px; width:60px; height:17px; z-index:1; }#buttonb { position:absolute; top:262px; left:0px; width:319px; height:27px; z-index:1; }#datum { position:absolute; top:0px; left:170px; width:79px; height:30px; z-index:1; }#uhrzeit { position:absolute; top:0px; left:235px; width:95px; height:30px; z-index:1; }#balken { position:absolute; top:20px; left:33px; width:280px; height:150px; z-index:1; }")
}

function printRES_DayHeader2() {
document.write("#TXTLEFT  { position:absolute; top:178px; left:5px; width:160px; height:65px; z-index:1; }#TXTRIGHT { position:absolute; top:178px; left:165px; width:155px; height:65px; z-index:1; }#Status { position:absolute; top:244px; left:5px; width:310px; height:30px; z-index:1; }#PDay   { position:absolute; top:180px; left:180px; width:139px; height:30px; z-index:1; }#PDayp  { position:absolute; top:200px; left:180px; width:139px; height:30px; z-index:1; }#PMax   { position:absolute; top:220px; left:180px; width:139px; height:30px; z-index:1; }")
}

function printRES_YSkalDivYT( var1, var2 ) {
document.write("#YT"+var1+"  { position:absolute; top:"+var2+"px;left:8px;width:20px; height:30px; z-index:1; }")
}
function printRES_YSkalDivY( var1, var2 ) {
document.write("#Y"+var1+"   { position:absolute; top:"+var2+"px;left:27px;width:9px; height:5px; z-index:1; }")
}

function printRES_YSkalDivYS( var1, var2 ) {
document.write("#YS"+var1+"  { position:absolute; top:"+var2+"px;left:38px;width:274px; height:2px; z-index:1; }")
}

function printRES_XSkalDivX( var1, var2 ) {
document.write("#X"+var1+"   { position:absolute; top:144px;left:"+var2+"px;width:10px; height:9px; z-index:1; }")
}

function printRES_XSkalDivXT( var1, var2 ) {
document.write("#XT"+var1+"  { position:absolute; text-align:center; top:151px;left:"+var2+"px;width:28px; height:30px; z-index:1; }")
}

function printRES_DayBody( var1, var2, var3, var4, var5 ) {
document.write("<\/style><\/head><body style='background-image:url(bg_palm.jpg); background-repeat:no-repeat'><map name='Browse'><area shape='rect' coords='0,0,21,25' href='palm.html?"+var1+"&"+var2+"' alt='back'><area shape='rect' coords='25,0,46,25' href='palm.html?"+var3+"&"+var4+"' alt='forw'><\/map><div id='browseb'><img src='e.gif' width='46' height='25' alt='Browse' usemap='#Browse' border='0'><\/div><map name='Button'><area shape='rect' coords='0,0,106,27' href='palm.html?min&000' alt='day'><area shape='rect' coords='107,0,209,27' href='palm.html?day&000' alt='month'><area shape='rect' coords='210,0,319,27' href='palm.html?month&000' alt='year'><\/map><div id='buttonb'><img src='e.gif' width='319' height='27' alt='Button' usemap='#Button' border='0'><\/div>")
}

function printRES_InitDayBalken() {
document.write("<div id='balken'><img src='./e.gif' width='0' height='129'>")
}

function printRES_BalkenBlackGif( var1, var2 ) {
document.write("<img src='./black.gif' width='"+var1+"' height='"+var2+"'>")
}

function printRES_emptyGif( var1, var2) {
document.write("<img src='./e.gif' width='"+var1+"' height='"+var2+"'>")
}

function printRES_YSkalYT( var1, var2 ) {
document.write("<div id='YT"+var1+"'>"+var2+"<\/div>")
}

function printRES_YSkalY( var1 ) {
document.write("<div id='Y"+var1+"'><img src='./black.gif' width='10' height='1'><\/div>")
}

function printRES_YSkalYS( var1 ) {
document.write("<div id='YS"+var1+"'><img src='./ydot.gif' width='274' height='1'><\/div>")
}

function printRES_XSkalBlackGif( var1 ) {
document.write("<div id='X"+var1+"'><img src='./black.gif' width='1' height='10'><\/div>")
}

function printRES_TextStatusDouble( var1, var2 ) {
document.write("<div id='Status'><table style='font-size: 16px; width: 305px;' border='0' cellpadding='0' cellspacing='0'><tbody><tr><td style='width: 20%; font-weight: bold;'>"+getText(LBL_STATUS)+":<\/td><td style='width: 40%; font-weight: bold;'>"+var1+"<\/td><td style='width: 40%; font-weight: bold;'>"+var2+"<\/td><\/tr><\/tbody><\/table><\/div>")
}

function printRES_TextStatusSingle( var1 ) {
document.write("<div id='Status'><table style='font-size: 16px; width: 305px;' border='0' cellpadding='0' cellspacing='0'><tbody><tr><td style='width: 20%; font-weight: bold;'>Status:<\/td><td style='width: 80%; font-weight: bold;'>"+var1+"<\/td><\/tr><\/tbody><\/table><\/div>")
}

function printRES_DayTextLeft1( var1 ) {
document.write("<div id='TXTLEFT'><table style='font-size: 16px;width: 150px;' border='0' cellpadding='0' cellspacing='0'><tbody><tr><td style='width: 30%; font-weight: bold;'>P<small>ac</small></td><td style='width: 54%; font-weight: bold; text-align: right;'>"+var1+"<br></td><td style='width: 16%; font-weight: bold; text-align: right;'>W</td></tr></tbody></table>")
}

function printRES_DayTextLeft2_2( var1, var2 ) {
document.write("<table style='width: 150px;' border='0' cellpadding='0' cellspacing='0'><tbody><tr><td style='font-size: 16px;width: 30%;'>P<small>1/2</small></td><td style='font-size: 11px;width: 27%; text-align: right;'>"+var1+"</td><td style='font-size: 11px;width: 27%; text-align: right;'>"+var2+"</td><td style='font-size: 16px;text-align: right; width: 16%;'>W</td></tr></tbody></table>")
}

function printRES_DayTextLeft2_3( var1, var2, var3 ) {
document.write("<table style='width: 150px;' border='0' cellpadding='0' cellspacing='0'><tbody><tr><td style='font-size: 12px; width: 30%;'>P<small>1/2/3</small></td><td style='font-size: 10px; width: 18%; text-align: right;'>"+var1+"</td><td style='font-size: 10px; width: 18%; text-align: right;'>"+var2+"</td><td style='font-size: 10px; width: 18%; text-align: right;'>"+var3+"</td><td style='font-size: 16px; width: 16%; text-align: right;'>W</td></tr></tbody></table>")
}

function printRES_DayTextLeft2_1( var1 ) {
document.write("<table style='font-size: 16px;width: 150px;' border='0' cellpadding='0' cellspacing='0'><tbody><tr><td style='width: 30%;'>P<small>dc</small></td><td style='width: 54%; text-align: right;'>"+var1+"</td><td style='width: 16%; text-align: right;'>W</td></tr></tbody></table>")
}

function printRES_DayTextLeft3( var1 ) {
document.write("<table style='font-size: 16px;width: 150px;' border='0' cellpadding='0' cellspacing='0'><tbody><tr><td style='width: 30%;'>"+getText(LBL_WGKURZ)+"</td><td style='width: 54%; text-align: right;'>"+var1+"<br></td><td style='width: 16%; text-align: right;'>%</td></tr></tbody></table></div>")
}

function printRES_DayTextRightA( var1 ) {
document.write("<div id='TXTRIGHT'><table style='font-size: 16px;width: 150px;' border='0' cellpadding='0' cellspacing='0'><tbody><tr><td style='width: 27%; font-weight: bold;'>E</td><td style='width: 40%; font-weight: bold; text-align: right;'>"+var1+"<br></td><td style='width: 33%; font-weight: bold; text-align: right;'>kWh</td></tr>")
}

function printRES_DayTextRightB( var1 ) {
document.write("<tr><td style='width: 27%;'>E<small>"+getText(LBL_SPEZ)+"</small></td><td style='width: 40%; text-align: right;'>"+var1+"<br></td><td style='width: 33%; text-align: right;'>Wh/W<small>p</small></td></tr>")
}

function printRES_DayTextRightC( var1 ) {
document.write("<tr><td style='width: 27%;'>P<small>max</small></td><td style='width: 40%; text-align: right;'>"+var1+"<br></td><td style='width: 33%; text-align: right;'>W</td></tr></tbody></table></div>")
}

function printRES_YSkalDivYTDay( var1, var2 ) {
document.write("#YT"+var1+"  { position:absolute; top:"+var2+"px;left:1px;width:20px; height:30px; z-index:1; }")
}

function printRES_YSkalYTDay( var1, var2 ) {
document.write("<div id='YT"+var1+"'>"+var2+"</div>")
}

function printRES_MonthTextRight( var1, var2, var3 ) {
document.write("<div id='TXTRIGHT'><table style='font-size: 16px;width: 150px;' border='0' cellpadding='0' cellspacing='0'><tbody><tr><td style='width: 27%; font-weight: bold;'>E</td><td style='width: 40%; font-weight: bold; text-align: right;'>"+var1+"<br></td><td style='width: 33%; font-weight: bold; text-align: right;'>kWh</td></tr><tr><td style='width: 27%;'>E<small>spez</small></td><td style='width: 40%; text-align: right;'>"+var2+"<br></td><td style='width: 33%; text-align: right;'>Wh/W<small>p</small></td></tr><tr><td style='width: 27%;'>E<small>max</small></td><td style='width: 40%; text-align: right;'>"+var3+"<br></td><td style='width: 33%; text-align: right;'>kWh</td></tr></tbody></table></div>")
}

function printRES_MonthTextLeft( var1, var2, var3 ) {
document.write("<div id='TXTLEFT'><table style='font-size: 16px;width: 150px;' border='0' cellpadding='0' cellspacing='0'><tbody><tr><td style='width: 30%; font-weight: bold;'>E<small>soll</small></td><td style='width: 40%; font-weight: bold; text-align: right;'>"+var1+"</td><td style='width: 30%; font-weight: bold; text-align: right;'>kWh</td></tr><tr><td style='width: 40%;'>E<small>ist</small>/E<small>soll</small></td><td style='width: 30%; text-align: right;'>"+var2+"<br></td><td style='width: 30%; text-align: right;'>%</td></tr><tr><td style='width: 30%;'>P<small>mittel</small></td><td style='width: 40%; text-align: right;'>"+var3+"<br></td><td style='width: 30%; text-align: right;'>kWh</td></tr></tbody></table></div>")
}



function drawBalkenMin( lDate, sDate, maxWatt ) {
var dDate=new Date(lDate), dt
var PacMax=0
var hourStart, hourEnd
var numBalken
var dy, dx, curX, h, mindif
var firstDraw, i, index1, wrI
var stromGes

hourStart = time_start[dDate.getMonth()]
hourEnd   = time_end[dDate.getMonth()]

numBalken = (hourEnd - hourStart) * 60 * 60 / Intervall
if( numBalken == 0 )
    return 0


dy = 129;
dx = 280;
curX = 0;
printRES_InitDayBalken()

firstDraw = true
i=0
index1=m.length-1
while( index1>-1 ) {
    dt = getWRToken( m[index1], 0 )
    if( dt.substring(0,8)==sDate ) {
        dDate = new Date( parseInt(dt.substring(6,8),10)+2000, parseInt(dt.substring(3,5),10)-1, parseInt(dt.substring(0,2),10), parseInt(dt.substring(9,11),10), parseInt(dt.substring(12,14),10), parseInt(dt.substring(15,17),10) )
        if( dDate.getHours()>=hourStart && dDate.getHours()<=hourEnd ) {
            i=(dDate.getHours()*3600+dDate.getMinutes()*60-hourStart*3600)/Intervall
            x = Math.floor(i/numBalken*dx+0.5)
            w = Math.floor( (x-curX)+0.5 )
            curX += w

            h=Math.floor(dy*10/100)    // defaulthöhe für Ausnahmen
            if( firstDraw ) {   // erster Balken
                firstDraw = false
                printRES_emptyGif( w,h )
            }
            else {
                stromGes = 0;
                for( wrI=1; wrI<=AnzahlWR; wrI++ ) {
                    if( typeof WRInfo[wrI-1][11]=="undefined" || WRInfo[wrI-1][11]==0 || (WRInfo[wrI-1][11]==2 && WRInfo[wrI-1][14]==0) ) {
                       data = enumData( m[index1], wrI )
                       stromGes += data[1]
                    } 
                }
                if( stromGes > PacMax )
                    PacMax = stromGes
                if( stromGes > maxWatt )
                    stromGes = maxWatt
                h = Math.floor(stromGes*dy/maxWatt)
                if( h > 0 ) {
                    document.write("<img src='b.gif' width='"+w+"' height='"+h+"'>")

                }
                else
                    printRES_emptyGif( w,h ) // unsichtbar
            }
        }
    }
    index1--
}
document.write("</div>")

return PacMax
}

function drawBalken( mode, sDate, eom, maxWatt ) {
var numBalken
var dy, dx, curX, h, mindif
var firstDraw, i, index1, wrI, found, days, months
var mittelCnt=0,PacMittel=0
var BALKEN_GAPL=1
var BALKEN_GAPR=3
var PacMax=0
var stromGes

numBalken=eom

dy = 129;
dx = 280;
curX = 0;
printRES_InitDayBalken()

if( mode==DAY ) {
    index1=da.length-1
    days=new Array(eom)
    for(i=0;i<eom;i++) {
        days[i]=0
    }
    // days und daysall abmischen
    for(index1=dal.length-1;index1>=0;index1--) {
        dt = getWRToken( dal[index1], 0 )
        if( dt.substring(3,8)==sDate.substring(3,8) ) {
            dDate = new Date( parseInt(dt.substring(6,8),10)+2000, parseInt(dt.substring(3,5),10)-1, parseInt(dt.substring(0,2),10) )
            i=dDate.getDate()
            data=enumData( dal[index1], 1 )
            days[eom-i]=data[1]
        }
    }
    for(index1=da.length-1;index1>=0;index1--) {
        dt = getWRToken( da[index1], 0 )
        if( dt.substring(3,8)==sDate.substring(3,8) ) {
            dDate = new Date( parseInt(dt.substring(6,8),10)+2000, parseInt(dt.substring(3,5),10)-1, parseInt(dt.substring(0,2),10) )
            i=dDate.getDate()
            days[eom-i]=0
            for( wrI=1; wrI<=AnzahlWR; wrI++ ) {
                if( typeof WRInfo[wrI-1][11]=="undefined" || WRInfo[wrI-1][11]==0 || (WRInfo[wrI-1][11]==2 && WRInfo[wrI-1][14]==0) ) {
                   data=enumData( da[index1], wrI )
                   days[eom-i]+=data[1]
                } 
            }
        }
    }
    index1=eom-1
}
else {
    months=new Array(eom)
    for(i=0;i<eom;i++) {
        months[i]=0
    }
    for(index1=mo.length-1;index1>=0;index1--) {
        dt = getWRToken( mo[index1], 0 )
        if( dt.substring(6,8)==sDate.substring(6,8) ) {
            dDate = new Date( parseInt(dt.substring(6,8),10)+2000, parseInt(dt.substring(3,5),10)-1, 1 )
            i=dDate.getMonth()+1
            months[eom-i]=0
            for( wrI=1; wrI<=AnzahlWR; wrI++ ) {
                if( typeof WRInfo[wrI-1][11]=="undefined" || WRInfo[wrI-1][11]==0 || (WRInfo[wrI-1][11]==2 && WRInfo[wrI-1][14]==0) ) {
                   data=enumData( mo[index1], wrI )
                   months[eom-i]+=data[1]
                }
            }
        }
    }
    index1=eom-1
}
while( index1>-1 ) {
    i=eom-index1

        x = Math.floor(i/numBalken*dx+0.5)
        w = Math.floor( (x-curX)+0.5 )
        curX += w
        w = w - BALKEN_GAPL - BALKEN_GAPR;
        h=dy*10/100;

        stromGes = 0;
        if( mode==DAY ) {
            stromGes=days[index1]
        }
        else {
            stromGes=months[index1]
        }
        if( stromGes > PacMax )
            PacMax = stromGes
        PacMittel += stromGes;
        mittelCnt++;
        if( stromGes > maxWatt )
            stromGes = maxWatt

        printRES_emptyGif( BALKEN_GAPL, 0 ) // unsichtbar
        h = Math.floor(stromGes*dy/maxWatt)
        if( h > 0 )
            document.write("<img src='./b.gif' width='"+w+"' height='"+h+"'>")
        else
            printRES_emptyGif( w,h ) // unsichtbar
        printRES_emptyGif( BALKEN_GAPR, 0 ) // unsichtbar

    index1--
}
document.write("</div>")
return new Array( PacMax, (PacMittel/mittelCnt))
}

function calcSollIst( para2, mode, eom, dDate, PacSum ) {
var PacSoll=0, PistProz=0
var stromSollProz
var i,end_mon

// SOLL-Prozent aufaddieren
if( mode == MIN ) {
    stromSollProz = sollMonth[dDate.getMonth()] / eom;
}
else if( mode == DAY ) {
    if( para2 == 0 )
        stromSollProz = sollMonth[dDate.getMonth()] * (new Date().getDate()) / eom
    else
        stromSollProz = sollMonth[dDate.getMonth()]
}
else {
    stromSollProz = 0;
    if( para2 == 0 )
        end_mon = new Date().getMonth()+1;
    else
        end_mon = 12;
    for( i=0; i<end_mon; i++ ) {
        if( para2 == 0 && i == (new Date().getMonth()) ) {
            stromSollProz += sollMonth[i] * (new Date().getDate()) / getEom(new Date())
        }
        else
            stromSollProz += sollMonth[i]
    }
}
// Strom SOLL errechnen */
PacSoll = SollYearKWP*AnlagenKWP/1000 * stromSollProz/100
// IST-Prozent berechnen */
PistProz = PacSum/1000 / PacSoll*100
return new Array( PacSoll, PistProz )
}

function showMins( para2 ) {
var dDate, lDate, sDate, sTime
var refreshTime
var maxWatt, dy, ymax, cnt, i, wrI, maxCnt, deltay, found
var dx, deltax
var PacDay, PacDayp, PacDayMax, status, localPdc, WG

// Datum ermitteln: Heute - para2
lDate = new Date().getTime()
lDate -= para2*86400000
dDate = new Date(lDate)
sDate = fmt00(dDate.getDate())+"."+fmt00(dDate.getMonth()+1)+"."+fmt00(dDate.getFullYear()-2000)

refreshTime=60
printRES_DayHeader( refreshTime, "min", para2 )
printRES_DayHeaderb( SLTyp )
printRES_DayHeader2()

// Y-Achsen Skalierung vorbereiten
dy=129
maxWatt=getGesAnlagenMaxWatt(MIN)
ret=calcYSkal( maxWatt )
ymax=ret[0]
deltay=ret[1]
maxCnt=ymax/deltay

for( cnt = 0; cnt <= maxCnt; cnt++ ) {
    if( cnt>0 )
        printRES_YSkalDivYT( cnt, 150 - 11 - Math.floor(deltay*dy*cnt / ymax) )
    printRES_YSkalDivY( cnt, 149 - Math.floor( deltay*dy*cnt / ymax ) )
    printRES_YSkalDivYS( cnt, 149 - Math.floor( deltay*dy*cnt/ymax ) )
}

// X-Achsen Skalierung vorbereiten
dx = 280
deltax=time_end[dDate.getMonth()]-time_start[dDate.getMonth()]
for( cnt = 0; cnt <= deltax; cnt++ ) {
    printRES_XSkalDivX( cnt, 31 + Math.floor(cnt * dx / deltax) )
}
for( cnt = 0; cnt <= deltax; cnt=cnt+2 ) {
    printRES_XSkalDivXT( cnt, 17 + cnt * dx / deltax )
}

printRES_DayBody( "min", para2+1, "min", para2-1, sDate )
var TDatum = new Date(sDate.substr(6,2),sDate.substr(3,2)-1,sDate.substr(0,2));
document.write("<div id='datum'>"+TDatum.format(DateFormat)+"</div>");
if( para2 == 0 )
{
  var Zeit= new Date();
  Zeit.setHours(Uhrzeit.substr(0,2));
  Zeit.setMinutes(Uhrzeit.substr(3,2));
  Zeit.setSeconds(Uhrzeit.substr(6,2));
  document.write("<div id='uhrzeit'>"+Zeit.format(TimeFormat)+"</div>");
}

// Tagesverlauf zeichnen
PacDayMax = drawBalkenMin( lDate, sDate, ymax )

// Y-Achsen zeichen */
for( cnt = 1; cnt <= maxCnt; cnt++ ) {
    valueSkal = Math.floor(deltay/100*cnt)/10
    if( cnt/2==Math.floor(cnt/2) ) {
        printRES_YSkalYT( cnt, valueSkal )
        printRES_YSkalYS( cnt )
    }
    printRES_YSkalY( cnt )
}

// X-Achsen zeichnen */
for( cnt = 0; cnt < deltax; cnt++ ) {
    printRES_XSkalBlackGif( cnt)
}
document.write("<small>")
for( cnt = 0; cnt < deltax; cnt=cnt+2 ) {
    document.write("<div id='XT"+cnt+"' align='center'>"+(cnt+time_start[dDate.getMonth()]) +"</div>")
}
document.write("<big>")
// Beschriftung
PacDay=0
PacDayp=0
found=false
for( i=0; i<dal.length; i++ )
{
    dt = getWRToken( dal[i], 0 )
    if( sDate == dt ) {
        data = enumData( dal[i], 1 );
        PacDay += data[1];
        PacDayp=PacDay/getCurAnlagenKWP()
        found=true
        break
    }
}
if( found==false && da.length>0 ) {
  for( i=0; i<da.length; i++ )
  {
    dt = getWRToken( da[i], 0 )
    if( sDate == dt ) {
        for( wrI=1; wrI<=AnzahlWR; wrI++ ) {
            if( typeof WRInfo[wrI-1][11]=="undefined" || WRInfo[wrI-1][11]==0 || (WRInfo[wrI-1][11]==2 && WRInfo[wrI-1][14]==0) ) {
               data = enumData( da[i], wrI );
               PacDay += data[1];
            }
        }
        PacDayp=PacDay/getCurAnlagenKWP()
        break
    }
  }
}
status=calcStatus()
if( status[1]=="" )
    printRES_TextStatusSingle( status[0] )
else
    printRES_TextStatusDouble( status[0], status[1] )

printRES_DayTextLeft1( Pac )

localPdc=0
if( AnzahlWR==1 && WRInfo[0][5]>1 ) { // Multistring!
    data = enumData( m[0], 1 )

    if( WRInfo[0][5]==2 ) {
        if( status[0]!="Offline" ) {
            localPdc += data[2] + data[3]
            printRES_DayTextLeft2_2( data[2], data[3] )
        }
        else
            printRES_DayTextLeft2_2( 0, 0 )
    }
    else {
        if( status[0]!="Offline" ) {
            localPdc += data[2] + data[3] + data[4]
            printRES_DayTextLeft2_3( data[2], data[3], data[4] )
        }
        else
            printRES_DayTextLeft2_3( 0, 0, 0 )
    }
}
else {
    if( status[0]!="Offline" ) {
        for( wrI=1; wrI<=AnzahlWR; wrI++ ) {
            if( typeof WRInfo[wrI-1][11]=="undefined" || WRInfo[wrI-1][11]==0 || (WRInfo[wrI-1][11]==2 && WRInfo[wrI-1][14]==0) ) {
               if( curStatusCode[wrI-1]!=255 ) {
                   data = enumData( m[0], wrI )
                   for( i=1; i<=WRInfo[wrI-1][5]; i++ )
                       localPdc += data[1+i]
               }
            }
        }
        if( localPdc>Pac )
            printRES_DayTextLeft2_1( localPdc )
        else
            printRES_DayTextLeft2_1( "---" )
    }
    else
        printRES_DayTextLeft2_1( 0 )
}

WG=0
if( localPdc>0 )
    WG = Math.floor(Pac / localPdc * 1000)/10
if( WG<98.0 )
    printRES_DayTextLeft3( WG )
else
    printRES_DayTextLeft3( "---" )


printRES_DayTextRightA( Math.floor( PacDay/10 )/100 )
printRES_DayTextRightB( Math.floor( PacDayp*100 )/100 )
printRES_DayTextRightC( PacDayMax )

document.write("</body></html>")
}



function showDays( para2 ) {
var dDate, jahr, monat, sDate, sDate2, sTime, eom
var refreshTime
var maxWatt, dy, ymax, cnt, i, wrI, maxCnt, deltay
var dx, deltax
var status
var PacSum, Pacp, PacMax, PacSoll, PistProz, PacMittel=0
var monate=getText(LBL_MONKURZ)

// Datum ermitteln: Monatsanfang - para2
dDate = new Date()
jahr=dDate.getFullYear()
monat=dDate.getMonth()+1
i=para2
while( i>0 ) {
   monat--
   if( monat==0 ) {
      jahr--
      monat=12
   }
   i--
}
dDate = new Date( jahr, monat-1, 1 )
sDate = "01."+fmt00(dDate.getMonth()+1)+"."+fmt00(dDate.getFullYear()-2000)
sDate2 = getToken( monate, monat-1 ,",")+" "+jahr
eom=getEom(dDate)

refreshTime = 255;
printRES_DayHeader(refreshTime, "day", para2)
printRES_DayHeaderb( SLTyp )
printRES_DayHeader2()

// Y-Achsen Skalierung vorbereiten
dy=129
maxWatt=getGesAnlagenMaxWatt(DAY)
ret=calcYSkal( maxWatt )
ymax=ret[0]
deltay=ret[1]
maxCnt=ymax/deltay

for( cnt = 0; cnt <= maxCnt; cnt++ ) {
    if( cnt>0 )
        printRES_YSkalDivYTDay( cnt, 150 - 11 - Math.floor(deltay*dy*cnt / ymax) )
    printRES_YSkalDivY( cnt, 149 - Math.floor( deltay*dy*cnt / ymax ) )
    printRES_YSkalDivYS( cnt, 149 - Math.floor( deltay*dy*cnt/ymax ) )
}


// X-Achsen Skalierung vorbereiten */
dx = 280;
for( cnt = 0; cnt < eom; cnt++ ) {
    printRES_XSkalDivX( cnt, 31 + Math.floor(cnt * dx / eom) )
}
for( cnt = 0; cnt < eom; cnt++ ) {
    if( cnt== 0 || (((cnt+1) % 5 == 0) && cnt < eom-2) || cnt == eom-1 )
        printRES_XSkalDivXT( cnt, 22 + Math.floor(cnt * dx / eom) )
}
//
printRES_DayBody( "day", para2+1, "day", (para2==0?0:para2-1) ,sDate2 );
document.write("<div id='datum'>"+sDate2+"</div>");

ret=drawBalken( DAY, sDate, eom, ymax )
PacMax=ret[0]
PacMittel=ret[1]

// Y-Achsen zeichen */
document.write("<small>")
for( cnt = 1; cnt <= maxCnt; cnt++ ) {
    valueSkal = Math.floor(deltay/1000*cnt)
    printRES_YSkalYTDay( cnt, valueSkal )
    printRES_YSkalY( cnt )
    printRES_YSkalYS( cnt)
}
// X-Achsen zeichnen */
for( cnt = 0; cnt < eom; cnt++ ) {
    printRES_XSkalBlackGif( cnt )
}
document.write("<small>")
for( cnt = 0; cnt < eom; cnt++ ) {
    if( cnt== 0 || (((cnt+1) % 5 == 0) && cnt < eom-2) || cnt == eom-1 )
        document.write("<div id='XT"+cnt+"'>"+(cnt+1)+"</div>")
}
document.write("<big><big>")

// Monatswerte holen
PacSum=0
i=0
while( i<mo.length )
{
    dt = getWRToken( mo[i], 0 )
    if( dt.substring(3,8)==sDate.substring(3,8) ) {
        for( wrI=1; wrI<=AnzahlWR; wrI++ ) {
            if( typeof WRInfo[wrI-1][11]=="undefined" || WRInfo[wrI-1][11]==0 || (WRInfo[wrI-1][11]==2 && WRInfo[wrI-1][14]==0) ) {
               data = enumData( mo[i], wrI );
               PacSum += data[1];
               if( para2==0 && da.length>0 ) {
                   data2 = enumData( da[0], wrI )
                   PacSum += data2[1]
               }
            }
        }

    }
    i++
}
Pacp=PacSum/getCurAnlagenKWP()

status=calcStatus()
if( status[1]=="" )
    printRES_TextStatusSingle( status[0] )
else
    printRES_TextStatusDouble( status[0], status[1] )


printRES_MonthTextRight( Math.floor( PacSum/1000 ), Math.floor( Pacp*10)/10, Math.floor( PacMax/100 )/10 )

ret = calcSollIst( para2, DAY, eom, dDate, PacSum )
PacSoll = ret[0]
PistProz = ret[1]
if( PacSum != 0 ) {
    printRES_MonthTextLeft( Math.floor(PacSoll), Math.floor(PistProz*10)/10, Math.floor(PacMittel/100)/10 )
}
else
    printRES_MonthTextLeft( 0,0,0 )

document.write("</body></html>")

}

