<?php

// edit these elements to fit your data desire

$subreddit = "The_Donald";		// check exact spelling!
$mode = "top";					// (hot|new|rising|controversial|top|gilded)
$timespan = "all";				// if $mode is top or controversial: (hour|day|week|month|year|all)
$poststoget = 1000;


// do not edit anything below this line
//-----------------------------------------------------


// basic script conf
date_default_timezone_set("UTC");
set_time_limit(3600*5);
ini_set("memory_limit","100M");
ini_set("error_reporting",1);


// construct call url
$url = "https://www.reddit.com/r/" . $subreddit . "/" . $mode . "/.json";
$url .= ($mode == "top" || $mode == "controversial") ? "?t=" . $timespan:"";

// set data directory and create if necessary
$workdir = getcwd();
$jsondir = $workdir . "/data_" . $subreddit . "/";

echo "\n";

if (!file_exists($jsondir)) {
	echo "directory does not exist\n";
	if (!mkdir($jsondir)) {
    	die("failed to create folder - make sure the script is allowed to write to its directory");
    } else {
	    echo "directory created\n";
    }
} else {
	echo "directory already exists\n";
}

// prepare output file
$timestamp = date("Y-m-d_H-i");
$fn_output = $jsondir . "list_" . $subreddit . "_" . $mode . "_";
$fn_output .= ($mode == "top" || $mode == "controversial") ? $timespan."_":"";
$fn_output .= $timestamp . ".json";


// let's get some data
$posts = array();
$postcount = 0;
$after = "";

echo "\ngetting data: ";

while($postcount < $poststoget) {
	
	if($after != "") {
		$url = $url . "&after=" . $after;
	}
	
	$data = file_get_contents($url);
	$data = json_decode($data);
	
	$posts = array_merge($posts,$data->data->children);
	$postcount = count($posts);
	
	$after = $data->data->after;
	
	echo $postcount . " ";
}

echo "\n\nfinished\n\n";

file_put_contents($fn_output, json_encode($posts));

?>