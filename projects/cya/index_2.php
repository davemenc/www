<?php
/* $Id */
/****************************************
* index.php -- project page
*****************************************/
include_once("config.php");
include_once("../library/miscfunc.php");
include_once("../library/debug.php");
include_once("../library/loc_login.php");
include_once("../library/mysql.php");
include_once("../library/htmlfuncs.php");
log_on();
//debug_on();
debug_string("----------------- START PROJECTS --------------------------------------");

// set the params array up
$PARAMS = array_merge($_POST,$_GET);

// open a link to the database (used everywhere)
$link = make_mysql_connect($dbhost,$dbuser,$dbpass,$dbname);

// Set the mode variable which controls what we do in app
if (!isset($PARAMS['mode'])){
    $mode = 'projectlist';
} else{
    $mode=$PARAMS['mode'];
}
if(isset($PARAMS['projno']))	$projno=$PARAMS['projno'];
else $projno=-1;// illegal project no


// switch on mode to control application
switch($mode){
    case "projectlist":
        debug_string("projectlist");
        projectlist();
        break;
    case "login":
        debug_string("login");
        $login = loc_GetAuthenticated($PARAMS,$link,$appword,"login",false,0,"Projects Page",'#F0F0c0',"index.php",true);
        print "logged in!</br>\n";
        break;
    case "logout":
        debug_string("logout");
        loc_delete_cookie();
        JumpTo();
        break;
    case "help":
    case "showproject":
        debug_string("showproject");
        if($projno>=0)showproject($projno);
        else projectlist("Illegal project number.");
    	break;
    case "test":
    	 debug_string ("test");
    	 break;
   	case "edit":
    	 debug_string ("edit");
   		$login = loc_GetAuthenticated($PARAMS,$link,$appword,"login",false,0,"Project List",'#F0F0c0',"index.php",true);
		if(!login) projectlist("Not Authorised");
		else if($projno>=0)editproject($projno);
        else projectlist("Illegal project number.");
		break;
   	case "add":
    	 debug_string ("add");
   		$login = loc_GetAuthenticated($PARAMS,$link,$appword,"login",false,0,"Project List",'#F0F0c0',"index.php",true);
		if(!login) projectlist("Not Authorised");
		else if($projno>=0)addproject($projno);
        else projectlist("Illegal project number.");
		break;
   	case "delete":
    	 debug_string ("delete");
   		$login = loc_GetAuthenticated($PARAMS,$link,$appword,"login",false,0,"Project List",'#F0F0c0',"index.php",true);
		if(!login) projectlist("Not Authorised");
		else if($projno>=0)deleteproject($projno);
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
echo <<<EOF1
		<div id="projectlisthead"><h1>Project List</h1></div>
			<div id="projecttable">
				<table border=1>
					<span id="tablehead">
						<tr>
							<th>Project</th>
							<th>Category</th>
							<th>Description</th>
						</tr>
					</span>
					<span id="tablebody">
EOF1;
	$sql = "select id,title,category,description from projects";
	$projects = MYSQLGetData($link,$sql);
	foreach($projects as $project){
		$title = $project['title'];
		$category = $project['category'];
		$id=$project['id'];
		$desc = $project['description'];
		print"<tr><td><a href=\"index.php?mode=showproject&projno=$id\">$title</a></td><td>$category</td><td>$desc</td></tr>\n";
	}

	print"\t\t\t\t\t\t</span>\n\t\t\t\t\t</table>\n\t\t\t\t</div>\n";
	if($error!="")print "<span id=\"errormessage\"></br>Error: $error</br></span>\n";
	print HTMLFoot();

}
/***********************
 * showproject
 ***********************/
function showproject($projno){
	global $link;
	debug_string("showproject($projno)");
	$sql = "Select * from projects where id=$projno and active=1";
	debug_string($sql);
	$projects = MYSQLGetData($link,$sql);
	if(count($projects)==0) {
		projectlist("No records found.");
		exit();
	}


	$project=$projects[0];
	$pix1=$project['pix1'];
	$pix2=$project['pix2'];
	$pix3=$project['pix3'];

	print HTMLHead();

	print "<h1>".$project['title']."</h1>\n";
	print "<h4>(Category: ".$project['category'].")</h4>\n";
	if(strlen($pix1)>5)print "<img src=\"".$pix1."\" width=\"500\">\n";
	//print "<P><i>".$project['description']."</i></p>\n";
	print "<P>".$project['problem']."</p>\n";
	print "<P>".$project['solution']."</p>\n";
	print "<P>".$project['implementation']."</p>\n";
	if(strlen($project['notes'])>5) print "<h2>Notes</h2><P>".$project['notes']."</p>\n";
	if(strlen($pix2)>5)print "</br><img src=\"".$pix2."\" width=\"300\">\n";
	if(strlen($pix3)>5)print "<img src=\"".$pix3."\" width=\"300\">\n";

	print HTMLFoot();
}
/***********************
 * editproject
 ***********************/
function editproject($projno){
	global $link;
	debug_string("editproject($projno)");
}
/***********************
 * deleteproject
 ***********************/
function deleteproject($projno){
	global $link;
	debug_string("deleteproject($projno)");
}
/***********************
 * addproject
 ***********************/
function addproject($projno){
	global $link;
	debug_string("addproject($projno)");
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
		<link rel="SHORTCUT ICON" href="http://http://menconi.com/images/proj.jpg"/>
		<link rel="stylesheet" type="text/css" href="projects.css" />
		<meta name="copyright" content="Copyright © 2011 Dave Menconi, All
		Rights Reserved">

		<meta name="author" content="Dave Menconi">

		<meta name="rating" content="PG-13">
		<Title>Project List</title>
	</head>
	<body >
		<div class="links">
			| <a href="index.php?mode=help">  Help</a> |
			<a href="index.php?mode=logout">Logout</a> |
			<a href="index.php?mode=login">Login</a> |
			<a href="index.php?mode=projectlist">Project List</a> |
		</div>
EOF;
	return $head;
}
/***********************
 * HTMLFOOT
 ***********************/
function HTMLFOOT(){
	debug_string("HTMLFOOT()");
$foot =  <<<EOF
		</br><hr></br>
		<div id="copyright">Copyright © 2014 Dave Menconi, All	Rights Reserved</div>
	</body>
</html>
EOF;
return $foot;
}

?>
