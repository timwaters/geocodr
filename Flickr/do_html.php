<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php 
if ($code != 0 OR $tryagain > 2){ 
$error =  $message . ' : Code: ' . $code . ' Tried '.$tryagain.'times.';
$errmsg = "Sorry couldn't find a location for ".$place.", because of an error. The error was ".$error. "<br />";
$emptysearch = true;
 }

if ($emptysearch) {
$place ="";
} else {

$bbox = getBBOX($clusters[$largestClust]);
	$clusterArray = array("type"=>"cluster", "id"=>$largestClust, "pointcount"=>$largestClustSize, 
	"lat"=>$cX,  
	"lon"=>$cY,
	"bbox"=>$bbox, 
	"place"=>$place);

$errmsg = $cX ." ". $cY;
$addmarker = "'".$place."'" . "," . $cX . "," . $cY ;

}


?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<link rel="search"
           type="application/opensearchdescription+xml" 
           href="http://geocodr.net/opensearchdescription.xml"
           title="Geocodr search for places" />
<style type="text/css">
        #map {
            width: 100%;
            height: 500px;
            border: 1px solid black;
        }
      
	 
    </style>
	    <script src="http://openlayers.org/api/OpenLayers.js"></script>	  
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
        controls: [new OpenLayers.Control.Attribution(), new OpenLayers.Control.LayerSwitcher(), new OpenLayers.Control.PanZoomBar(), new OpenLayers.Control.Navigation()]
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
	

	</script>
		
		
	</head>
  <body onLoad="init();addmarker(<?php echo $addmarker ?>);" >
  <img src="geocodr.png" width="396" height="90"><br />
  <?php echo $errmsg ?> 
  <form action="search.php" method="get">
  <input type="text" name="find" id="find" value="<?php echo $place?>" style="margin:5px"  >
  <input type="submit" value="Find!" style="margin:5px" >
  </form>
   <div id="map"></div>

</body>
</html>

