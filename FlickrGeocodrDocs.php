<head>
<title>Flickr Geocodr - finding places from peoples photos</title>
<head>
<p><img src="Flickr/geocodr.png" alt="flickrgeocodr" width="396" height="90"></p>
<p>Flickr Geocodr - inspired by <a href="http://brainoff.com/flickr/geocoder/">Mikel Marons's work</a> <br />
This version uses k-means clustering to geocode places based on peoples public photos!. There are two webservices:</p>
<p><strong>http://geocodr.net/Flickr/flickrgeocodr.php </strong><br />
which can output as REST XML or JSON<br />
<br />
<strong>http://geocodr.net/Flickr/flickrgeocodr_cluster.php </strong><br />
to output more detailed information regarding the clusters themselves in REST XML or JSON. </p>
<p><a href="FlickrGeocodrApp.php">Example application</a> <br /> 
<br />
<strong>Documentation for webservice</strong>:<br>
URL:  http://geocodr.net/Flickr/flickrgeocodr.php?place=<br />
You will need to create a query string. The only required key-value pair is  "place", and it will use the other values on their default settings. <br />
place = the placename to search for. <br />
format=  json|xml (default xml). <br />
searchmode = tags|text (default tags) (text searchs tags, title and description). <br />
sortmode = interestingness-desc|relevance|(or any other values from <a href="http://www.flickr.com/services/api/flickr.photos.search.html">Flickr API</a >)(default interestingness-desc). <br /> bbox = any min_lon, min_lat, max_lon, max_lat  Comma separated, no spaces. Good for limiting to continent / country.(default world -180.0,-90.0,180.0,90.0). <br />
<strong>Tweaks</strong><br />
In addition to changing  searchmode and sortmode, results may change and improve by changing the following:<br />
numresults = 5 to 500 (default 50) Number of photo results to process from Flickr, more will slow things down, but may increase sample size, and accuracy.<br />
numclusters = 2 to 20 (default 3) Number of clusters to use internally, experiment with this, I find 3 and 4 fine.<br />
<p>results:<br />
<br />
&lt;item&gt;<br />
&lt;latitude&gt;51.507715576923&lt;/latitude&gt;<br />
&lt;longitude&gt;-0.146583&lt;/longitude&gt;<br />
&lt;place&gt;london&lt;/place&gt;<br />
&lt;error&gt;1&lt;/error&gt;<br />
&lt;/item&gt;<br />

<br />
<strong>Cluster information in JSON</strong> <strong>(flickrgeocodr_cluster.php</strong>)<br />
http://geocodr.net/Flickr/flickrgeocodr_cluster.php<br />
You can use all the same GET values as above, but there are additional ones:<br />
showpoints = true|false (defaults to false) Returns photos in each cluster (link to photo, and link url) <br />
	showclusters =  true|false (defaults to true) <br />
format = xml or json (default is xml) 
	<br />
	<br />
	Returned XML comes in three flavours, Just the largest cluster, all the clusters and all clusters with point information: </p>
	<pre><br />&lt;clusters&gt;<br />&lt;cluster&gt;<br />&lt;type&gt;cluster&lt;/type&gt;<br />&lt;id&gt;0&lt;/id&gt;<br />&lt;pointcount&gt;26&lt;/pointcount&gt;<br />&lt;latitude&gt;51.507715576923&lt;/latitude&gt;<br />&lt;longitude&gt;-0.146583&lt;/longitude&gt;<br />&lt;place&gt;london&lt;/place&gt;<br />&lt;bbox&gt;51.459134,-0.224028,51.543732,-0.122985&lt;/bbox&gt;<br />&lt;/cluster&gt;<br />&lt;cluster&gt;<br />&lt;type&gt;cluster&lt;/type&gt;<br />&lt;id&gt;1&lt;/id&gt;<br />&lt;pointcount&gt;6&lt;/pointcount&gt;<br />&lt;latitude&gt;51.506104833333&lt;/latitude&gt;<br />&lt;longitude&gt;-0.018274666666667&lt;/longitude&gt;<br />&lt;place&gt;london&lt;/place&gt;<br />&lt;bbox&gt;51.483895,-0.055709,51.527369,-0.001266&lt;/bbox&gt;<br />&lt;/cluster&gt;
	&lt;cluster&gt;<br />&lt;type&gt;cluster&lt;/type&gt;<br />&lt;id&gt;2&lt;/id&gt;<br />&lt;pointcount&gt;18&lt;/pointcount&gt;<br />&lt;latitude&gt;51.506230722222&lt;/latitude&gt;<br />&lt;longitude&gt;-0.098079611111111&lt;/longitude&gt;<br />&lt;place&gt;london&lt;/place&gt;<br />&lt;bbox&gt;51.421085,-0.119519,51.527963,-0.065188&lt;/bbox&gt;<br />&lt;/cluster&gt;
	&lt;error&gt;1&lt;/error&gt;<br />&lt;/clusters&gt;


http://geocodr.net/Flickr/flickrgeocodr_cluster.php?place=london&amp;format=xml&amp;showpoints=true

&lt;clusters&gt;<br />&lt;cluster&gt;<br />&lt;type&gt;cluster&lt;/type&gt;<br />&lt;id&gt;0&lt;/id&gt;<br />&lt;pointcount&gt;26&lt;/pointcount&gt;<br />&lt;latitude&gt;51.507715576923&lt;/latitude&gt;<br />&lt;longitude&gt;-0.146583&lt;/longitude&gt;<br />&lt;place&gt;london&lt;/place&gt;<br />&lt;bbox&gt;51.459134,-0.224028,51.543732,-0.122985&lt;/bbox&gt;<br /><br />&lt;points&gt;<br />&lt;point&gt;<br />&lt;type&gt;point&lt;/type&gt;<br />&lt;link&gt;http://www.flickr.com/photos/33286810@N00/13317153&lt;/link&gt;<br />&lt;thumnail&gt;<br />http://static.flickr.com/11/13317153_ca353f3462_t.jpg<br />&lt;/thumnail&gt;<br />&lt;latitude&gt;51.50679&lt;/latitude&gt;<br />&lt;longitude&gt;-0.142571&lt;/longitude&gt;<br />&lt;/point&gt;<br />&lt;point&gt;<br />&lt;type&gt;point&lt;/type&gt;<br />&lt;link&gt;<br />http://www.flickr.com/photos/87677821@N00/147034740<br />&lt;/link&gt;<br />&lt;thumnail&gt;<br />http://static.flickr.com/52/147034740_a53ad71e81_t.jpg<br />&lt;/thumnail&gt;<br />&lt;latitude&gt;51.543732&lt;/latitude&gt;<br />&lt;longitude&gt;-0.152735&lt;/longitude&gt;<br />&lt;/point&gt;
.
.
.
</pre>
<p><strong>Examples</strong></p><p>
<a href="FlickrGeocodrApp.php">Example application</a> <br />
<br /> 
<br />

Simple geocode query: Where is Yorkshire in the world?<br />
<a href="http://geocodr.net/Flickr/flickrgeocodr.php?place=yorkshire">http://geocodr.net/Flickr/flickrgeocodr.php?place=yorkshire</a>
</p>
<p>


Longer query: Where is Southamption in the UK?<br />
<a href="http://geocodr.net/Flickr/flickrgeocodr.php?place=southampton&bbox=-8.66250,50.06250,2.58750,58.50000&searchmode=text&sortmode=relevance&numresults=100&numclusters=4"> http://geocodr.net/Flickr/flickrgeocodr.php?place=southampton<br/>&bbox=-8.66250,50.06250,2.58750,58.50000&searchmode=text&sortmode=relevance&numresults=100&numclusters=4</a>


</p>
<p>Tim Waters (<a href="http://thinkwhere.wordpress.com">Blog</a>)</p>
