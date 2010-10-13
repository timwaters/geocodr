<?php 
require_once 'Flickr/APIfix.php';
//require_once XN_DIR_PRIVATE.'/config.php';
require_once 'cluster/DataPoint.php';
require_once 'cluster/JCA.php';

//search required
 $search; //search query required
 $searchmode = "tags"; // tags or text text = title, description or tags
 $sortmode = "interestingness-desc"; //relevance  date-posted-desc
 $bbox; //optional
 $numresults = 50; //more will take longer, but possibly better results;
 $clusters = 3;// number of clusters. for tweaking
  $place = $_GET[place];
  $searchmode = $_GET[searchmode];
  
  $place = (isset ($_GET['place'])) ? $_GET['place'] : die("No search, use search=mysearch");
  
  $searchmode = (isset ($_GET['searchmode'])) ? $_GET['searchmode'] : $searchmode = "tags";
  $sortmode = (isset ($_GET['sortmode'])) ? $_GET['sortmode'] : $searchmode = "interestingness-desc";
  $bbox = (isset ($_GET['bbox'])) ? $_GET['bbox'] : $bbox = "-180.00000,-90.00000,180.00000,90.00000";
  $numresults = (isset ($_GET['numresults'])) ? $_GET['numresults'] : $numresults = 50;
  $clusters = (isset ($_GET['clusters'])) ? $_GET['clusters'] : $clusters = 3;
  
  
	

    header("Content-Type: text/xml");
    echo '<?xml version="1.0" encoding="utf-8" ?>'.chr(13);
    echo '  <item>'.chr(13);
    echo '    <longitude>'.$lon.'</longitude>'.chr(13);
    echo '    <latitude>'.$lat.'</latitude>'.chr(13);
	echo '    <place>'.$place.'</place>'.chr(13);
    echo '  </item>'.chr(13);

?>
