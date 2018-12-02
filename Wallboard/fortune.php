<?php
/*
Copyright 2012 Dave Menconi

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0
 */

//initialize variables


$fieldnames=array("URL","TITLE");


$datafilename = "fortune.csv";

//read the file in
if (($handle = fopen($datafilename, "rt")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
       	$quotes[] = $data[0];
    }
    fclose($handle);
}
$urlcount = count($quotes);
	$urlno = GenUrlno($urlcount);
	genpage($quotes[$urlno]);
exit();
/*********************************************************************/
/**********************************************
 *
 **********************************************/
function GenUrlno($max,$index=0){
//	print "GenUrlno($max)\n";
	// We want a unique large number that will be the same throughout any given day but be radically different from one day to the next
	//  get an MD5 hash of the day number and make it decimal & use that as a hash

	$day = date("zHi")+$index;
	$hash = hash("md5",$day);
	$hash = hexdec(substr($hash,strlen($hash)-7,7));
	$quoteno =  $hash%$max;
	return($quoteno);
}

/**********************************************
 *
 **********************************************/
function genpage($quote){
//print "genpage($quote)\n";
echo <<< EOF
<html>
<head>
	<TITLE>Quote of the Hour</TITLE>
	<LINK href="stylesheet.css" rel="stylesheet" type="text/css">
</head>
	<body>
	<div class="quote">$quote</div>

	</body>
</html>
EOF;

}
?>
