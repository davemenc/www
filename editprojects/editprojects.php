<?php
/****************************************
* editprojects.php -- Project web page editor
*****************************************/
include_once("../projects/config.php");
include_once("../library/miscfunc.php");
include_once("../library/debug.php");
include_once("../library/loc_login.php");
include_once("../library/mysql.php");
include_once("../library/htmlfuncs.php");
log_on();
//debug_on();
debug_string("----------------- START PROJECTS EDITOR --------------------------------------");
// set the params array up
$PARAMS = array_merge($_POST,$_GET);
debug_array("PARAMS",$PARAMS);

// open a link to the database (used everywhere)
$link = make_mysql_connect($dbhost,$dbuser,$dbpass,$dbname);

// Set the mode variable which controls what we do in app
if (!isset($PARAMS['mode'])){
    $mode = 'projectlist';
} else{
    $mode=MYSQLSanitize($PARAMS['mode']);
}
if(isset($PARAMS['projno']))	$projno=MYSQLSanitize($PARAMS['projno']);
else $projno=-1;// illegal project no
debug_string("projno",$projno);
// switch on mode to control application
switch($mode){
    case "projectlist":
        debug_string("projectlist");
        projectlist();
        break;
   	case "edit":
    	debug_string ("edit");
		if($projno>=0)editproject($projno,$PARAMS);
        else projectlist("Illegal project number.");
		break;
	case "parseedit":
    	 debug_string ("parseedit");
		if($projno>=0)$result = parseedit($projno,$PARAMS);
        else projectlist("Illegal project number.");

        $msg="";
        if ($result<0)$msg = "Not Authoritized.";
        projectlist($msg);
		break;
   	case "add":
    	debug_string ("add");
		addproject();
		break;
	case "parseadd":
    	debug_string ("parseadd");
		if (parseadd($PARAMS)<0) projectlist("Not Autorhized.");
		addproject();
		break;
   	case "delete":
    	debug_string ("delete");
 		if($projno>=0)deleteproject($projno);
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
		<div id="projectlisthead"><h1>Edit Projects</h1></div>
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
	$sql = "select id,title,category,description from projects where active=1";
	$projects = MYSQLGetData($link,$sql);
	foreach($projects as $project){
		$title = $project['title'];
		$category = $project['category'];
		$id=$project['id'];
		$desc = $project['description'];
		print"<tr><td><a href=\"editprojects.php?mode=edit&projno=$id\">$title</a></td><td>$category</td><td>$desc</td></tr>\n";
	}

	print"\t\t\t\t\t\t</span>\n\t\t\t\t\t</table>\n\t\t\t\t</div>\n";
	if($error!="")print "<span id=\"errormessage\"></br>Error: $error</br></span>\n";

print "</br><a href=\"editprojects.php?mode=add\">Add Project</a></br>\n";
	print HTMLFoot();

}
/***********************
 * deleteproject
 ***********************/
function deleteproject($projno){
	global $link;
	debug_string("deleteproject($projno)");
	$sql = "update projects set active=\"0\" where id=$projno";
	debug_string("delete sql",$sql);
	mysql_update($link,$sql,true);
}
/***********************
 * undeleteproject
 ***********************/
function undeleteproject($projno){
	global $link;
	debug_string("undeleteproject($projno)");
	$sql = "update projects set active=\"1\" where id=$projno";
	debug_string("undelete sql",$sql);
	do_mysql($link,$sql,true);
}
/***********************
 * addproject
 ***********************/
function addproject(){
	global $link;
	debug_string("addproject()");

$form = <<<EOF
<a href="editprojects.php">Return to Edit List</a></br>
<form name="add" action="editprojects.php" method="post">
<input type="hidden" name="mode" value="parseadd">
<li>Title: <input type=text name="title" value="" size=4>
<li>Sequence: <input type=text name="seq" value="" size=5>
<li>Type:	<input type=text name="type" value="" size=5>
<li>Picture 1:	<input type=text name="pix1" value="" size=5>
<li>Picture 2:	<input type=text name="pix2" value="" size=5>
<li>Picture 3:	<input type=text name="pix3" value="" size=5>
<li>Description:<textarea name="description" cols=80 rows=4></textarea>
<li>Problem:<textarea name="problem" cols=80 rows=4></textarea>
<li>Solution:<textarea name="solution" cols=80 rows=4></textarea>
<li>Implementation:<textarea name="implementation" cols=80 rows=4></textarea>
<li>Result:<textarea name="result" cols=80 rows=4></textarea>
<li>Sourcecode:<textarea name="sourcecode" cols=80 rows=4></textarea>
<li>Notes:<textarea name="notes" cols=80 rows=4></textarea>
<li>AppWord: <input type=text name="AW" value="" size=12>
<li><input type="submit" value="Update Item">
</form>
EOF;
	print HTMLHead();
	print $form;
	print HTMLFOOT();
}
/***********************
 * parseadd
 ***********************/
function parseadd($PARAMS){
	global $link,$magicpassword;
	debug_string("parseadd(params)");
	debug_array("params",$PARAMS);
	debug_string("magicpw",$magicpassword);
	if(!isset($PARAMS['AW']) || $PARAMS['AW']!=$magicpassword) return -1;
	$paramToDB = array("title"=>"title", "seq"=>"seq", "type"=>"category", "pix1"=>"pix1", "pix2"=>"pix2", "pix3"=>"pix3", "description"=>"description", "problem"=>"problem", "solution"=>"solution", "implementation"=>"implementation",  "result"=>"result","sourcecode"=>"sourcecode", "notes"=>"notes");
	$values = array();
	foreach($paramToDB as $param=>$field){
		if(isset($PARAMS[$param])) $values[$field]=MYSQLSanitize($PARAMS[$param]);
		else $values[$field]="";
	}
	$fieldList = $valueList = "(";
	$first = true;
	foreach($values as $field=>$value){
		if(!$first){
			$fieldList .= ", ";
			$valueList .= ", ";
		}
		else $first = false;

		$fieldList .= $field;
		$valueList .= "'".$value."'";
	}
	$fieldList .= ")";
	$valueList .= ")";
	debug_string("fields list",$fieldList);
	debug_string("value list",$valueList);
	$sql = "insert into projects $fieldList values $valueList;";
	debug_string("parseadd SQL",$sql);
	do_mysql($link,$sql,true);
	return 0;
}
/***********************
 * editproject
 ***********************/
function editproject($projectid){
	global $link;
	$paramToDB = array("title"=>"title", "seq"=>"seq", "type"=>"category", "pix1"=>"pix1", "pix2"=>"pix2", "pix3"=>"pix3", "description"=>"description", "problem"=>"problem", "solution"=>"solution", "implementation"=>"implementation", "result"=>"result","sourcecode"=>"sourcecode", "notes"=>"notes");
	debug_string("editproject($projectid)");
	$sql = "Select * from projects where id=$projectid and active=1";
	debug_string($sql);
	$projects = MYSQLGetData($link,$sql);
	if(count($projects)==0) {
		projectlist("No records found.");
		return;
	}
	if(count($projects)>1) {
		projectlist("Invalid project id $projectid.");
		return;
	}
	$project = $projects[0]; // only one
	foreach($paramToDB as $formname=>$fieldname){
		$$formname = $project[$fieldname];
	}
$form = <<<EOF2
<form name="edit" action="editprojects.php" method="post">
<input type="hidden" name="projno" value="$projectid">
<input type="hidden" name="mode" value="parseedit">
<li>Title: <input type=text name="title" value="$title" size=20>
<li>Sequence: <input type=text name="seq" value="$seq" size=7>
<li>Type:	<input type=text name="type" value="$type" size=10>
<li>Picture 1:	<input type=text name="pix1" value="$pix1" size=15>
<li>Picture 2:	<input type=text name="pix2" value="$pix2" size=15>
<li>Picture 3:	<input type=text name="pix3" value="$pix3" size=15>
<li>Description:<textarea name="description" cols=80 rows=4>$description</textarea>
<li>Problem:<textarea name="problem" cols=80 rows=4>$problem</textarea>
<li>Solution:<textarea name="solution" cols=80 rows=4>$solution</textarea>
<li>Implementation:<textarea name="implementation" cols=80 rows=4>$implementation</textarea>
<li>Result:<textarea name="result" cols=80 rows=4>$result</textarea>
<li>Sourcecode:<textarea name="sourcecode" cols=80 rows=4>$sourcecode</textarea>
<li>Notes:<textarea name="notes" cols=80 rows=4>$notes</textarea>
<li>Delete: <input type=checkbox name="delete" value="1" 0>
<li>AppWord: <input type=text name="AW" value="" size=12>
<li><input type="submit" value="Update Item">

</form>
EOF2;
	print HTMLHead();
	print $form;
	print HTMLFOOT();
}
/***********************
 * parseedit
 ***********************/
function parseedit($projectid,$PARAMS){
	global $link,$magicpassword;
	debug_string("parseadd(params)");
	debug_array("params",$PARAMS);
	debug_string("magicpw",$magicpassword);
	if(!isset($PARAMS['AW']) || $PARAMS['AW']!=$magicpassword) return -1;
	$paramToDB = array("title"=>"title", "seq"=>"seq", "type"=>"category", "pix1"=>"pix1", "pix2"=>"pix2", "pix3"=>"pix3", "description"=>"description", "problem"=>"problem", "solution"=>"solution", "implementation"=>"implementation", "result"=>"result","sourcecode"=>"sourcecode", "notes"=>"notes");
	debug_array("paramToDB",$paramToDB);
// do the delete tag if it exists
	if(isset($PARAMS['delete'])){
		if($PARAMS['delete']==1) {
			deleteproject($projectid);
			return;
		} else {
			undeleteproject($projectid);
			return;
		}
	}
	$values = array();
	foreach($paramToDB as $param=>$field){
		if(isset($PARAMS[$param])) {
			$value = MYSQLSanitize($PARAMS[$param]);
			$sql = "update projects set $field='$value' where id=$projectid";
			debug_string("parseedit SQL",$sql);
			do_mysql($link,$sql,true);
		}
	}
	return 0;
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
		<meta name="description" content="Edit Projects">
		<meta name="">
		<meta name="DISTRIBUTION" content="IU">
		<meta name="ROBOTS" content="noindex,nofollow">
		<meta name="revisit-after" content="30 days">
		<link rel="stylesheet" type="text/css" href="http://localhost/projects/projects.css" />
		<meta name="copyright" content="Copyright © 2014 Dave Menconi, All
		Rights Reserved">

		<meta name="author" content="Dave Menconi">

		<meta name="rating" content="PG-13">
		<Title>Edit Projects</title>
	</head>
	<body >
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
/***********************
 * MYSQLSanitize
 ***********************/
function MYSQLSanitize($s){
	debug_string("MYSQLSanitize($s)");
    if(array_key_exists('HTTP_HOST',$_SERVER)) $host = $_SERVER['HTTP_HOST'];
    else $host="";
    if ($host == "menconi.com") return $s;
	$s = str_replace ('\\','\\\\',$s);
	$s = str_replace ("'","\\'",$s);
	$s = str_replace ("_",'\_',$s);
	$s = str_replace ("%","\\%",$s);
	$s = str_replace ("\"",'\"',$s);
	$s = str_replace ("\t",'\\t',$s);
	$s = str_replace ("\x08","\\b",$s);
	$s = str_replace ("\0",'\\0',$s);
	$s = str_replace ("\n",'\\n',$s);
	$s = str_replace ("\r",'\\r',$s);
	$s = str_replace ("\x1a","\\z",$s);
	return $s;
}
?>