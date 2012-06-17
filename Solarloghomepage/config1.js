var scriptPath = function () {
    var scripts = document.getElementsByTagName('SCRIPT');
    var path = '';
    if(scripts && scripts.length>0) {
        for(var i in scripts) {
            if(scripts[i].src && scripts[i].src.match(/config\.js$/)) {
                path = scripts[i].src.replace(/(.*)config\.js$/, '$1');
            }
        }
    }
    return path;
};

var scriptPath1 = function() {
	var scripts= document.getElementsByTagName('script');
	var path= scripts[scripts.length-1].src.split('?')[0];      // remove any ?query
	var mydir= path.split('/').slice(0, -1).join('/')+'/';
	return mydir
}
var xxTest=scriptPath1();

var slDaten=xxTest+"../TestCases/01_SunnyExplorer_ein-wr/02_SolarLog/";

var anlagenfotoDiv="<img src='"+xxTest+"solaranlage.jpg' width='699px'></img>";
