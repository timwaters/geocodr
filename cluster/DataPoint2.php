<?php

class DataPoint2 {


/* 
    This class represents a candidate for Cluster analysis. A candidate must have
    a name and two independent variables on the basis of which it is to be clustered.
    A Data Point must have two variables and a name. A Vector of  Data Point object
    is fed into the constructor of the JCA class. JCA and DataPoint are the only
    classes which may be available from other packages.
    @author Shyam Sivaraman
    @version 1.0
    @see JCA
    @see Cluster */



  	public  $mX,$mY;
    //private	 $mObjName;
	public  $mObjName;
     public  $mCluster;
    public  $mEuDt;

 	public function DataPoint2($x, $y, $name) {
        $this->mX = $x;
        $this->mY = $y;
        $this->mObjName = $name;
        $this->mCluster = "";
    }

    	public function setCluster($cluster) {
        $this->mCluster = $cluster;
        $this->calcEuclideanDistance();
	
    }

   public function calcEuclideanDistance() { 
    $cent = $this->mCluster->getCentroid();
	
    //called when DP is added to a cluster or when a Centroid is recalculated.
        $this->mEuDt = sqrt(pow(($this->mX - $cent->getCx()), 2) + pow(($this->mY - $cent->getCy()), 2));
    }

//£c = centroid
    public function testEuclideanDistance($c) {
        return sqrt(pow(($this->mX - $c->getCx()), 2) + pow(($this->mY - $c->getCy()), 2));
    }

    public function getX() {
        return $this->mX;
    }

    public function getY() {
        return $this->mY;
    }

    public function getCluster() {
        return $this->mCluster;
    }

    public function getCurrentEuDt() {
        return $this->mEuDt;
    }

    public function getObjName() {
        return $this->mObjName;
    }




}

?>