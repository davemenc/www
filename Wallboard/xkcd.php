<?php
/*
Copyright (c) 2009 Dave Menconi

*/

//initialize variables


$fieldnames=array("URL","TITLE");


$datafilename = "xkcd.csv";

//read the file in
$row = 0;
if (($handle = fopen($datafilename, "rt")) !== FALSE) {
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
$urlcount = count($urls);
$urlno = GenUrlno($urlcount);
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
?>
