<?php


header("Content-Type: text/xml");

if ($code != 0 OR $tryagain > 2){
		
				$error =  $message . ' : Code: ' . $code . ' Tried '.$tryagain.'times.';
			 echo '<?xml version="1.0" encoding="utf-8" ?>'.chr(13);
 echo '<clusters>'.chr(13);	
 echo '    <error>'.$error.'</error>'.chr(13);
  echo '</clusters>'.chr(13);	
				} else {

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
	 

	} //else error
	}
	 echo '    <error>'.$error.'</error>'.chr(13);
	  echo '</clusters>'.chr(13);	

	//??
	
//??	