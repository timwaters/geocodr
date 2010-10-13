<?php 
//Cluster code converted from Shyam Sivaraman's java http://www.sourcecodesworld.com/source/show.asp?ScriptID=807
//
class Cluster {
    private $mName;
    private $mCentroid;
    private $mSumSqr;
    private $mDataPoints;

    public function Cluster($name) {
        $this->mName = $name;
        $this->mCentroid = ""; //will be set by calling setCentroid()
        $this->mDataPoints = array();
    }

    public function setCentroid($c) {
        $this->mCentroid = $c;
    }
//public Centroid getCentroid() {
    public function getCentroid() {
        return $this->mCentroid;
    }

    public function addDataPoint($dp) { //called from CAInstance
//	echo("..". $dp->mObjName."|");
     $dp->setCluster($this); //initiates a inner call to
	
	$dp->calcEuclideanDistance();
	
		array_push($this->mDataPoints, $dp);
        $this->calcSumOfSquares();
    }





    public function removeDataPoint($dpa) {
//	echo"removeDataPoint dp = dpname=".$dpa->mObjName ." cluster name=" . $this->mName ."<br />" ;
	$key = array_search($dpa, $this->mDataPoints, true); 
	//echo "KEY=".$key;
	unset ($this->mDataPoints[$key]);
	//reindex
	$temp_array = array_values($this->mDataPoints);
	$this->mDataPoints = $temp_array;
	
	//unset($this->mDataPoints[$dp]);
       // $this->mDataPoints->removeElement($dp);
        $this->calcSumOfSquares();
    }

    public function getNumDataPoints() {

        return count($this->mDataPoints);
    }

    public  function getDataPoint($pos) {
        return $this->mDataPoints[$pos];
    }

    public function calcSumOfSquares() { //called from Centroid
	
        $size = count($this->mDataPoints);
		
        $temp = 0;
	//	echo "start for..";
        for ($i = 0; $i < $size; $i++) {
	//	echo "<br />i=".$i . "...";
	//echo "DATaPOINT dps size = ".$size ."  ";
		//print_r($this->mDataPoints[$i]);	echo "<br />";
	//	print_r($this->mDataPoints[$i]->getCurrentEuDt());
	
            $temp = $temp + $this->mDataPoints[$i]->getCurrentEuDt();
        }
        $this->mSumSqr = $temp;
    }

    public function getSumSqr() {
        return $this->mSumSqr;
    }

    public function getName() {
        return $this->mName;
    }

    public function getDataPoints() {
        return $this->mDataPoints;
    }

}