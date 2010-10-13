<?php

/**
 * This class represents the Centroid for a Cluster. The initial centroid is calculated
 * using a equation which divides the sample space for each dimension into equal parts
 * depending upon the value of k.
 * @author Shyam Sivaraman
 * @version 1.0
 * @see Cluster
 */

class Centroid {
    private $mCx, $mCy;
    private $mCluster;

    public function Centroid($cx, $cy) {
        $this->mCx = $cx;
        $this->mCy = $cy;
    }

    public function calcCentroid() { //only called by CAInstance
	//echo"mCluster=";
	//print_r($this->mCluster);
        $numDP = $this->mCluster->getNumDataPoints();
		//echo "NUM = {$numDP}";
        $tempX = 0;
		$tempY = 0;
        $i;
        //caluclating the new Centroid
        for ($i = 0; $i < $numDP; $i++) {
            $tempX = $tempX + $this->mCluster->getDataPoint($i)->getX(); 
            //total for x
            $tempY = $tempY + $this->mCluster->getDataPoint($i)->getY(); 
            //total for y
        }
        $this->mCx = $tempX / $numDP;  //divide by zero?
        $this->mCy = $tempY / $numDP;
        //calculating the new Euclidean Distance for each Data Point
        $tempX = 0;
        $tempY = 0;
        for ($i = 0; $i < $numDP; $i++) {
            $this->mCluster->getDataPoint($i)->calcEuclideanDistance();
        }
        //calculate the new Sum of Squares for the Cluster
        $this->mCluster->calcSumOfSquares();
    }

    public function setCluster($c) {
        $this->mCluster = $c;
		//print_r($this->mCluster);
    }

    public function getCx() {
        return $this->mCx;
    }

    public function getCy() {
        return $this->mCy;
    }

    public function getCluster() {
        return $this->mCluster;
    }

}