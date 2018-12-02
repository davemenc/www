<?php
/****************************************
* index.php -- Project web page
*****************************************/
include_once("config.php");
include_once("../library/miscfunc.php");
include_once("../library/debug.php");
include_once("../library/loc_login.php");
include_once("../library/mysql.php");
include_once("../library/htmlfuncs.php");
//log_on();
//debug_on();
debug_string("----------------- START PROJECTS --------------------------------------");

// set the params array up
$PARAMS = array_merge($_POST,$_GET);
debug_array("PARAMS",$PARAMS);

// open a link to the database (used everywhere)
$link = make_mysql_connect($dbhost,$dbuser,$dbpass,$dbname);

// Set the mode variable which controls what we do in app
if (!isset($PARAMS['mode'])){
    $mode = 'projectlist';
} else{
    $mode=$PARAMS['mode'];
}
if(isset($PARAMS['projno'])){
	$projno=$PARAMS['projno'];
	$mode="showproject";
}
else {
	$projno=-1;// illegal project no
	$mode='projectlist';
}
debug_string("projno",$projno);
debug_string("mode",$mode);

// switch on mode to control application
switch($mode){
    case "projectlist":
        debug_string("projectlist");
        projectlist();
        break;
    case "help":
    case "showproject":
        debug_string("showproject");
        if($projno>=0)showproject($projno);
        else projectlist("Illegal project number.");
    	break;
    default:
        debug_string("default");
        projectlist("Illegal mode.");
}
break_mysql_connect($link);
exit();
/*************************************/
/** FUNCTIONS ***********************/
/*************************************/
/***********************
 * HELP
 ***********************/
function help(){
	debug_string("help()");

}
/***********************
 * projectlist
 ***********************/
function projectlist($error=""){
	global $link;
	debug_string("projectlist($error)");
	print HTMLHead();
//	<div id="sidebar" style="float:right; height:632px" ></div>
echo <<<EOF1
		<hr>
		<div id="projectlisttitle"><h1>Menconi's Project List</h1></div>
		<hr>
			<div id="projecttable">
				<table >
					<span id="tablehead">
						<tr>
							<th>Project</th>
							<th>Category</th>
							<th>Description</th>
						</tr>
					</span>
					<div id="tablebody">

EOF1;
	$sql = "select id,title,category,description from projects where active=1";
	$projects = MYSQLGetData($link,$sql);
	foreach($projects as $project){
		$title = $project['title'];
		$category = $project['category'];
		$id=$project['id'];
		$desc = $project['description'];
		print"\t\t\t\t\t<tr><td><a href=\"index.php?projno=$id\">$title</a></td>\n\t\t\t\t\t<td>$category</td>\n\t\t\t\t\t<td>$desc</td></tr>\n\n";
	}

	print"\t\t\t\t\t</div>\n";
	print "\t\t\t\t</table>\n";
	print "\t\t\t</div>\n";
	if($error!="")print "<span id=\"errormessage\"></br>Error: $error</br></span>\n";
	print HTMLFoot();

}
/***********************
 * showproject
 ***********************/
function showproject($projno){
	global $link;
	debug_string("showproject($projno)");
	$sql = "Select * from projects where id='$projno' and active=1";
	debug_string($sql);
	$projects = MYSQLGetData($link,$sql);
	if(count($projects)==0) {
		projectlist("No records found.");
		return;
	}

	$project=$projects[0];
	$pix1=$project['pix1'];
	$pix2=$project['pix2'];
	$pix3=$project['pix3'];

	print HTMLHead();
	//print "\t\t<div id=\"sidebar\"></div>\n";
	print "\t\t<div id=\"info\">\n";

	if(strlen($pix1)>5)print "\t\t\t<dif id=\"topimage\"><img src=\"".$pix1."\" height=\"150\" align=\"middle\" class=\"center\"></div></br>\n";
	print "\t\t\t<div id=\"projectdetailhead\"><h1>".$project['title']."</h1></div>\n";
	print "\t\t\t<div id=\"projectdetailsub\"><h2>(Category: ".$project['category'].")</h2></div>\n";
	//print "\t\t\t<P><i>".$project['description']."</i></p>\n";


	print "\t\t\t<P>".$project['problem']."</p>\n";
	print "\t\t\t<P>".$project['solution']."</p>\n";
	print "\t\t\t<P>".$project['implementation']."</p>\n";
	print "\t\t\t<P>".$project['result']."</p>\n";

	if(strlen($pix2)>5)print "<img style=float:left;  height=80px; src=\"".$pix2."\" width=\"225\">\n";
	if(strlen($pix3)>5)print "<img style=float:right;  height=70px; src=\"".$pix3."\" width=\"250\">\n";
	print "<hr>\n";
	if(strlen($project['notes'])>5) print "<p><strong><em>Notes:</em></strong></p><p>".$project['notes']."</p>\n";
	print "\t\t\t<hr>\n\t\t</div>\n\t</div>\n";
	print HTMLFoot();
}
/***********************
 * HTMLHead
 ***********************/
function HTMLHead(){
	debug_string("HTMLHead()");
$head =  <<<EOF
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;
		charset=iso-8859-1">
		<meta name="description" content="project list">
		<meta name="">
		<meta name="DISTRIBUTION" content="IU">
		<meta name="ROBOTS" content="noindex,nofollow">
		<meta name="revisit-after" content="30 days">
		<link rel="SHORTCUT ICON" href="proj.jpg"/>
		<link rel="stylesheet" type="text/css" href="projects.css" />
		<meta name="copyright" content="Copyright © 2014 Dave Menconi, All
		Rights Reserved">
		<meta name="author" content="Dave Menconi">
		<meta name="rating" content="PG-13">
		<Title>Project List</title>
	</head>
<body >
<!--BEGIN CONTAINER -->
<div id="container">
	<div id="header">
		<div id="logo"></div>
		<div id="navbar">
<!-- REMOVE
			<ul>
				<a href="http://index.php" style="text-decoration:none"><li>About</li></a>
				<a href="http://index.php" style="text-decoration:none"><li>Services</li></a>
				<a href="http://index.php" style="text-decoration:none"><li>e-mail</li></a>
			</ul>
-->
		</div>
	</div>
<!--CONTENT AREA -->
	<div id="content_area">

EOF;
	return $head;
}
/***********************
 * HTMLFOOT
 ***********************/
function HTMLFOOT(){
	debug_string("HTMLFOOT()");
$foot =  <<<EOF
<!-- FOOTER -->
	<div id="footer">
		<div id="copyright">Copyright © 2014 Dave Menconi, All	Rights Reserved</div>
	</div>
</div>
<!-- END CONTAINER  -->
</body>
</html>
EOF;
return $foot;
}

?>
