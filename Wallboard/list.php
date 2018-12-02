<?php
/*
LIST.PHP
 */
 // initialize global constants
$servername = "http://localhost/wallboard/";

 $params = array_merge($_GET,$_POST);
//print_r($params);

 //read in the data
  $filename = "urls.csv";
  $filename = "test.csv";
  $urls = readindata($filename,$fieldnames);

// set mode
 if(array_key_exists("mode",$params)) $mode=$params['mode'];
 else $mode='display';

// print "mode=$mode\n";
switch($mode){
	case "edit":
		print "edit!\n";
		if(array_key_exists("key",$params))$idx = $params['key'];
		else displaylist($urls,$fieldnames,"No index found in edit."); // doesnt return
		print "idx: $idx\n";
		break;
	case "parseedit":
		print "parseedit!\n";
		break;
	case "disable":
//		print "disable!\n";
		if(array_key_exists("key",$params))$idx = $params['key'];
		else displaylist($urls,$fieldnames,"No index found in disable."); // doesnt return
		$urls[$idx]['Active']="N";
		writeoutdata($filename,$fieldnames,$urls);
		displaylist($urls,$fieldnames);
		break;
	case "enable":
//		print "enable!\n";
		if(array_key_exists("key",$params))$idx = $params['key'];
		else displaylist($urls,$fieldnames,"No index found in edit."); // doesnt return
		$urls[$idx]['Active']="Y";
		writeoutdata($filename,$fieldnames,$urls);
		displaylist($urls,$fieldnames);
		break;
	case "display":
	default:
		print "display!\n";
		displaylist($urls,$fieldnames);
}


 exit();
 /***********************************************************/
 /****************** FUNCTIONS *****************************/
/***********************************************************/

/**********************************************
 * READINDATA
 * Input
 *   Filename -- the filename that has our data
 *     We assume that the field names are in the first line
 *     We assume that it is comma delimited with nl as line separators
 *     We also assume that the NLs are the same as the computer that it is run on
 *   Fieldnames -- an array that will contain the field names in the first line
 * Output
 *   Returns an array with the field names (from the 1st line of the file) as keys
 * Side Effects
 *   $fieldnames contains all the field names from the 1st line of the file
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
function printheader(){
print <<< EOF
<html>
<head>
<meta http-equiv="refresh" content="25" />
	<LINK href="stylesheet.css" rel="stylesheet" type="text/css">

<title>Wallboard Data</title>
</head>
<body>

EOF;
}
/**********************************************
 *
 **********************************************/
function printfooter(){
print <<< EOF
</head>
</html>

EOF;
}

/**********************************************
 *
 **********************************************/
function displaylist($data,$fieldnames, $error=""){
	global $servername;
	printheader();
	if ($error!="")print "<hr><span class=\"error\">ERROR: $error</span><hr></br>\n";

	print "<table border=\"1\">\n";
	print "<tr>\n\t";
	foreach($fieldnames as $fieldname){
		print "<th>$fieldname</th> ";
	}
	print "<th> Actions</th>";
	print "</tr>\n";

	foreach($data as $idx=>$record){
		print "<tr>\n\t";

		foreach ($record as $key=>$value){
			print "<td>&nbsp;$value&nbsp;</td> ";
		}
		print "<td><a href=\"".$servername."list.php?mode=edit&key=$idx\"><span class=\"action\">Edit</span></a> | <a href=\"".$servername."list.php?mode=disable&key=$idx\"><span class=\"action\">Disable</span></a> | <a href=\"".$servername."list.php?mode=Enable&key=$idx\"><span class=\"action\">enable</span></a></td>\n";
		print "</tr>\n";
	}
	print "</table>\n";

	print "</body>\n</html>\n";
	exit();
}
/**********************************************
 *
 **********************************************/

function writeoutdata($filename,$fieldnames,$data){

	if (($handle = fopen($filename, "wt")) !== FALSE) {
		// put out the header line
		$first = true;
		foreach($fieldnames as $fieldname){
			if($first){
				fputs($handle,"$fieldname");
				$first=false;
			}
			else fputs($handle, ",$fieldname");
		}
		fputs($handle, "\n");

		// now put out the data
		foreach($data as $datum){ // do a line
			$first=true;
			foreach($datum as $field){//step through the fields on a line
				if($first){
					fputs($handle, $field);
					$first=false;
				}
				else fputs($handle, ",$field");
			}
			fputs($handle, "\n");
		}
	    fclose($handle);
	}
}
?>