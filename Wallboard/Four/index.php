<?php
/*
Copyright 2011 Dave Menconi

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0
 */
 // initialize global constants

date_default_timezone_set('America/Los_Angeles');

$holdtime = 1;// how many seconds to keep the same set of URLs on the screen

// get variable unique to this wallboard ($instance, perhaps some others)
include_once "config.php";

//read in the URLs
$filename = "urls.csv";
$urls = readindata($filename,$fieldnames);

// some urls are just for morning, just for afternoon or both; this strips out the ones that dont apply to this time
$urls = StripOutWrongTime($urls);

//some urls are serious, some are fun. some are for a specific venu; this strips out the ones that dont apply to this venue
$urls = StripOutWrongType($urls,$venue,$type);//venue and type come from config.php

// its allowed to set a URL to inactive by putting an "N" in the Active field
$urls = StripOutInactive($urls);

// now sort it by index
usort ($urls,"IDXcmp");

// pick a url
$urlno = pickurlno(count($urls), $holdtime);

//Get the main url were going to use
$mainurls[0] = $urls[$urlno];

// we load up the array with blank pages so that if we dont have enough urls of the right type it wont get an error
for ($i=1; $i<4; $i++) $mainurls[$i]['URL']="http://platform.share.scea.com/wallboard1/empty.html";


unset($urls[$urlno]);// remove that one

$shape = $mainurls[0]['Shape'];

$urls = StripOutWrongShape($urls,$shape);

shuffle($urls);// shuffle whats left

$count = count($urls);
print "Shape: $shape; count: $count; Venue: $venue; Type: $type</br>\n";

switch ($shape){
        case "1": // this one is narrow and short, do 4 total
                if ($count>4)$count = 4;
                for($i=0; $i<$count;$i++){
            		$mainurls[$i+1]=$urls[$i];
                }
                  print $mainurls[0]['URL'].", ".$mainurls[1]['URL'].", ".$mainurls[2]['URL'].", ".$mainurls[3]['URL']."</br>\n";
                $html = "<table >\n<tr>\n<td><iframe src=\"".$mainurls[0]['URL']."\" width=\"960\" height=\"540\"></iframe></td>\n<td><iframe src=\"".$mainurls[1]['URL']."\" width=\"960\" height=\"540\"></iframe></td>\n</tr>\n<tr>\n<td><iframe src=\"".$mainurls[2]['URL']."\" width=\"960\" height=\"540\"></iframe></td>\n<td><iframe src=\"".$mainurls[3]['URL']."\" width=\"960\" height=\"540\"></iframe></td>\n</tr>\n</table>\n";
                break;
        case "3": // this one is narrow and tall, do 2
                $count = count($urls);
                if ($count>2)$count = 2;
                for($i=0; $i<$count;$i++){
            		$mainurls[$i+1]=$urls[$i];
                }
                $url1=$mainurls[0]['URL'];
                $url2=$mainurls[1]['URL'];
                  print"$url1, $url2</br>\n";

                $html = "<table><tr><td><iframe src=\"$url1\" width=\"960\" height=\"1080\"></iframe></td><td><iframe src=\"$url2\" width=\"960\" height=\"1080\"></iframe></td></tr></table>";
                break;
        case "2":// this one is wide and short, do 2
                $count = count($urls);
                if ($count>2)$count = 2;
                for($i=0; $i<$count;$i++){
            		$mainurls[$i+1]=$urls[$i];
                }
                $url1=$mainurls[0]['URL'];
                $url2=$mainurls[1]['URL'];
                  print"$url1, $url2</br>\n";
               $html = "<table><tr><td><iframe src=\"$url1\" width=\"1920\" height=\"540\"></iframe></td></tr><tr><td><iframe src=\"$url2\" width=\"1920\" height=\"540\"></iframe></td></tr></table>";
                break;
        case "4": // this one is wide and tall, do 1
               $url = $mainurls[0]['URL'];
                 print"$url</br>\n";
                $html = "<iframe src=\"$url\" width=\"1920\" height=\"1080\"></iframe>";
                break;
        default:
                print "<h1>Error!</h1><P>$shape:$idx\n";
}
print <<< EOF
<html>
<head>
<meta http-equiv="refresh" content="25" />
	<LINK href="stylesheet.css" rel="stylesheet" type="text/css">

<title>Wallboard Data</title>
</head>
<body>
$html;
</body>
</html>
EOF;
exit(); /******************************************************************************************************/
/*********************************************************************/
/**********************************************
 *
 **********************************************/
function GenUrlno($max,$check=0){
	global $holdtime;
	//print "GenUrlno($max)\n";
	// We want a unique large number that will be the same throughout any given day but be radically different from one day to the next
	//  get an MD5 hash of the day number and make it decimal & use that as a hash
	$time =time()+$check;
	$val = floor($time/$holdtime);
	$urlno1 = floor($val%$max);

	$hash = hash("md5",$val);
	$hash = hexdec(substr($hash,strlen($hash)-7,7));
	$urlno2 =  $hash%$max;
	return($urlno2);
}

/**********************************************
 *
 **********************************************/
function genpage($url){
global $instance,$holdtime;
$title = $url['TITLE'];
$url = $url['URL'];
echo <<< EOF
<html>
<head>
	<meta http-equiv="refresh" content="$holdtime" />
	<title>Wallboard Data</title>

	<LINK href="stylesheet.css" rel="stylesheet" type="text/css">
</head>
	<body>
	<h1>$title</h2>
	<IMG SRC="$url"  BORDER=0></br>
	Instance: $instance
	<hr>

	</body>
</html>
EOF;

}

/**********************************************
 *
 **********************************************/
function readindata($filename,&$fieldnames){
	$row = 0;
	if (($handle = fopen($filename, "rt")) !== FALSE) {
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		// get row headers
		if($row==0){
			$fieldnames = $data;
			$row++;
			continue;
		}
		$num = count($data);
		for ($c=0; $c < $num; $c++) {
			$urls[$row][$fieldnames[$c]] = $data[$c];
		}
		$row++;
	    }
	    fclose($handle);
	}
	return($urls);
}
/**********************************************
 *
 **********************************************/
function StripOutWrongTime($urls){
	//print "StripOutWrongTime(urls)\n";

	sort($urls); // organize the indexes

	// when is it: morning or evening?
	$ampm=date("A"); // get string "AM" or "PM"

	if ($ampm=="AM") $skiptime = "P";//for AM remove P
	else $skiptime = "A"; // for PM remove A
	$count=count($urls);
	for($i=0;$i<$count;$i++){// step through URLs and...
		if($urls[$i]['Time']==$skiptime) {
			unset ($urls[$i]);// unset the ones with the wrong time
		}
	}
	return($urls);
}
/**********************************************
 *
 **********************************************/
function StripOutWrongType($urls,$venue,$type){
	//print "StripOutWrongType(urls,$venue,$type)\n";

	sort($urls); // organize the indexes (remove gaps and make indexes monotonicly increasing)
//print_r($urls);
	//adjust type were looking for
	if($type="F")$removetype = "S";
	else $removetype = "F";

	print "removetype=$removetype\n";
		$count=count($urls);
		for($i=0;$i<$count;$i++){
		// remove based on venue -- if its explicitely for a DIFFERENT venue then its gone
		if($urls[$i]['Venue']!=$venue && $urls[$i]['Venue']!=0){ // not THIS venue, not 0 (any venue)...
//			print "remove $i for venue $venue: ".$urls[$i]['Venue']."\n";
			unset ($urls[$i]);//...so dump it
		}
			//now remove based on type -- if this is a serious venue then fun goes and vice versa
		else if($urls[$i]['Venue']==0 && $urls[$i]['Type']==$removetype){ // open venue & wrong type...
	//		print "remove $i for type " .$urls[$i]['Type']."\n";
			unset ($urls[$i]);//...so dump it
		}
	}
	return($urls);

}/**********************************************
 *
 **********************************************/
function StripOutInactive($urls){
	//print "StripOutInactive(urls)\n";

	sort($urls); // organize the indexes (remove gaps and make indexes monotonicly increasing)

		$count=count($urls);
		for($i=0;$i<$count;$i++){
		// remove based on active field -- if it has an "N" we remove it
		if(strtoupper($urls[$i]['Active'])=="N"){ // Not Active...
			unset ($urls[$i]);//...so dump it
		}
	}
	sort($urls);
	return($urls);
}

/**********************************************
 *
 **********************************************/
function StripOutWrongShape($urls,$shape){
	//print "StripOutWrongShape(urls,$shape)\n";

	sort($urls); // organize the indexes

	$count=count($urls);
	for($i=0;$i<$count;$i++){// step through URLs and...
		if($urls[$i]['Shape']!=$shape) {
			unset ($urls[$i]);// unset the ones with the wrong shape
		}
	}
	return($urls);
}
/**********************************************
 *
 **********************************************/
function IDXcmp($a,$b){
    if ($a['IDX'] == $b['IDX']) return 0;
    return ($a['IDX'] < $b['IDX']) ? -1 : 1;
}
/**********************************************
 *
 **********************************************/
function pickurlno($max, $delay){
	//print"pickurlno($max, $delay)\n";
	return (time()/$delay)%$max;
}

?>