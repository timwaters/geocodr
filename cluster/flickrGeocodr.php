<?php
//require_once XN_DIR_PRIVATE.'/config.php';
$MY_FLICKR_API_KEY = 'watever';
require_once 'Flickr/APIfix.php';

require_once 'cluster/DataPoint.php';
require_once 'cluster/JCA.php';

//some constants
$SHOWTAGS = "";
$TAG_MODE = 'any';
$FRMtags ="";
$sort = "";
$DOEXTRA= false;

 //tags showtags bbox numresults
if (isset($_GET['desc']) ) {
  $Fdesc = urldecode($_GET['desc']);
  if ($Fdesc == "true") $DOEXTRA = true;
}
if (isset($_GET['oname']) ) {
  $Oname = urldecode($_GET['oname']);
 if ($Oname == "true") $DOEXTRA = true;
}

if (isset($_GET['showtags']) ) {
  $SHOWTAGS = $_GET['showtags'];
  if ($SHOWTAGS == "true") $DOEXTRA = true;
}

if (isset($_GET['sort']) ) {
  $sort = urldecode($_GET['sort']);
}

//tags showtags bbox numresults
if (isset($_GET['tags']) ) {
  $FRMtags = urldecode($_GET['tags']);
}

if (isset($_GET['andor']) ) {
  $TAG_MODE = $_GET['andor'];
}
 
  if (isset($_GET['bbox']) ) {
  $FRMbbox = $_GET['bbox'];
} 
  if (strlen($FRMbbox ) < 1) {
   $FRMbbox ="-8.359, 47.368, -0.557, 56.284";
}
  
    if (isset($_GET['numresults']) ) {
  $FRMnumresults = $_GET['numresults'];
  }

if ( !function_exists('htmlspecialchars_decode') )
{
   function htmlspecialchars_decode($text)
   {
       return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
   }
}

function escape_quotes($receive) {
   if (!is_array($receive))
       $thearray = array($receive);
   else
       $thearray = $receive;
  
   foreach (array_keys($thearray) as $string) {
       $thearray[$string] = addslashes($thearray[$string]);
       $thearray[$string] = preg_replace("/[\\/]+/","/",$thearray[$string]);
   }
  
   if (!is_array($receive))
       return $thearray[0];
   else
       return $thearray;
}

function removeEmptyLines($string)
{
return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string);
}


$api = new Flickr_API(array( 'api_key'  => $MY_FLICKR_API_KEY, ));


	# call a method
	
	$response = $api->callMethod('flickr.photos.search', array(
			'tags' => $FRMtags, 'bbox' => $FRMbbox, 'per_page' => $FRMnumresults, 'tag_mode' => $TAG_MODE,  'sort' => $sort, 'extras' => 'geo',
		));
		
if ($response){

	


$xmlString = $response;

$xml2 = $xmlString;


$photosColl = $xml2->photos;

//only one node
$photos = $photosColl[0];

$outpu = "";
//print_r($photos);
		?>

<?php 

$cnt ="0";
$datapoints = array();
foreach ($photos as $photo) {

	$thumbnail = "http://static.flickr.com/{$photo['server']}/{$photo['id']}_{$photo['secret']}_t.jpg";
	$photoLink = "http://www.flickr.com/photos/{$photo['owner']}/{$photo['id']}";
    $outpu = $outpu . "<p><a href='$photoLink'><img src='$thumbnail'> {$photo['title']} </a>  ";
	
	$photolat = $photo['latitude'];
	$photolon = $photo['longitude'];
	
//	$popupHTML =  "<a href=\"$photoLink\"><img src=\"$thumbnail\"> <br>{$photo['title']} </a>";
	//$popupHTML = addslashes($popupHTML);

   $date = "";
	$desc ="";
	$ownerUsername = "";
		
			
		//$popupHTML = $popupHTML . $ownerUsername. $descAndDate  ;
		//$popupHTML = addslashes($popupHTML);


array_push($datapoints, new DataPoint((float)$photolat,(float)$photolon,$photo['title']));



	
	
			
///			
$cnt++;	



} //foreach photo
//echo $outpu;

///	
//cluster stuff
$NumClusters = 3; //there has to be more clusters than points ;)
$NumIterations = 100; //about the number of datapoints
$jca = new JCA($NumClusters,$NumIterations ,$datapoints);
$jca->startAnalysis();
$clusters = $jca->getClusterOutput();

 




//CLUSTERS
		$largestClust = 0;
        $largestClustSize = 0;
         
        for ($i=0; $i<count($clusters); $i++){
            $tempCluster = $clusters[$i];
            
           if (count($tempCluster) > $largestClustSize) {
           $largestClustSize =  count($tempCluster);
           $largestClust = $i;
           }
            
			}
            echo "-----------Cluster ".$i." Size= ". count($tempCluster) . " --------- <br />";
           
		 //  foreach ($tempCluster as $clusterObj){
		//   	echo $clusterObj->getObjName() . "[" . $clusterObj->getX() . "," . $clusterObj->getY() . "]<br /><br />" ;
		 //  } 
		 
       
	$cX = $jca->getCluster($largestClust)->getCentroid()->getCx();
      $cY = $jca->getCluster($largestClust)->getCentroid()->getCy();
       
   		 echo "<hr>";
        echo("Cluster ".$largestClust . " with ". $largestClustSize. " points, is the biggest. X= ".$cX." Y= ".$cY );
	//CLUSTERS	
}else{
		$code = $api->getErrorCode();
						$message = $api->getErrorMessage();
						echo 'Whoops! '  . $message . ' :: Code: ' . $code . ' <br />';
						die("Sorry, but theres been an Error. Most likely it cannot connect to Flickr, try Reloading and try again, it often helps. If not, drop me a note via the ning bar.");
	}
