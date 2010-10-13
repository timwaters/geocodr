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
  $format = (isset ($_GET['format'])) ? $_GET['format'] : $format = "xml"; //or json
  //$format = "json";
  $showpoints =(isset ($_GET['showpoints'])) ? $_GET['showpoints'] : $showpoints = "false"; 
  $showclusters = (isset ($_GET['showclusters'])) ? $_GET['showclusters'] : $showclusters = "false";
  $place = (isset ($_GET['place'])) ? $_GET['place'] : die("No place to search, use place=mysearch");
  $searchmode = (isset ($_GET['searchmode'])) ? $_GET['searchmode'] : $searchmode = "tags";
  $sortmode = (isset ($_GET['sortmode'])) ? $_GET['sortmode'] : $sortmode = "interestingness-desc";
  $bbox = (isset ($_GET['bbox'])) ? $_GET['bbox'] : $bbox = "-180.00000,-90.00000,180.00000,90.00000";
  $numresults = (isset ($_GET['numresults'])) ? $_GET['numresults'] : $numresults = 50;
  $numclusters = (isset ($_GET['numclusters'])) ? $_GET['numclusters'] : $numclusters = 3;
  
  
   
  $key = $MY_FLICKR_API_KEY;
  $tryagain = 0;
  geocode($key, $place, $searchmode, $sortmode, $format, $bbox, $numresults, $numclusters,$tryagain,$showclusters, $showpoints);


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



function geocode($key, $place, $searchmode, $sortmode, $format, $bbox, $numresults, $numclusters, $tryagain, $showclusters, $showpoints ) {
$error = "1";
  
 //$f = new phpFlickr('1b5c444be51e50c05eeeed1b72b8747f');
	

  $f = new phpFlickr('whatever');

//$api = new Flickr_API(array( 'api_key'  => $key, ));

$resp = $f->photos_search(array( 
			$searchmode => $place, 'bbox' => $bbox, 'per_page' => $numresults, 'sort' => $sortmode, 'tag_mode' => 'all', 'extras' => 'geo',
		));

if ($resp){



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
           }
            
		}//for
   
	$cX = $jca->getCluster($largestClust)->getCentroid()->getCx();
    $cY = $jca->getCluster($largestClust)->getCentroid()->getCy();
} //if cnt > 3
	
	
	
if ($format != "json") {


header("Content-Type: text/xml");



 echo '<?xml version="1.0" encoding="utf-8" ?>'.chr(13);
 echo '<clusters>'.chr(13);	
	if ($showclusters == "true"){
	
	
	
	for ($a=0; $a<count($clusters); $a++){	 
	
	$bbox = getBBOX($clusters[$a]);
	echo '<cluster>'.chr(13);
	echo '    <type>'."cluster".'</type>'.chr(13);
	echo '    <id>'.$a.'</id>'.chr(13);
	echo '    <pointcount>'.count($clusters[$a]).'</pointcount>'.chr(13);
	echo '    <latitude>'.$jca->getCluster($a)->getCentroid()->getCx() .'</latitude>'.chr(13);
	echo '    <longitude>'.$jca->getCluster($a)->getCentroid()->getCy().'</longitude>'.chr(13);
	echo '    <place>'.$place.'</place>'.chr(13);
	echo '    <bbox>'.$bbox.'</bbox>'.chr(13);

	
	
		if ($showpoints == "true") {
			echo '    <points>'.chr(13);
				for ($b=0;$b<count($clusters[$a]);$b++) {
			
				$picLink = $clusters[$a][$b]->getObjLink();
				$picThum = $clusters[$a][$b]->getObjThumb();
				$picX = $clusters[$a][$b]->getX();
				$picY = $clusters[$a][$b]->getY();
					
					echo '    <point>'.chr(13);
						echo '    <type>'."point".'</type>'.chr(13);
						echo '    <link>'.$picLink.'</link>'.chr(13);	
						echo '    <thumnail>'.$picThum.'</thumnail>'.chr(13);									
						echo '    <latitude>'.$picX.'</latitude>'.chr(13);
			 			echo '    <longitude>'.$picY.'</longitude>'.chr(13);
					echo '    </point>'.chr(13);
			}//for cluster points 
				echo '    </points>'.chr(13);
		} //if showpoints
		echo '</cluster>'.chr(13);
	} //for show clusters

	 } else { //just show main cluster}
	 $bbox = getBBOX($clusters[$largestClust]);
	 echo '<cluster>'.chr(13);
	echo '    <type>'."cluster".'</type>'.chr(13);
	echo '    <id>'.$largestClust.'</id>'.chr(13);
	echo '    <pointcount>'.$largestClustSize.'</pointcount>'.chr(13);
	echo '    <latitude>'.$cX.'</latitude>'.chr(13);
	echo '    <longitude>'.$cY.'</longitude>'.chr(13);
	echo '    <place>'.$place.'</place>'.chr(13);
	echo '    <bbox>'.$bbox.'</bbox>'.chr(13);
	echo '</cluster>'.chr(13);
	 

	} //else show main cluster
	 echo '    <error>'.$error.'</error>'.chr(13);
	  echo '</clusters>'.chr(13);	

	//??
	
//??	


} else //is JSON
		
	{//json  much quicker!!
			
	
	header("Content-Type: text/javascript");
	$json = new Services_JSON();
	
	
	if ($showclusters == "true"){
		$clusterArray = array();
		$pointArray  = array();
	
	
	 
	for ($a=0; $a<count($clusters); $a++){	 	
	
	$bbox = getBBOX($clusters[$a]);
	$clusterArray[$a] = array("type"=>"cluster", "id"=>$a, "pointcount"=>count($clusters[$a]), 
	"latitude"=>$jca->getCluster($a)->getCentroid()->getCx(),  
	"longitude"=>$jca->getCluster($a)->getCentroid()->getCy(),
	"bbox"=>$bbox, "place"=>$place);
	
	
		if ($showpoints == "true") {
	
			for ($b=0;$b<count($clusters[$a]);$b++) {
			
				$picLink = $clusters[$a][$b]->getObjLink();
				$picThum = $clusters[$a][$b]->getObjThumb();
				$picX = $clusters[$a][$b]->getX();
				$picY = $clusters[$a][$b]->getY();
				$pointArray[$b] = array("type"=>"point", "link"=>$picLink, "thumnail"=>$picThum, 
				"latitude"=>$picX, "longitude"=>$picY); 
	
			}//for cluster points 
			array_push($clusterArray[$a], $pointArray);
		
		} //if showpoints
	
	
	}// for 
		 
	
		
		 
			} else {  //showclusters
	//just show the main cluster
	$bbox = getBBOX($clusters[$largestClust]);
	$clusterArray = array("type"=>"cluster", "id"=>$largestClust, "pointcount"=>$largestClustSize, 
	"latitude"=>$cX,  
	"longitude"=>$cY,
	"bbox"=>$bbox, "place"=>$place);
	
		}
			
	$jsonArr = array("clusters"=>$clusterArray, "error"=>$error);
	$output = $json->encode($jsonArr); 
	//print($output); 	
	
	echo($output);	
			
			
	} //json


} else { //if no response from flickr or ( if ($cnt < $numclusters) { )
		
	$code = $api->getErrorCode();
	$message = $api->getErrorMessage();
		if ($code != 0 OR $tryagain > 2){
		
				$error =  $message . ' : Code: ' . $code . ' Tried '.$tryagain.'times.';
				if ($format != "json") {
			/* 	header("Content-Type: text/xml");
				echo '<?xml version="1.0" encoding="utf-8" ?>'.chr(13);
				echo '<item>'.chr(13);
				echo '    <latitude></latitude>'.chr(13);
				echo '    <longitude></longitude>'.chr(13);
				echo '    <error>'.$error.'</error>'.chr(13);
				echo '  </item>'.chr(13); */
				 echo '<?xml version="1.0" encoding="utf-8" ?>'.chr(13);
 echo '<clusters>'.chr(13);	
 echo '    <error>'.$error.'</error>'.chr(13);
  echo '</clusters>'.chr(13);	
 
					} else 
				{//json 
				header("Content-Type: text/javascript");
				echo '{  "error": "'.$error.'" } ';
				
				}
				
				}else {
			
				//call function again for 2 times...
				$tryagain++;
				geocode($key, $place, $searchmode, $sortmode, $format, $bbox, $numresults, $numclusters, $tryagain, $showclusters, $showpoints);
				
				}
	}



}//function


	

?>
