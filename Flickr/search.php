<?php
//caching: http://phpflickr.com/docs/?page=caching


require_once 'phpFlickr-2.1.0/phpFlickr.php';
require_once '../cluster/DataPoint_flickr.php';
require_once '../cluster/JCA.php';

require_once('../JSON/JSON.php');
//$MY_FLICKR_API_KEY = '1b5c444be51e50c05eeeed1b72b8747f';  //CHANGE THIS!
$MY_FLICKR_API_KEY = 'whatever';

  //search required
  //slight adaption to give back more info re: clusters
  $format = (isset ($_GET['format'])) ? $_GET['format'] : $format = "html"; //or json  xml html
  //$format = "json";
  $showpoints =(isset ($_GET['showpoints'])) ? $_GET['showpoints'] : $showpoints = "false"; 
  $showclusters = (isset ($_GET['showclusters'])) ? $_GET['showclusters'] : $showclusters = "false";
  $place = (isset ($_GET['find'])) ? $_GET['find'] :  $emptysearch = true;

 
  
  $searchmode = (isset ($_GET['searchmode'])) ? $_GET['searchmode'] : $searchmode = "tags";
  $sortmode = (isset ($_GET['sortmode'])) ? $_GET['sortmode'] : $sortmode = "interestingness-desc";
  $bbox = (isset ($_GET['bbox'])) ? $_GET['bbox'] : $bbox = "-180.00000,-90.00000,180.00000,90.00000";
  $numresults = (isset ($_GET['numresults'])) ? $_GET['numresults'] : $numresults = 50;
  $numclusters = (isset ($_GET['numclusters'])) ? $_GET['numclusters'] : $numclusters = 3;
  

   
  $key = $MY_FLICKR_API_KEY;
  $tryagain = 0;
  if (!$emptysearch)  { 
  geocode($key, $place, $searchmode, $sortmode, $format, $bbox, $numresults, $numclusters,$tryagain,$showclusters, $showpoints);
  } else {
  include ("do_html.php");
  }
  
 function geocode($key, $place, $searchmode, $sortmode, $format, $bbox, $numresults, $numclusters, $tryagain, $showclusters, $showpoints ) {
$error = "1";
  
 //$f = new phpFlickr('1b5c444be51e50c05eeeed1b72b8747f');
	

  $f = new phpFlickr('whatever');

//$api = new Flickr_API(array( 'api_key'  => $key, ));

$resp = $f->photos_search(array( 
			$searchmode => $place, 'bbox' => $bbox, 'per_page' => $numresults, 'sort' => $sortmode, 'tag_mode' => 'all', 'extras' => 'geo',
		));

if ($resp){

//print_r($resp);

$cnt ="0";
$datapoints = array();

foreach ($resp['photo'] as $photo) {

		$thumbnail = "http://static.flickr.com/{$photo['server']}/{$photo['id']}_{$photo['secret']}_t.jpg";
		$photoLink = "http://www.flickr.com/photos/{$photo['owner']}/{$photo['id']}";
		$photolat = $photo['latitude'];
		$photolon = $photo['longitude'];
array_push($datapoints, new DataPoint_flickr((float)$photolat,(float)$photolon,$photo['title'], $thumbnail, $photoLink));
		$cnt++;	
}

if ($cnt < $numclusters) {

$error = "Sorry no photos found for ".$place ;
} else {



$jca = new JCA($numclusters,$numresults * 2 ,$datapoints);
$jca->startAnalysis();
$clusters = $jca->getClusterOutput();

	$largestClust = 0;
    $largestClustSize = 0;
         
        for ($i=0; $i<count($clusters); $i++){
          $tempCluster = $clusters[$i];
            
           if (count($tempCluster) > $largestClustSize) {
			   $largestClustSize =  count($tempCluster);
			   $largestClust = $i;
           } //if
            
		}//for
   
	$cX = $jca->getCluster($largestClust)->getCentroid()->getCx();
    $cY = $jca->getCluster($largestClust)->getCentroid()->getCy();
} //if cnt > 3


if ($format == "json") {
include ("do_json.php");
}
else if ($format == "xml") {
include ("do_xml.php");
}
else if ($format == "html") {
include ("do_html.php");
}

// if not resp
}else {

	$code = $api->getErrorCode();
	$message = $api->getErrorMessage();
		if ($code != 0 OR $tryagain > 2){
				if ($format == "json") {
				include ("do_json.php");
				}
				else if ($format == "xml") {
				include ("do_xml.php");
				}
				else if ($format == "html") {
				include ("do_html.php");
				}
		}
			
				//call function again for 2 times...
				$tryagain++;
				geocode($key, $place, $searchmode, $sortmode, $format, $bbox, $numresults, $numclusters, $tryagain, $showclusters, $showpoints);
				
				}  //end geocode function
  }
  
  
function getBBOX($cluster){
	$xs = array();
	$ys = array();
	
	for ($b=0;$b<count($cluster);$b++) {
	//echo $cluster[$b]->getX();

	$xx = $cluster[$b]->getX();
	$yy =$cluster[$b]->getY();
				//$xs[] = $xx;
				//$xy[] = $xy;
				array_push($xs, $xx);
				array_push($ys, $yy);
		}
	$bbox = min($xs).','. min($ys).','. max($xs).','.max($ys);
	return $bbox;
}
?>

