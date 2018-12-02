<?php
/*
Copyright (c) 2009 Dave Menconi

*/
// get variable unique to this wallboard ($instance, perhaps some others)
include_once "config.php";
//initialize variables
$datafilename = "url.csv";
$dummyurl = "Dummy.html";

		$logfilename = $datafilename.".log";
$params = array_merge($_GET,$_POST);

$fieldnames = array();
$urldata = readindata($datafilename,$fieldnames);
usort($urldata,'cmp');

if (isset($params['m'])) $mode=$params['m'];
else $mode="display";
//print_r($urldata);
/* ----------------------------------------------------------------- */
switch($mode){
  case "c":// create page
  	show_create_page($fieldnames);
  	break;
	case "e"://main edit page
		show_edit_page($datafilename,$fieldnames);
		break;
	case "u": //Update page
		print "display Update<br>\n";
		if(isset($params['i']) && isset($urldata[$params['i']])){
			$record = $urldata[$params['i']];
			show_update_page($record,$fieldnames,$params['i']);
		}
		else
			genpage($usldata,$fieldnames);
		break;
	case "pd": //parse delete page
//		print "parse delete~~";
		if(isset($params['i'])){
			$idx = $params['i'];
			if(isset($urldata[$idx])) {
				if(strtoupper($urldata[$idx]['Active'])=="Y"){
					$urldata[$idx]['Active']="N"; // if it`s active don`t delete it, just make it inactive
				}
				else {
					unset($urldata[$idx]);
					sort($urldata);
				}
			}
		}
		overwrite_file($datafilename,$fieldnames,$urldata);
		genpage($urldata,$fieldnames);
		break;
	case "pu": // parse update
		print "parse update";
		print_r($params);
		if(isset($params['i']) && isset($urldata[$params['i']])){
			$i=$params['i'];
			foreach($fieldnames as $fieldname){
				if(isset($params[$fieldname]))$urldata[$i][$fieldname]=$params[$fieldname];
			}
		overwrite_file($datafilename,$fieldnames,$urldata);

		}
		else {
			print "oops";
		}
		genpage($urldata,$fieldnames);
		break;
	case "pc": // parse create
		//print "parse create</br>";
		$record = $params;
		unset($record['m']);
		//print_r($params);
		//print_r($record);
		$urldata[]=$record;
		overwrite_file($datafilename,$fieldnames,$urldata);
		genpage($urldata,$fieldnames);
		break;
	case "display":
	default:
		genpage($urldata,$fieldnames);
}
exit();
/*********************************************************************/
/**********************************************
 *
 **********************************************/
function show_create_page($fieldnames){
global $instance;
 send_header($instance);
	//print "show_create_page(fieldnames)<br>\n";
	print "<form method=\"post\" action=\"index.php\">\n";

	foreach($fieldnames as $fieldname){
		print "<li class=\"formfieldname\">$fieldname: ";
		print "<input class=\"formdata\" type=\"text\" name=\"$fieldname\"  size=\"50\"></li>\n";
	}
	print "</ul>\n";
	print "<input type=\"submit\"  value=\"Submit\"></br>\n";
	print "<input type=\"hidden\" name=\"m\" value=\"pc\">\n";
	print "	</form>\n";
	print "<hr><a href=\"index.php\">Return to Main Page</a>\n";
 send_footer();
}
/**********************************************
 *
 **********************************************/
function show_update_page($record,$fieldnames,$idx){
 send_header($instance);
	print "show_update_page(record,fieldnames,$idx)<br>\n";
	print "<form method=\"post\" action=\"index.php\">\n";
	print "<ul class=\"formlist\">";
	foreach($fieldnames as $fieldname){
		print "<li class=\"formfieldname\">$fieldname: ";
	  $value = $record[$fieldname];
	  $len = strlen($value);
		print "<input class=\"formdata\" type=\"text\" name=\"$fieldname\" value=\"$value\" size=\"$len\"></li>\n";
	}
	print "</ul>\n";
	print "<input type=\"submit\"  value=\"Submit\"></br>\n";
	print "<input type=\"hidden\" name=\"m\" value=\"pu\">\n";
	print "<input type=\"hidden\" name=\"i\" value=\"$idx\">\n	</form>\n";
 send_footer();
}
/**********************************************
 *
 **********************************************/

/**********************************************
 *
 **********************************************/
function overwrite_file($filename,$fieldnames,$data){
	$handle = fopen($filename,"wt");
	usort($data,'cmp');

	fputcsv($handle, $fieldnames);
	for($i=0;$i<count($data);$i++){
		fputcsv($handle, $data[$i]);
	}
	// change to do CSV
/*	foreach($data as $datum){
		$line="";
		foreach($fieldnames as $fieldname){
			if(isset($datum[$fieldname])){
				$line .= $datum[$fieldname]."\t";
			}
			else $line.="\t";
		}
		fwrite($handle,trim($line)."\n");
	}
*/
	fclose($handle);
}
/**********************************************
 *
 **********************************************/
function getfile($filename,&$fieldnames){
	//read the file in
	$row = 0;
	if (($handle = fopen($filename, "rt")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
					$num = count($data);
					for ($c=0; $c < $num; $c++) {
						if(strlen($data[$c])==0) {
							switch($c){
								case 0:
									$data[$c]="Dummy.html";
									break;
								case 1:
									$data[$c]="No Title";
									break;
								case 2:
									$data[$c]=0;
									break;
							}
						}
						$urldata[$row][$fieldnames[$c]] = $data[$c];
					}
					$row++;
			}
			fclose($handle);
	}
	return $urldata;
}
/**********************************************
 *
 **********************************************/
function cmp($a, $b) {
	return $a>$b;
}

/**********************************************
 *
 **********************************************/
function genpage($data,$fieldnames){
global $instance;

 send_header($instance);
print "	<h1>WALLBOARD URLS FOR WALLBOARD $instance</h1>\n<ul>\n";
	print "<table border=\"1\">\n";
	print "<tr>\n";
	foreach($fieldnames as $fieldname){
		print "<th>$fieldname</th>\n";
	}
		print "<th colspan=2>Actions</th></tr>\n";

	for($i=0;$i<count($data);$i++){
		print "<tr>\n";
		foreach($fieldnames as $fieldname){
			print "<td>".$data[$i][$fieldname]."</td>\n";
		}
		print "<td><a href=\"index.php?i=$i&m=u\">Edit</a></td>\n";
		print "<td><a href=\"index.php?i=$i&m=pd\">Delete</a></td>\n";
		print "</tr>\n";
	}
	print "</table>\n";


echo <<< EOF2
	<hr>
	<a href="index.php?m=c">Add a Url to Wallboard $instance</a>
EOF2;
 send_footer();
}
/**********************************************
 *
 **********************************************/
function getline($tasklist){
	$totprob = calctotprob($tasklist);

	$roll= mt_rand(0,$totprob);
	for($i=0;$i<count($tasklist);$i++){
		if($tasklist[$i]['chance']>=$roll){
			break;
		}
	}
	return($tasklist[$i]);
}
/**********************************************
 *
 **********************************************/
 function calctotprob($tasklist){
 	$totprob = 0;
	for($i=0;$i<count($tasklist);$i++){
		$chance = $tasklist[$i]['chance'];
		if($totprob<$chance) $totprob=$chance;
	}
	return $totprob;
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
			}//while
        fclose($handle);
    }//if
    return($urls);
}
/**********************************************
 *
 **********************************************/
function send_header($instance){
echo <<< EOF

<html>
<head>
	<TITLE>WALLBOARD URLS</TITLE>
	<LINK href="stylesheet.css" rel="stylesheet" type="text/css">
	<link rel="SHORTCUT ICON" href="checklisticon.ico"/>
</head>
	<body>


EOF;
}
/**********************************************
 *
 **********************************************/
function send_footer(){
echo <<< EOF2

	</body>
</html>
EOF2;

}
?>
