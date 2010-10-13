<?php
require_once '../cluster/DataPoint2.php';
class DataPoint_flickr extends Datapoint2 {


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



	private $mthumbnail;
	private $mlink;
public function DataPoint_flickr($x, $y, $name, $thumbnail, $link) {
	
//public function __construct($x, $y, $name, $thumbnail, $link) {

        $this->mX = $x;
        $this->mY = $y;
        $this->mObjName = $name;
        $this->mCluster = "";
		$this->mthumbnail = $thumbnail;
		$this->mlink = $link;
		
    }

    	

    public function getObjThumb() {
        return $this->mthumbnail;
    }

public function getObjLink() {
        return $this->mlink;
    }
	
	




}

?>