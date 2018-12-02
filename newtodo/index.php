<?php
/*
Copyright (c) 2009 Dave Menconi
Program to display master todo list
*/
include_once("../library/mysql.php");
include_once("config.php");
$PARAMS = array_merge($_POST,$_GET);
$link = make_mysql_connect($dbhost, $dbuser, $dbpass, $dbname);

//initialize variables
$datafilename = "mastertodo.dum";
$dummyurl = "Dummy.html";

		$logfilename = $datafilename.".log";
		$fieldnames = array("url","title","importance","active");
$params = array_merge($_GET,$_POST);
print "params\n";print_r($params);
$tododata=getfile($datafilename,$fieldnames);
		//sort array
if (isset($params['m'])) $mode=$params['m'];
else $mode="display";

switch($mode){
	case "e"://main edit page
		print "edit\n";
		show_edit_page($datafilename,$fieldnames);
		break;
	case "u": //Update page
		print "u: update page;\n";
		if(isset($params['i']) && isset($tododata[$params['i']])){
			$record = $tododata[$params['i']];
			show_update_page($record,$fieldnames,$params['i']);
		}
		break;
	case "pd": //parse delete page
		print "parse delete~~;";
		if(isset($params['i'])){
			$idx = $params['i'];
			unset($tododata[$idx]);
			sort($tododata);
		}
		overwrite_file($datafilename,$fieldnames,$tododata,$i);
exit();
		show_edit_page($datafilename,$fieldnames);
		break;
	case "pu": // parse update
		print "parse update;";
		if(isset($params['active'])) $active = $params['active'];
		else $active = "-1";
		if(isset($params['url'])) $webpage = $params['url'];
		else $webpage = "Dummy.html";
		if(isset($params['title'])) $todoitem = $params['title'];
		else $todoitem = "No Title";
		if(isset($params['importance'])) $importance = $params['importance'];
		else $importance = 0;
		if(isset($params['i'])) $i = $params['i'];
		else {
			show_edit_page($datafilename,$fieldnames);
			print "Error: Record number to edit was nul.";
			exit();
		}
		$tododata[$i]['url'] = $webpage;
		$tododata[$i]['title'] = $todoitem;
		$tododata[$i]['importance'] = $importance;
		$tododata[$i]['active'] = $active;
		print "datafilename: $datafilename\n";
		print_r($fieldnames);
		print_r($tododata);
		print"i: $i\n";
		overwrite_file($datafilename,$fieldnames,$tododata,$i);
exit();
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
break_mysql_connect($link);
exit();
/*********************************************************************/
/**********************************************
 *
 **********************************************/
function show_update_page($record,$fieldnames,$idx){
	//print "show_update_page(record,fieldnames)\n";
		//Array ( [webpage] => Dummy.html [todoitem] => zTest [importance] => 0 )
	$webpage=$record['url'];
	$todoitem=$record['title'];
	$importance=$record['importance'];
	$active=$record['active'];
	print "<form method=\"post\" action=\"index.php\">\n";
	print "<ul>";
	print "<li>Todo Item: <input type=\"text\" name=\"todoitem\" value=\"$todoitem\">\n";
	print "<li>Importance: <input type=\"text\" name=\"importance\" value=\"$importance\">\n";
	print "<li>Active: <input type=\"text\" name=\"active\" value=\"$active\">\n";
	print "<li>Web Page: <input type=\"text\" name=\"webpage\" value=\"$webpage\">\n";
	print "</ul>\n";
		print "<input type=\"submit\"  value=\"Submit\"></br>\n";
		print "<input type=\"hidden\" name=\"m\" value=\"pu\">\n";
		print "<input type=\"hidden\" name=\"i\" value=\"$idx\">\n	</form>\n";

}

/**********************************************
 *
 **********************************************/
function show_edit_page($filename,$fieldnames){
	global $tododata;
	print "show_edit_page($filename,fieldnames)\n";
//		usort($tododata,'cmp2');
//print_r ($tododata);
		print "<table border=\"1\"><tr><th>Title</th><th>Importance</th><th>Active</th><th>ACTIONS</th></tr>\n";
		foreach($tododata as $idx=>$todo){

			print "<tr><td><a href=\"".$todo['url']."\">".$todo['title']."</a></td><td><center>".$todo['importance']."</td><td><center>".$todo['active']."</td>\n";
			print "<td><a href=\"index.php?m=pd&i=$idx\">Delete</a> | <a href=\"index.php?m=u&i=$idx\">Update</a></td></tr>\n";
		}
		print "</table>\n";


		print "<h2>Add New Item</h2><form method=\"post\" action=\"index.php\">\n";
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
function cmp2($a, $b) {
	$importance = strcmp( $b['importance'],$a['importance']);
	if($importance != 0) return $importance;
	return strcmp( $a['title'],	$b['title']);
}

function overwrite_file($filename,$fieldnames,$data,$i){
	global $reftodolist,$link;
	print "overwrite_file($filename, fieldnames,data,$i)\n";
	print "data $i, "; print($data[$i]);
	print_r($data[$i]);
}
/**********************************************
 *
 **********************************************/
function oldoverwrite_file($filename,$fieldnames,$data){
	//print "oldoverwrite_file($filename,fieldnames,data)\n";
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
 	global $reftodolist,$link;
	//print "getfile($filename,fieldnames,data)\n";

	$sql=" select * from newtodo  order by importance desc, title";
//	print "SQL=$sql</br>\n";
	$list = MYSQLGetData($link,$sql);
	$reftodolist = $list;
//	print_r($list);
	return ($list);

 }
/**********************************************
 *
 **********************************************/
function oldgetfile($filename,&$fieldnames){
	//print "oldgetfile($filename,fieldnames,data)\n";
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
	return strcmp( $a['title'],	$b['title']);
}

/**********************************************
 *
 **********************************************/
function genpage($tasklist){
	//print "genpage(tasklist)\n";
echo <<< EOF

<html>
<head>
	<TITLE>Dave's Things To Do 2014</TITLE>
	<LINK href="stylesheet.css" rel="stylesheet" type="text/css">
	<link rel="SHORTCUT ICON" href="checklisticon.ico"/>
</head>
	<body>
	<h1>Things I'm Working On In 2014</h1>

<ul>

EOF;
	for($i=0;$i<count($tasklist);$i++){
		if($tasklist[$i]['active']==0)continue;
		print "<li><a href=\"".$tasklist[$i]['url']."\">".$tasklist[$i]['title']."</A>\n";

	}

	print "</ul>\n";
	$pick = getline($tasklist);
	print "<hr><h2>Today's Task</h2>\n";
	print "<a href=\"".$pick['url']."\">".$pick['title']."<p>\n	<a href=\"index.php?m=e\">Edit List</a></br>\n<hr>\n";

	// display some random numbers just for fun
	print "<h3>Random Numbers</h3>\n<table border =\"1\">\n";
	print"<tr><th>Sides</th><th>Value</th>";
	$rndsides= array(4=>"Four",6=>"Six",8=>"Eight",10=>"Ten",12=>"Twelve",20=>"Twenty",100=>"Percentage");
	$rndnums=array();
	foreach($rndsides as $rndside=>$sidename){
		$rndnum=mt_rand(1,$rndside);
		print "<tr><td><b>$sidename</td><td><center><i> $rndnum</td></tr>\n";
	}
	print "</table>\n";

echo <<< EOF2

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
	//print "getline(tasklist)\n";
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
	//print "calctotprob(tasklist)\n";
 	$totprob = 0;
	for($i=0;$i<count($tasklist);$i++){
		$chance = $tasklist[$i]['chance'];
		if($totprob<$chance) $totprob=$chance;
	}
	return $totprob;
 }
?>
