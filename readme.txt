Note:
You need a Flickr API key for the /Flickr/flickrgeocodr.php
comment out this line:
// require_once '../xn_private/config.php';
 $MY_FLICKR_API_KEY = '1ascfascasc5eeeed1asc7f';

for the geocodr sample application, you need to change the Yahoo app key (if you want to show yahoo!
maps on the openlayers map)
   <script src="http://api.maps.yahoo.com/ajaxymap?v=3.0&appid=geothings"></script>

the sample application uses Dojo for the xml calls.


Geocodr by tim waters

Idea inspired by Mikel Maron


Cluster code converted from Shyam Sivaraman's java http://www.sourcecodesworld.com/source/show.asp?ScriptID=807


http://geocodr.net

It can do things like search within bounding boxes and all sorts! http://geocodr.net/FlickrGeocodrDocs.php

http://geocodr.net/Flickr/search.php?find=happiness&bbox=-77.117,38.7988,-76.807,38.9784
