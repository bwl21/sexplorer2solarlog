

var scriptPath = function() {
	var scripts= document.getElementsByTagName('script');
	var path= scripts[scripts.length-1].src.split('?')[0];      // remove any ?query
	var mydir= path.split('/').slice(0, -1).join('/')+'/';
	return mydir
}
var xxTest=scriptPath();

var slDaten=xxTest+"/02_SolarLog/";

var anlagenfotoDiv="<img src='"+xxTest+"solaranlage.jpg' width='699px'></img>";
