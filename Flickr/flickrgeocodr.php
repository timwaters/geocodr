<?php 
//http://phpflickr.com/docs/?page=caching

require_once 'phpFlickr-2.1.0/phpFlickr.php';


require_once '../cluster/DataPoint.php';
require_once '../cluster/JCA.php';
$MY_FLICKR_API_KEY = 'whatever';
//$MY_FLICKR_API_KEY = '1b5c444be51e50c05eeeed1b72b8747f'; 
 
  $format = (isset ($_GET['format'])) ? $_GET['format'] : $format = "xml"; //or json
  
  $place = (isset ($_GET['place'])) ? $_GET['place'] : die("No place to search, use place=mysearch");
  
  $searchmode = (isset ($_GET['searchmode'])) ? $_GET['searchmode'] : $searchmode = "tags";
  $sortmode = (isset ($_GET['sortmode'])) ? $_GET['sortmode'] : $sortmode = "interestingness-desc";
  $bbox = (isset ($_GET['bbox'])) ? $_GET['bbox'] : $bbox = "-180.00000,-90.00000,180.00000,90.00000";
  $numresults = (isset ($_GET['numresults'])) ? $_GET['numresults'] : $numresults = 50;
  $numclusters = (isset ($_GET['numclusters'])) ? $_GET['numclusters'] : $numclusters = 3;
  
  $key = $MY_FLICKR_API_KEY;
  $tryagain = 0;
 geocode($key, $place, $searchmode, $sortmode, $format, $bbox, $numresults, $numclusters,$tryagain);

function geocode($key, $place, $searchmode, $sortmode, $format, $bbox, $numresults,$numclusters, $tryagain ) {
$error = "1";



  $f = new phpFlickr('whatever');


	$resp = $f->photos_search(array( 
			$searchmode => $place, 'bbox' => $bbox, 'per_page' => $numresults, 'sort' => $sortmode, 'tag_mode' => 'all', 'extras' => 'geo',
		));		

if ($resp){

$cnt ="0";
$datapoints = array();

foreach ($resp['photo'] as $photo) {
		
		$photolat = $photo['latitude'];
		$photolon = $photo['longitude'];
		array_push($datapoints, new DataPoint((float)$photolat,(float)$photolon,$photo['title']));
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
           }
            
		}//for
   
	$cX = $jca->getCluster($largestClust)->getCentroid()->getCx();
    $cY = $jca->getCluster($largestClust)->getCentroid()->getCy();
} //if cnt > 3
	
if ($format != "json") {
    header("Content-Type: text/xml");
    echo '<?xml version="1.0" encoding="utf-8" ?>'.chr(13);
    echo '  <item>'.chr(13);
    echo '    <latitude>'.$cX .'</latitude>'.chr(13);
	echo '    <longitude>'.$cY.'</longitude>'.chr(13);
	echo '    <place>'.$place.'</place>'.chr(13);
	echo '    <error>'.$error.'</error>'.chr(13);
    echo '  </item>'.chr(13);
} else 
		{//json  much quicker!!
		header("Content-Type: text/javascript");
		echo '{  "latitude": "'.$cX.'",  "longitude": "'.$cY.'",  "place": "'.$place.'", "error": "'.$error.'" } ';
		
		}

} else{ //if no response from flickr
		
	$code = $api->getErrorCode();
	$message = $api->getErrorMessage();
		if ($code != 0 OR $tryagain > 2){
	//	if ($code == 0 ){
		
				$error =  $message . ' : Code: ' . $code . ' Tried '.$tryagain.'times.';
				if ($format != "json") {
				header("Content-Type: text/xml");
				echo '<?xml version="1.0" encoding="utf-8" ?>'.chr(13);
				echo '<item>'.chr(13);
				echo '    <latitude></latitude>'.chr(13);
				echo '    <longitude></longitude>'.chr(13);
				echo '    <error>'.$error.'</error>'.chr(13);
				echo '  </item>'.chr(13);
					} else 
				{//json 
				header("Content-Type: text/javascript");
				echo '{  "error": "'.$error.'" } ';
				
				}
				
				}else {
			
				//call function again for 2 times...
				$tryagain++;
				geocode($key, $place, $searchmode, $sortmode, $format, $bbox, $numresults, $numclusters, $tryagain);
				
				} 
	}


}//function

?>
