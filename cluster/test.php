<?php
require_once 'DataPoint.php';
require_once 'JCA.php';



$datapoints = array();
		
//array_push($datapoints, new DataPoint(-22.001,21,"p53"), new DataPoint(19,20,"bcl2"), new DataPoint(-21.001,20,"p55") );
//array_push($datapoints, new DataPoint(18,22,"fas"), new DataPoint(1,3,"amylase"), new DataPoint(3,2,"maltase") );
array_push($datapoints, new DataPoint(54.152684,-0.776939,"confusing the enemy"));
array_push($datapoints, new DataPoint(53.801056,-1.539351,"traffic"));
array_push($datapoints, new DataPoint(53.911767,-1.502208,"Harewood 1 4.10.06"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"Misty Sunrise sans zombies"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"Misty Winter Sunrise"));
array_push($datapoints, new DataPoint(53.783447,-1.542781,"leeds at night"));
array_push($datapoints, new DataPoint(53.911767,-1.502208,"Harewood HDR 01"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"Infra-Red Woods"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"Misty Sunrise"));
array_push($datapoints, new DataPoint(53.752228,-1.556179,"silhouette"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"Winter Trees by Night"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"Misty Morning"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"Snow-blasted trees"));
array_push($datapoints, new DataPoint(51.24935,0.62828,"leeds castle"));
array_push($datapoints, new DataPoint(53.911767,-1.502208,"Harewood 2 4.10.06"));
array_push($datapoints, new DataPoint(53.797295,-1.550009,"traffic2"));
array_push($datapoints, new DataPoint(53.783485,-1.542851,"leeds traffic"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"Misty Woodland"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"Snow By Night"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"Misty Morning"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"Anyone for Golf?"));
array_push($datapoints, new DataPoint(53.799233,-1.544191,"But Nigel,...."));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"Misty Winter Sunrise"));
array_push($datapoints, new DataPoint(53.790958,-1.530999,"Hall of Steel"));
array_push($datapoints, new DataPoint(53.79913,-1.547871,"trees come in threes"));
array_push($datapoints, new DataPoint(53.911767,-1.502208,"Harewood 4 4.10.06"));
array_push($datapoints, new DataPoint(53.790958,-1.530999,"Hall of Steel"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"middleton woods"));
array_push($datapoints, new DataPoint(53.911767,-1.502208,"Harewood 3 4.10.06"));
array_push($datapoints, new DataPoint(53.790958,-1.530999,"General Electric M134 Minigun"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"middleton woods, sunset"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"spring"));
array_push($datapoints, new DataPoint(53.839887,-1.502844,"Red Flower"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"Winter Trees by Night"));
array_push($datapoints, new DataPoint(53.799655,-1.544065,"Windows"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"Goodbye Autumn"));
array_push($datapoints, new DataPoint(53.612323,-2.605723,"Adlington"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"Misty Morning"));
array_push($datapoints, new DataPoint(53.687374,-2.611887,"Wheelton Top Lock - the marina"));
array_push($datapoints, new DataPoint(53.8,-1.5434,"Giant bauble"));
array_push($datapoints, new DataPoint(53.5553,-2.594393,"Our Usual"));
array_push($datapoints, new DataPoint(53.795478,-1.549621,"Windows"));
array_push($datapoints, new DataPoint(53.8,-1.5465,"Looking up at The Light"));
array_push($datapoints, new DataPoint(53.803037,-1.548989,"leeds civic hall"));
array_push($datapoints, new DataPoint(53.8695,-1.6597,"Flags and rainbow"));
array_push($datapoints, new DataPoint(53.611878,-2.607155,"Botany Bay"));
array_push($datapoints, new DataPoint(53.795765,-1.601257,"Armley cordon"));
array_push($datapoints, new DataPoint(51.246175,0.629053,"The Great Waters and Leeds Castle"));
array_push($datapoints, new DataPoint(53.75618,-1.555497,"middleton woods"));

$NumClusters = 3; //there has to be more clusters than points ;)
$NumIterations = 100; //about the number of datapoints
       $jca = new JCA($NumClusters,$NumIterations ,$datapoints);
        $jca->startAnalysis();
        $clusters = $jca->getClusterOutput();
      	$largestClust = 0;
        $largestClustSize = 0;
         
		 for ($i = 0; $i < count($clusters); $i++){
	$cX = (float)$jca->getCluster($i)->getCentroid()->getCx();
	$cY = (float)$jca->getCluster($i)->getCentroid()->getCy();
echo $cX ." ".$cY;
}
		 
        for ($i=0; $i<count($clusters); $i++){
            $tempCluster = $clusters[$i];
            
           if (count($tempCluster) > $largestClustSize) {
           $largestClustSize =  count($tempCluster);
           $largestClust = $i;
           }
            
            echo "-----------Cluster ".$i." Size= ". count($tempCluster) . " --------- <br />";
           
		   foreach ($tempCluster as $clusterObj){
		   	echo $clusterObj->getObjName() . "[" . $clusterObj->getX() . "," . $clusterObj->getY() . "]<br /><br />" ;
		   } 
		 
        }

       
      $cX = $jca->getCluster($largestClust)->getCentroid()->getCx();
      $cY = $jca->getCluster($largestClust)->getCentroid()->getCy();
       
    echo "<hr>";
        echo("Cluster ".$largestClust . " with ". $largestClustSize. " points, is the biggest. X= ".$cX." Y= ".$cY );
  

?>
