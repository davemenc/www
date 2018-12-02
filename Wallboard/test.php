<?php
/*
Copyright 2012 Dave Menconi

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0
 */

//initialize variables

include_once("config.php");

$fieldnames=array("URL","Type","Time","Size","Duration","IDX","Site","Active","Note");

$duration = 45;

$datafilename = "urls.csv";

//read the file in
$urls = readindata($datafilename,$fieldnames);
$urlcount = count($urls);
$urlno = GenUrlno($urlcount);
print_r($urls[$urlno]);
exit();

genpage($urls[$urlno]);
exit();
/*********************************************************************/
/**********************************************
 *
 **********************************************/
function GenUrlno($max,$check=0){
	//print "GenUrlno($max)\n";
	// We want a unique large number that will be the same throughout any given day but be radically different from one day to the next
	//  get an MD5 hash of the day number and make it decimal & use that as a hash

	$day = date("z");
	$hash = hash("md5",$day);
	$hash = hexdec(substr($hash,strlen($hash)-7,7));
	$urlno =  $hash%$max;
	return($urlno);
}

/**********************************************
 *
 **********************************************/
function genpage($url){
$title = $url['TITLE'];
$url = $url['URL'];
echo <<< EOF
<html>
<head>
	<TITLE>Today's XKCD Comic</TITLE>
	<LINK href="stylesheet.css" rel="stylesheet" type="text/css">
</head>
	<body>
	<h1>$title</h2>
	<IMG SRC="$url"  BORDER=0></br>
	Images courtesy of <b>XKCD.COM</b>
	<hr>
	</body>
</html>
EOF;

}

/**********************************************
 *
 **********************************************/
function readindata($filename,$fieldnames){

	$row = 0;
	if (($handle = fopen($filename, "rt")) !== FALSE) {
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		// get row headers
		if($row==0){

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
?>
