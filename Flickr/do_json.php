<?php

	header("Content-Type: text/javascript");
	$json = new Services_JSON();
	if ($code != 0 OR $tryagain > 2){
					$error =  $message . ' : Code: ' . $code . ' Tried '.$tryagain.'times.';

				echo '{  "error": "'.$error.'" } ';
			
	} else {
	 
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
			
			
} //else not an error
	