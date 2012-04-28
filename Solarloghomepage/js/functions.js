var pfad = "";

var debug=0;
//var debug=1;
//var eeg_debug=0;
var eeg_debug=1;

function aw_fmt0(x, n) {
  if ( n == null ) n = 1;
  var e = Math.pow(10, n);
  var k = (Math.round(x * e) / e).toString();
  if (k.indexOf('.') == -1) k += '.';
  k += e.toString().substring(1);
  return k.substring(0, k.indexOf('.') + n+1);
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
  return getToken( d, wrI, "|" );
}
function enumData( d, wrI ) {
  var data=new Array();
  var pnt1, pnt2, s=0;
  data[0] = getWRToken( d, 0 );
  d = getWRToken( d, wrI );
  pnt1=0;
  while( true ) {
    pnt2 = d.indexOf(";",pnt1);
    if( pnt2 <= 0 ) {
      data[1+s] = parseInt( d.substr(pnt1),10 );
      break;
    }
    data[1+s] = parseInt( d.substr(pnt1, pnt2-pnt1),10 );
    pnt1 = pnt2+1;
    s++;
  }
  return data;
}

function runden(x, n, vz) {
  if ( n == null ) n = 1;
  if ( vz == null ) vz = 0;
  var e = Math.pow(10, n);
  var k = (Math.round(x * e) / e).toString();
  if (k.indexOf('.') == -1) k += '.';
  k += e.toString().substr(1);
  k = k.substring(0, k.indexOf('.') + n+1);
  f = k;
  //if (Langx == "DE") {
  var parts = k.split(".");
  f = parts[0]+Dezimalseparator+parts[1];
  //}
  if ( (vz == 1) && k > 0 ) {
    f = "+"+f;
  }
  if ( n == 0 ) f = f.substring(0, f.length-1);
  return f;
}

function fmt00(v){
  if( v<10 )
    return "0"+v;
  else
    return ""+v;
}
function kWYScale(y) {
  return ""+Math.floor(y/1000)+" kWh";
}
function dayOfYear(y,m,d) {
  return ymd2julian(y,m+1,d)-ymd2julian(y,1,1);
}
function ymd2julian(y,m,d) {
  if (m < 3)
    var f = -1;
  else
    var f = 0;
  return Math.floor((1461*(f+4800+y))/4) + Math.floor(((m-2-(f*12))*367)/12) - Math.floor(3*Math.floor((y+4900+f)/100)/4) + d - 32075;
}

function setYear(n,yy) {
  var inverter = Array();
  for(wrI=getStartIndex();wrI<=getEndIndex();wrI=getNextIndex()){
    if (WRInfo[wrI-1][11] == 0)
      inverter.push(wrI);
  }
  var doyLastDay = dayOfYear(today.getYear(), today.getMonth(), today.getDate());

  if(yy == da[0].substr(6,2)) {
    days[n] = new Array(doyLastDay);
  }
  else if (yy%4 == 0) {
    days[n] = new Array(366);
  }
  else {
    days[n] = new Array(365);
  }

  todaya = new Date(20+yy, 0, 1 );
  for ( var i=da.length-1; i>=0; i-- ) {
    dt = getWRToken( da[i], 0);
    dtx    = new Date(20+dt.substr(6,2), dt.substr(3,2)-1, dt.substr(0,2) );

    if ( dt.substr(6,2) == yy ) {
      sum=0;

      for ( wrI=getStartIndex(); wrI<=getEndIndex(); wrI=getNextIndex() ) {
        if (WRInfo[wrI-1][11] == 0) {
          data = enumData( da[i], wrI);
          sum += data[1];
        }
      }
      days[n][Math.floor( (dtx - todaya) / 86400000)] = sum;
    }
  }
}

function formatUhrzeit(Zeit) {
  var stunden=Math.floor(Zeit/3600);
  var minuten=Math.round((Zeit%3600)/60);
  if( minuten >= 10 )
    return stunden+":"+minuten+" "+getText(LBL_HOUR);
  return stunden+":0"+minuten+" "+getText(LBL_HOUR);
}
function formatDT(dt) {
  return dt.substring(9,14)+" "+getText(LBL_HOUR);
}

function monatXScale(x) {
  var day, result="";
  day=new Date(x).getDate();
  if( day%2==0 || day==1 || day==nod ) {
    result=""+day;
  }
  return result;
}
function GoDay( jahr, monat, tag) {
  datum1 = new Date();
  datum2 = new Date(jahr,monat,tag);
  offset = -Math.floor((datum1-datum2)/86400000);
  modus=0;
  GoLocation();
}
function GoMonth( jahr, monat ) {
  offset = - ((new Date().getFullYear()*12+new Date().getMonth()) - (jahr*12+monat));
  modus=1;
  GoLocation();
}
function GoYear( jahr ) {
  offset = - (new Date().getFullYear() - jahr);
  modus=2;
  GoLocation();
}
function setSToday( today ) {
  var Month=new Array(getText(LBL_MON01),getText(LBL_MON02),getText(LBL_MON03),getText(LBL_MON04),getText(LBL_MON05),getText(LBL_MON06),getText(LBL_MON07),getText(LBL_MON08),getText(LBL_MON09),getText(LBL_MON10),getText(LBL_MON11),getText(LBL_MON12));
  switch( modus ) {
    case 0:
      hoffset = - ((new Date().getFullYear()*12+new Date().getMonth()) - (fmt00(today.getFullYear())*12+today.getMonth()))
      sTodayKlar = "<a href=\"visu.html?mode=1\&amp;offset="+hoffset+"\&amp;flag="+flag+"\"> "+fmt00(today.getDate())+"."+fmt00(today.getMonth()+1)+"."+fmt00(today.getFullYear()-2000) + "</a>";
      break
    case 1:
      hoffset = - (new Date().getFullYear() - today.getFullYear())
      sTodayKlar = "<a href=\"visu.html?mode=2\&amp;offset="+hoffset+"\&amp;flag="+flag+"\"> "+Month[today.getMonth()]+" "+today.getFullYear() + "</a>";
      break
    case 2:
      sTodayKlar = "<a href=\"visu.html?mode=3\&amp;flag="+flag+"\">" + today.getFullYear() + "</a>";
      break
    default:
      sTodayKlar = "";
  }
  sToday = fmt00(today.getDate())+"."+fmt00(today.getMonth()+1)+"."+fmt00(today.getFullYear()-2000);
}
function CheckInv( i ) {
  inv = inv ^ Math.pow(2,i);
  showOneInv(i)
}
function showOneInv( i ) {
  if( AnzahlWR > 1 ) {
    var name;
    if( i==31 )
      name = "InvAll";
    else
      name = "Inv"+i;
    document.images[name].src=(inv & Math.pow(2,i)) ? oChoose[i].src : oEmpty[i].src;
  }
}
function showAllInv() {
  showOneInv(31);
  for( i=1; i<=AnzahlWR; i++ )
    showOneInv(i);
}
function CheckFlag( i ) {
  flag = flag ^ Math.pow(2,i);
  showAllFlags(i);
}
function showAllFlags() {
  for( i=0; i<(aFlagBez.length-1); i++ ) {
    document.images["Flag"+i].src=(flag & Math.pow(2,i)) ? oFlagsC[i].src : oFlagsE[i].src;
  }
}
function awCheckFlag( i ) {
  flag = flag ^ Math.pow(2,i);
  awshowAllFlags(i);
}
function awshowAllFlags() {
  for( i=(aFlagBez.length-1); i<aFlagBez.length; i++ ) {
    document.images["Flag"+i].src=(flag & Math.pow(2,i)) ? oFlagsC[i].src : oFlagsE[i].src;
  }
}
function GoBackward() {
  switch( modus ) {
    case 0:
      min_offset = (((indate.getFullYear()*12+indate.getMonth())*30)+indate.getDate()) - (((fmt00(today.getFullYear())*12+today.getMonth())*30)+today.getDate());
      if( min_offset < 0 ){
        offset--;
        GoLocation();
      }
      break
    case 1:
      min_offset = ((indate.getFullYear()*12+indate.getMonth()) - (fmt00(today.getFullYear())*12+today.getMonth()));
      if( min_offset < 0 ){
        offset--;
        GoLocation();
      }
      break
    case 2:
      min_offset = (indate.getFullYear() - fmt00(today.getFullYear()));
      if( min_offset < 0 ){
        offset--;
        GoLocation();
      }
      break
    default:
  }
}
function GoForward() {
  if( modus<=2 && offset< 0 ) {
    offset++;
    GoLocation();
  }
}
function getMaxWRP( modusNeu ) {
  var maxWRP = 0, i, modusLokal=modus;
  if( modusNeu != null )
    modusLokal=modusNeu;

  if( AnzahlWR==1 )
    return MaxWRP[0][modusLokal];

  if( inv & Math.pow(2,31) ) {
    for( i=0; i<AnzahlWR; i++ ) {
      maxWRP += MaxWRP[i][modusLokal];
    }
  }
  else {
    for( i=0; i<AnzahlWR; i++ ) {
      if( inv & Math.pow(2,1+i) ) {
        if( modusLokal==0 ) {
          if( MaxWRP[i][modusLokal] > maxWRP )
            maxWRP = MaxWRP[i][modusLokal];
        }
        else {
          maxWRP += MaxWRP[i][modusLokal];
        }
      }
    }
  }
  return maxWRP;
}
function getCurAnlagenKWP() {
  if( inv & Math.pow(2,31) )
    return AnlagenKWP;
  var wrI, sum=0;
  for( wrI=1; wrI<=AnzahlWR; wrI++ ) {
    if( inv & Math.pow(2,wrI) )
      sum += WRInfo[wrI-1][2];
  }
  return sum;
}
function getSollMonth( monat ) {
  var wrI, i=0, sum=0, anzahl=0, sumSoll;
  while( i<mo.length ) {
    dt = getWRToken( mo[i], 0 );
    if( parseInt(dt.substring(3,5),10)==monat+1 &&
      !(dt.substring(6,8)==sToday.substring(6,8)) ) {
      if( inv & Math.pow(2,31) ) {
        for( wrI=1; wrI<=AnzahlWR; wrI++ ) {
          data = enumData( mo[i], wrI );
          sum += data[1];
          anzahl++;
        }
      }
      if( !(inv & Math.pow(2,31)) ) {
        for( wrI=1; wrI<=AnzahlWR; wrI++ ) {
          if( inv & Math.pow(2,wrI) ) {
            data = enumData( mo[i], wrI );
            sum += data[1];
            anzahl++;
          }
        }
      }
    }
    i++
  }
  if( anzahl>0 )
    sum = sum / anzahl / getCurAnlagenKWP();
  sumSoll = SollYearKWP/100*sollMonth[monat];
  if( sum == 0 || sum < sumSoll*0.8 )
    sum = sumSoll;
  else
    sum = (sum*3 + sumSoll)/4;
  return sum;
}
function enumEventData( d, c ) {
  var data=new Array();
  var pnt=0, pnt2=0, s=0;
  while( true ) {
    pnt2 = d.indexOf(c,pnt);
    if( pnt2 < 0 ) {
      data[s] = d.substr(pnt);
      break;
    }
    else
      data[s] = d.substr(pnt, pnt2-pnt);
    pnt=pnt2+1;
    s++;
  }
  return data;
}
function sortDate( d ) {
  return d.substr(6,2)+d.substr(3,2)+d.substr(0,2)+d.substr(9);
}
function sortEvent( a, b) {
  var data1 = enumEventData(a,";");
  var data2 = enumEventData(b,";");
  if( ""+data1[2]+sortDate(data1[0]) < ""+data2[2]+sortDate(data2[0]) )
    return 1;
  else
    return -1;
}
function writeTD( value, width, align, color) {
  document.write("<td");
  if (width != null)
    document.write(" width=\""+width+"\"");
  if (align != null)
    document.write(" align=\""+align+"\"");
  if (color != null)
    document.write(" bgcolor="+color);
  document.write(">"+value+"<\/td>");
}
function writeTABLE_START(height) {
  document.write("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"");
  if (height != null)
    document.write(" height=\""+height+"\"");
  document.write(" width=\"100%\">");
  document.write("<tbody>");
  document.write("<tr>");
}
function writeTABLE_END() {
  document.write("<\/tr>");
  document.write("<\/tbody>");
  document.write("<\/table>");
}
function calcStatus() {
  var i, i2, f1, f2, cnt=0, found, bez="", bez2="", ret=new Array(2);
  // Status
  f1=new Array(AnzahlWR);
  f2=new Array(AnzahlWR);
  for( i=0; i<AnzahlWR; i++ ) {
    found = false;
    if(curStatusCode[i]==255)
      bez="Offline";
    else
      bez = getToken(StatusCodes[i],curStatusCode[i],",");
    for( i2=0; i2<cnt; i2++ ) {
      if( f1[i2]==bez ) {
        f2[i2]++;
        found = true;
        break;
      }
    }
    if( !found ) {
      f1[cnt]=bez;
      f2[cnt]=1;
      cnt++;
    }
  }
  bez2="";
  if( cnt>1 ) {
    for( i=0; i<cnt; i++ ) {
      if( i>0 )
        bez2+=", ";
      if( f2[i]==1 )
        bez2+=f1[i];
      else
        bez2+=f2[i]+"x"+f1[i];
    }
  } else
    bez2=f1[0];
  ret[0]=bez2;
  // Fehler
  found = false;
  for( i=0; i<AnzahlWR; i++ ) {
    if( curFehlerCode[i]!=0 ) {
      bez = getToken(FehlerCodes[i],curFehlerCode[i],",");
      found = true;
    }
  }
  if( !found )
    bez="";
  ret[1]=bez;
  return ret;
}


var WRCol = new Array("Red","Green","brown","lime","olive","pink","fuchsia","lightblue","aqua","silver","gray","#009900","#009933","#009966","#009999","#0099CC","#0099FF","#00CCFF","#00FFFF","#33FFFF");
