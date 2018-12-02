<?php
/*
Copyright (c) 2009 Dave Menconi

*/
//initialize variables
$datafilename = "mastertodo.tab";
$dummyurl = "Dummy.html";

		$logfilename = $datafilename.".log";
		$fieldnames = array("webpage","todoitem","importance");
$params = array_merge($_GET,$_POST);

$tododata=getfile($datafilename,$fieldnames);
		//sort array

if (isset($params['m'])) $mode=$params['m'];
else $mode="display";

switch($mode){
	case "e"://main edit page
		show_edit_page($datafilename,$fieldnames);
		break;
	case "u": //Update page
		print "display Update";
		if(isset($params['i']) && isset($tododata[$params['i']])){
			$record = $tododata[$params['i']];
			show_update_page($record,$fieldnames,$params['i']);
		}
		break;
	case "pd": //parse delete page
		print "parse delete~~";
		if(isset($params['i'])){
			$idx = $params['i'];
			unset($tododata[$idx]);
			sort($tododata);
		}
		overwrite_file($datafilename,$fieldnames,$tododata);
		show_edit_page($datafilename,$fieldnames);
		break;
	case "pu": // parse update
		print "parse update";
		if(isset($params['webpage'])) $webpage = $params['webpage'];
		else $webpage = "Dummy.html";
		if(isset($params['todoitem'])) $todoitem = $params['todoitem'];
		else $todoitem = "No Title";
		if(isset($params['importance'])) $importance = $params['importance'];
		else $importance = 0;
		if(isset($params['i'])) $i = $params['i'];
		else {
			show_edit_page($datafilename,$fieldnames);
			print "oops";
			exit();
		}
		$tododata[$i]['webpage'] = $webpage;
		$tododata[$i]['todoitem'] = $todoitem;
		$tododata[$i]['importance'] = $importance;
		overwrite_file($datafilename,$fieldnames,$tododata);

		show_edit_page($datafilename,$fieldnames);
		break;
	case "pc": // parse create
		print "parse create</br>";

		if(isset($params['url'])) $url=$params['url'];
		else $url = "Dummy.html";

		if(isset($params['todo'])) $title=$params['todo'];
		else $title = "No Title";

		if(isset($params['import'])) $importance=$params['import'];
		else $importance = "0";

		$result = "$url\t$title\t$importance\n";
		$handle = fopen($datafilename, "at");
		fwrite ($handle,$result);
		fclose($handle);
		show_edit_page($datafilename,$fieldnames);
		break;
	case "display":
	default:
		$tododata=getfile($datafilename,$fieldnames);
		//sort array
		usort($tododata,'cmp');
	// set some more variables based on the arguments
		// calculate probabilites
		$totprob = 0;
		for($i=0;$i<count($tododata);$i++){
			$totprob+=$tododata[$i]['importance'];
			$tododata[$i]['chance'] = $totprob;
		}
		genpage($tododata);
}
exit();
/*********************************************************************/
/**********************************************
 *
 **********************************************/
function show_update_page($record,$fieldnames,$idx){
	print "show_update_page(record,fieldnames)\n";
		//Array ( [webpage] => Dummy.html [todoitem] => zTest [importance] => 0 )
	$webpage=$record['webpage'];
	$todoitem=$record['todoitem'];
	$importance=$record['importance'];
	print "<form method=\"post\" action=\"index.php\">\n";
	print "<ul>";
	print "<li>Web Page: <input type=\"text\" name=\"webpage\" value=\"$webpage\">\n";
	print "<li>Todo Item: <input type=\"text\" name=\"todoitem\" value=\"$todoitem\">\n";
	print "<li>Importance: <input type=\"text\" name=\"importance\" value=\"$importance\">\n";
	print "</ul>\n";
		print "<input type=\"submit\"  value=\"Submit\"></br>\n";
		print "<input type=\"hidden\" name=\"m\" value=\"pu\">\n";
		print "<input type=\"hidden\" name=\"i\" value=\"$idx\">\n	</form>\n";

}

/**********************************************
 *
 **********************************************/
function show_edit_page($filename,$fieldnames){
		$tododata=getfile($filename,$fieldnames);
		print "<table border=\"1\"><tr><th>Webpage</th><th>Title</th><th>Importance</th></tr>\n";
		foreach($tododata as $idx=>$todo){
			print "<tr><td>".$todo['webpage']."</td><td>".$todo['todoitem']."</td><td>".$todo['importance']."</td>\n";
			print "<td><a href=\"index.php?m=pd&i=$idx\">Delete</a> | <a href=\"index.php?m=u&i=$idx\">Update</a></td></tr>\n";
		}
		print "</table>\n";
		print "<form method=\"post\" action=\"index.php\">\n";
		print "Webpage: &nbsp;&nbsp;&nbsp;<input type=\"text\" name=\"url\" value=\"Dummy.html\"></br>\n";
		print "Title: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"text\" name=\"todo\" value=\"No Title\"></br>\n";
		print "Importance: &nbsp;<input type=\"text\" name=\"import\" value=\"0\"></br>\n";
		print "<input type=\"submit\"  value=\"Submit\"></br>\n";
		print "<input type=\"hidden\" name=\"m\" value=\"pc\">\n	</form>\n";

		print "<a href=\"index.php\">Display Main Page</a>\n<br>";
}
/**********************************************
 *
 **********************************************/
function overwrite_file($filename,$fieldnames,$data){
	$handle = fopen($filename,"wt");
//	$fieldline = "";
//	foreach($fieldnames as $name){
//		$fieldline .= $name."\t";
//	}
//	$fieldline = trim($fieldline)."\n";
//	fwrite($handle,$fieldline);
	foreach($data as $datum){
		$line="";
		foreach($fieldnames as $fieldname){
			if(isset($datum[$fieldname])){
				$line .= $datum[$fieldname]."\t";
			}
			else $line.="\t";
		}
		fwrite($handle,trim($line)."\n");
	}
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
        	$tododata[$row][$fieldnames[$c]] = $data[$c];
        }
        $row++;
    }
    fclose($handle);
}
return $tododata;
}
/**********************************************
 *
 **********************************************/
function cmp($a, $b) {
	return strcmp( $a['todoitem'],	$b['todoitem']);
}

/**********************************************
 *
 **********************************************/
function genpage($tasklist){
echo <<< EOF

<html>
<head>
	<TITLE>Dave's Things To Do 2012</TITLE>
	<LINK href="stylesheet.css" rel="stylesheet" type="text/css">
	<link rel="SHORTCUT ICON" href="checklisticon.ico"/>
</head>
	<body>
	<h1>Things I'm Working On 2012</h1>

<ul>

EOF;
	for($i=0;$i<count($tasklist);$i++){
		print "<li><a href=\"".$tasklist[$i]['webpage']."\">".$tasklist[$i]['todoitem']."</A>\n";

	}

	print "</ul>\n";
	$pick = getline($tasklist);
	print "<hr><h2>Today's Task</h2>\n";
	print "<a href=\"".$pick['webpage']."\">".$pick['todoitem']."<p>\n";


echo <<< EOF2

	<hr>
	<a href="index.php?m=e">Edit List</a>
	<hr>
	<div class="legal">Copyright &copy; 2012 Dave Menconi. All rights reserved.</div>
	</body>
</html>
EOF2;

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
?>
