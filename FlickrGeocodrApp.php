<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<link rel="search"
           type="application/opensearchdescription+xml" 
           href="http://geocodr.net/opensearchdescription.xml"
           title="Geocodr search for places" />
<title>Flickr Geocodr - finding places from peoples photos</title>
<script src="http://openlayers.org/api/OpenLayers.js"></script>
<script src="prototype.js"></script>
<script type="text/javascript">


var map, layer, laycontrol;

function init(){

    var options = {
        projection: new OpenLayers.Projection("EPSG:900913"),
        displayProjection: new OpenLayers.Projection("EPSG:4326"),
        units: "m",
        numZoomLevels: 18,
        maxResolution: 156543.0339,
        maxExtent: new OpenLayers.Bounds( - 20037508, -20037508, 20037508, 20037508.34),
        controls: [new OpenLayers.Control.Attribution(), new OpenLayers.Control.LayerSwitcher(), new OpenLayers.Control.PanZoomBar(),new OpenLayers.Control.Navigation()]
    };
    
	map = new OpenLayers.Map('map', options);
	
	var mapnik = new OpenLayers.Layer.TMS("OSM Mapnik", "http://tile.openstreetmap.org/", {
        type: 'png',
        getURL: osm_getTileURL,
        displayOutsideMaxExtent: true,
        attribution: '<a href="http://www.openstreetmap.org/">OpenStreetMap</a>'
    });
	

	map.addLayers([mapnik]);

    map.zoomIn();
	//map.setCenter(new OpenLayers.LonLat(-10, 20), 1);

}

function osm_getTileURL(bounds) {
    var res = this.map.getResolution();
    var x = Math.round((bounds.left - this.maxExtent.left) / (res * this.tileSize.w));
    var y = Math.round((this.maxExtent.top - bounds.top) / (res * this.tileSize.h));
    var z = this.map.getZoom();
    var limit = Math.pow(2, z);

    if (y < 0 || y >= limit) {
        return OpenLayers.Util.getImagesLocation() + "404.png";
    } else {
        x = ((x % limit) + limit) % limit;
        return this.url + z + "/" + x + "/" + y + "." + this.type;
    }
}

function addmarker(place, lat, lon){
 
    
    var llunproj = new OpenLayers.LonLat(lon,lat);
   var lonlat  = llunproj.transform(map.displayProjection, map.projection);

	var markersLayer = new OpenLayers.Layer.Markers(place);
	var cmarker = new OpenLayers.Marker(
			lonlat, 
			new OpenLayers.Icon(
				'http://openlayers.org/api/img/marker.png', 
				new OpenLayers.Size(21,25)
				));
	markersLayer.addMarker(cmarker); 
	map.addLayer(markersLayer);
	var cpopup = new OpenLayers.Popup('Cluster',
			lonlat,
			new OpenLayers.Size(134,50), place, true);

	cpopup.setBackgroundColor('yellow');

	cpopup.setOpacity(0.8);
	cpopup.hide();
	map.addPopup(cpopup);

	cmarker.events.register( 'click', cmarker, function (e) { cpopup.toggle() } );
	map.setCenter(lonlat, 10);


}
//dojo call

function getplacereq_prototype(place) {

	var url = 'Flickr/flickrgeocodr.php';
	//var pars = 'empID=' + empID + '&year=' + y;
	var pars = 'place=' + place;

	var myAjax = new Ajax.Request(
			url, 
			{
method: 'get', 
parameters: pars, 
onComplete: doPlace_prototype
});

}

function doPlace_prototype(originalRequest) {
	// console.log(originalRequest.responseText);
	var data = originalRequest.responseXML;
	document.getElementById("sign").value = "Clustering";

	if (data.documentElement.getElementsByTagName("error")[0].childNodes[0].nodeValue.length < 2){
		var lat = data.documentElement.getElementsByTagName("latitude")[0].childNodes[0].nodeValue;
		//console.log(lat);
		var lon = data.documentElement.getElementsByTagName("longitude")[0].childNodes[0].nodeValue;
		place = data.documentElement.getElementsByTagName("place")[0].childNodes[0].nodeValue;
		//error check 
		addmarker(place, lat, lon);
		document.getElementById("sign").value = "Showing: "+ place + " lat="+ lat +" lon="+lon;
	}
	else{
		document.getElementById("sign").value = "error = " + data.documentElement.getElementsByTagName("error")[0].childNodes[0].nodeValue;
	} 

}



function geocode(place){
	//alert(place);
	document.getElementById("sign").value = "Connecting.";
	getplacereq_prototype(place)

}
// -->
</script>
<style type="text/css">
#map {
	width: 650px;
height: 400px;
margin : 5px;
border: 1px solid black;
}
fieldset {
margin : 5px;
width: 500px;
}
#sign {
border: 1px dashed gray;
margin: 5px;
	background-color: #FFFFCC;  

}

</style>
</head>
<body onLoad="init()" />

<p><img src="Flickr/geocodr.png" width="396" height="90"><br />
A geocoder made from searching Flickr photos (<a href="FlickrGeocodrDocs.php">About</a>)<br />
<input type="text" id="placebox" style="margin:5px"  >
<input type="button" style="margin:5px" value="geocode" onClick="geocode(document.getElementById('placebox').value);">
<input type="text" size="60" disabled id="sign" value="">
<p>
<div id="map"></div>
<p>&nbsp;</p>
</body>
</html>
