<?php

require_once 'Cluster.php';
require_once 'Centroid.php';

/**

This class is the entry point for constructing Cluster Analysis objects.
Each instance of JCA object is associated with one or more clusters, 
and a Vector of DataPoint objects. The JCA and DataPoint classes are
the only classes available from other packages.
@see DataPoint
 k = how many clusters?
 iter = nbumber of iterations, usually around the number of datapoints
from http://www.codecodex.com/wiki/index.php?title=K-means_cluster_analysis_algorithm
**/

 class JCA { 


  
	private $clusters;
	private $miter;
	private $mDataPoints;
	private $mSWCSS;

//   public JCA(int k, int iter, Vector dataPoints) {
    public function JCA($k, $iter, $dataPoints) {
	$clusters = array();

        for ($i = 0; $i < $k; $i++) {
            $clusters[$i] = new Cluster("Cluster" + $i);
        }
        $this->miter = $iter;
        $this->mDataPoints = $dataPoints;
		
		$this->clusters = $clusters;

    }

    private function calcSWCSS() {
        $temp = 0;
        for ($i = 0; $i < count($this->clusters); $i++) {
            $temp = $temp + $this->clusters[$i]->getSumSqr();
        }
        $this->mSWCSS = $temp;
    }



    public function startAnalysis() {
        //set Starting centroid positions - Start of Step 1
		
		$this->setInitialCentroids();

		/////////
		 /*   int n = 0;
        //assign DataPoint to clusters
        loop1: while (true) {
            for (int l = 0; l < clusters.length; l++) 
            {
                clusters[l].addDataPoint((DataPoint)mDataPoints.elementAt(n));
                n++;
                if (n >= mDataPoints.size())
                    break loop1;
            }
        } */
		/////////
		$n = 0;
		$cn = count($this->mDataPoints);
		
/* 	do {
 
   
for ($l = 0; $l < count($this->clusters); $l++)       {
			
                $this->clusters[$l]->addDataPoint($this->mDataPoints[$n]);
				
                $n++;
    if ($n >= $cn) {
   //    echo "n is too big";
       break;
   }
   
      }//for

} while (0);
 */
/* 
do {
for ($l = 0; $l < count($this->clusters); $l++)  {
			        // echo "AA n= ".$n." l= ".$l;
					    $this->clusters[$l]->addDataPoint($this->mDataPoints[$n]);
					
                $n++;
  
   
      }
//echo "XX n= ".$n." l= ".$l;
} while ($cn > $n);
 */
while (count($this->mDataPoints) > $n) {

//echo "<br /><br />Count dp  = ".count($this->mDataPoints)." n= ".$n. " ... <br />";

for ($l = 0; $l < count($this->clusters); $l++)  {
			    //  echo "<br />n= ".$n." l= ".$l ." ";
					    $this->clusters[$l]->addDataPoint($this->mDataPoints[$n]);
					
                $n++;
				           
    if ($n >= count($this->mDataPoints)) {
       //echo "n is too big";
       break;
   }
  
   
      }


} //while
/* 	//$i = 0;
do {
   echo $i;
} while ($i > 0); */
	
		 
       
        //calculate E for all the clusters
        $this->calcSWCSS();
     
        //recalculate Cluster centroids - Start of Step 2
        for ($i = 0; $i < count($this->clusters); $i++) {
		//print_r($this->clusters[$i]->getCentroid()); //works
		
            $this->clusters[$i]->getCentroid()->calcCentroid();
        }
        
        //recalculate E for all the clusters
        $this->calcSWCSS();

        for ($i = 0; $i < $this->miter; $i++) {
            //enter the loop for cluster 1
            for ($j = 0; $j < count($this->clusters); $j++) {
                for ($k = 0; $k < $this->clusters[$j]->getNumDataPoints(); $k++) {
                
                    //pick the first element of the first cluster
                    //get the current Euclidean distance
					//double tempEuDt = clusters[j].getDataPoint(k).getCurrentEuDt();
                    $tempEuDt = $this->clusters[$j]->getDataPoint($k)->getCurrentEuDt();
                   // Cluster tempCluster = null;
					$tempCluster = "";
                   $matchFoundFlag = false;
                    
                    //call testEuclidean distance for all clusters
                    for ($l = 0; $l < count($this->clusters); $l++) {
                    
                    //if testEuclidean < currentEuclidean then
                        if ($tempEuDt > $this->clusters[$j]->getDataPoint($k)->testEuclideanDistance($this->clusters[$l]->getCentroid())) {
                            $tempEuDt = $this->clusters[$j]->getDataPoint($k)->testEuclideanDistance($this->clusters[$l]->getCentroid());
                            $tempCluster = $this->clusters[$l];
                            $matchFoundFlag = true;
                        }
                        //if statement - Check whether the Last EuDt is > Present EuDt 
                        
                        }
//for variable 'l' - Looping between different Clusters for matching a Data Point.
//add DataPoint to the cluster and calcSWCSS

       if ($matchFoundFlag) {
		$tempCluster->addDataPoint($this->clusters[$j]->getDataPoint($k));
		
		$this->clusters[$j]->removeDataPoint($this->clusters[$j]->getDataPoint($k));
		
                        for ($m = 0; $m < count($this->clusters); $m++) {
                            $this->clusters[$m]->getCentroid()->calcCentroid();
                        } //m

//for variable 'm' - Recalculating centroids for all Clusters

                        $this->calcSWCSS();
                    } //match found
                    
//if statement - A Data Point is eligible for transfer between Clusters.
                }
                //for variable 'k' - Looping through all Data Points of the current Cluster.
            }//for variable 'j' - Looping through all the Clusters.
        }//for variable 'i' - Number of iterations.
		
		
    } //function
	
// public function Vector[] getClusterOutput() {
    public function  getClusterOutput() {
        $v = array();
        for ($i = 0; $i < count($this->clusters); $i++) {
            $v[$i] = $this->clusters[$i]->getDataPoints();
        }
        return $v;
    }


    private function setInitialCentroids() {
    	

        $cx = 0;
		$cy = 0;
        for ($n = 1; $n <= count($this->clusters); $n++) {
            $cx = ((($this->getMaxXValue() - $this->getMinXValue()) / (count($this->clusters) + 1)) * $n) + $this->getMinXValue();
            $cy = ((($this->getMaxYValue() - $this->getMinYValue()) / (count($this->clusters) + 1)) * $n) + $this->getMinYValue();
            $c1 = new Centroid($cx, $cy);
            $this->clusters[$n - 1]->setCentroid($c1);
            $c1->setCluster($this->clusters[$n - 1]);
        } //for
    }

    private function getMaxXValue() {
        $temp;
        $temp = $this->mDataPoints[0]->getX();
        for ($i = 0; $i < count($this->mDataPoints); $i++) {
            $dp = $this->mDataPoints[$i];
			//variable = (condition) ? value-if-true : value-if-false;
            $temp = ($dp->getX() > $temp) ? $dp->getX() : $temp;
        }
        return $temp;
    }

    private function getMinXValue() {
        $temp = 0;
        $temp = $this->mDataPoints[0]->getX();
        for ($i = 0; $i < count($this->mDataPoints); $i++) {
            $dp = $this->mDataPoints[$i];
            $temp = ($dp->getX() < $temp) ? $dp->getX() : $temp;
        }
        return $temp;
    }

    private function getMaxYValue() {
        $temp = 0;
        $temp = $this->mDataPoints[0]->getY();
        for ($i = 0; $i < count($this->mDataPoints); $i++) {
            $dp = $this->mDataPoints[$i];
            $temp = ($dp->getY() > $temp) ? $dp->getY() : $temp;
        }
        return temp;
    }

    private function getMinYValue() {
    $temp = 0;
        $temp = $this->mDataPoints[0]->getY();
        for ($i = 0; $i < count($this->mDataPoints); $i++) {
            $dp = $this->mDataPoints[$i];
            $temp = ($dp->getY() < $temp) ? $dp->getY() : $temp;
        }
        return temp;
    }

    public function getKValue() {
        return count($this->$clusters);
    }

    public function getIterations() {
        return $this->miter;
    }

    public function getTotalDataPoints() {
        return count($this->mDataPoints);
    }

    public function getSWCSS() {
        return $this->mSWCSS;
    }

//public function Cluster getCluster(int pos) {
    public function getCluster($pos) {
        return $this->clusters[$pos];
    }
}

?>