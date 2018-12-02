<?php
$dbname = "keybox";
$dbuser="root";
$dbpass="5guvt4wk";
$dbhost="127.0.0.1";
$masterPW="8cSGb7oJEtnI";

$version = "1.0";

$DEBUG = False;// debug information is displayed -- set false in production
$ERRORS = True; // errors are returend -- set false in production
error_reporting(E_ALL);
// get perameters from post and get
$PARAMS = FilterSQL(array_merge($_POST,$_GET));

// set the action variable
if (isset($PARAMS['act'])){$act=$PARAMS['act'];}
else $act = "help";
Debug(__LINE__,$act);

// get & check the password


if(isset($PARAMS['pw'])) {$pw = $PARAMS['pw'];}
else{
	Error(__LINE__,1);
	exit();
}
if($pw!=$masterPW){
	Error(__LINE__,2);
	exit();
}

// Get other 2 paramters: name, date (yyyy-mm-dd),
if(isset($PARAMS['name'])) {$name = $PARAMS['name'];}
else $name = "";
Debug(__LINE__,$name);

if(isset($PARAMS['date'])) {$date = $PARAMS['date'];}
else $date = date("Y-m-d");
Debug(__LINE__,$date);

Debug(__LINE__,date("m/d/Y"));

// Create link to DB
$link = mysql_connect($dbhost, $dbuser, $dbpass,true);
if(!$link) Error(__LINE__,7,"Could not open connection to db, $dbhost, $dbuser,pass.");
$result = mysql_select_db($dbname,$link);
if(!$result) Error(__LINE__,8,"Could not select db $dbname.");
switch($act){
	case "add":
		Debug(__LINE__,"do_add");
		do_add($link, $name);
		do_list($link);
		break;
	case "list":
		Debug(__LINE__,"do_list");
		do_list($link);
		break;
	case "fetch":
		Debug(__LINE__,"do_fetch");
		do_fetch($link,$name,$date);
		break;
	case "help":
	default:
		Debug(__LINE__,"do_help");
		do_help();
}
mysql_close($link);
exit();
/******************** FUNCTIONS ***************************/

/**********************************************************
 * do_list();
 * $link -- link to db
 * Return -- nothing
 * side effect -- displays page with keys (and add form)
 **********************************************************/
function do_list($link){
	global $version;

	Debug(__LINE__,"do_list(link)");

	print display_header("KeyBox List", "");
	print '<h1>Named Key Storage</h1>'."\n";

	// Named list
	print '<a name="list"><h2>List of Names</h2>'."\n";
	print '<a href="#list"> List of Key Names</a> | <a href="#addname">Add a Name</a></br></br>'."\n";
	$sql = "select name from names order by name";
	Debug(__LINE__,$sql);
	$names = MYSQLGetData($link,$sql);
	foreach ($names as $name){

		print "<li>".$name['name'];
	}
	print "<hr>";
	print '<a name="addname"><h2>Add a Name</h2>'."\n";
	print '<a href="#list"> List of Key Names</a> | <a href="#addname">Add a Name</a></br></br>'."\n";
	print '<form action="keybox.php">';
	print 'Name: <input type="text" name="name"> </br>';
	print 'Password: <input type="password" name="pw"></br>';
	print '<input type="submit" value="Save">';
	print '<input type="hidden" name ="act" value="add">';
	print "</form>";
	print display_footer("Version $version","Copyright &copy; 2015 Dave Menconi. All rights reserved.");
}

/**********************************************************
 * do_fetch();
 * $link -- link to db
 * $name -- name of key we want to fetch
 * $date -- date of key we want to fetch
 * Return -- nothing
 * side effect -- returns just that key
 **********************************************************/
function do_fetch($link,$name,$date){
	Debug(__LINE__,"do_fetch(link,$name,$date)");
	$sql = "select id,name from names where name='$name'";
	Debug(__LINE__,$sql);
	$names =  MYSQLGetData($link,$sql);

	Debug(__LINE__, "Count of names: ".count($names));
	if(count($names)==0) Error(__LINE__,8,"No such name as $name in fetch.");// name not in database, user error
	else if(count($names)>1) Error(__LINE__,3,"Duplicate name $name in fetch.");//multiple names in database; big Bug
	if ($name != $names[0]['name']) Error(__LINE__,9,"Names don't match. Request name=$name. DB name=".$names[0]['name'].".");// this is impossible, I think
	$id = $names[0]['id'];

	$sql = "select onekey from keybox  where name='$id' and date='$date'";
	Debug(__LINE__,$sql);
	$keys =  MYSQLGetData($link,$sql);
	if (count($keys)==1) print $keys[0]['onekey'];
	else if (count($keys)>1) Error(__LINE__,5);
	else {// named key is not in keybox
		$key = gen_key($name,$date);
		$sql = "insert into keybox (name,date,onekey) values ('$id','$date','$key') ";
		Debug(__LINE__,$sql );
		$result = mysql_query($sql,$link);
		if(!$result) Error(__LINE__,6,"Bad mysql call $sql.");
		print $key;
	}
	Debug(__LINE__,"exiting fettch");
	exit();
}

/**********************************************************
 * do_add($link, $name);
 * $link -- link to db
 * $name -- name of key we want to add
 * Return -- nothing
 * side effect -- adds key to db
 **********************************************************/
function do_add($link,$name){
	Debug(__LINE__,"do_add(link,$name)");
	$sql = "select name from names  where name='$name'";
	$names =  MYSQLGetData($link,$sql);
	if(count($names)>0) {
		Error(__LINE__,3);
		exit();
	}

	$sql = "insert into names (name) values ('$name') ";
	Debug(__LINE__,$sql);
	$result = mysql_query($sql,$link);
	if(!$result) Error(__LINE__,4,"Bad mysql call $sql.");
}
/**********************************************************
 * do_help()
 * Return -- nothing
 * side effect -- displays page with help text
 **********************************************************/
function do_help(){
	global $version;
	Debug(__LINE__,"do_help()");

	$body = <<<EOF
<h1>KeyBox Help page</h1>
This is the help page for the key store KeyBox.
<h2>Purpose</h2>
The purpose of the KeyBox is to store keys. They are stored by name and there is a unique key for each date.
<h2>Structure</h2>
There is a list of key names. For each name a key is generated for each day.
Strictly speaking, the key isn't generated until it's requested but that shouldn't matter.
For each name/date pair one key is stored forever. It can be fetched back at any time by using the name and date.
<h2>Syntax</h2>
This is php program. It uses standard php syntax in the url.</br>
https://domainname.com/keybox.php?&lt;param&gt;=&lt;value&gt;&&lt;param&gt;=&lt;value&gt;...</br>
One of the param/value pairs must always be the password (pw=&lt;password&gt;)</br>
One of the param/value pairs will usually be the action (act=&lt;action&gt;)</br>
<h2>Parameters</h2>
There are four parameters that are possible. Depending on the action some may be optional.
<ul>
	<li>act -- name of the action to be taken; if missing help will be assumed
	<li>pw -- password, always required
	<li>date -- the date in YYYY-MM-DD formet; if missing todays date (at the server) will be used
	<li>name -- name of the key; this is required for the fetch and add commands
</ul>
<h2>Actions</h2>

There are 4 actions available :
<ul>
	<li>help -- this help page; the password (pw) is required
	<li>fetch -- get a key; the password (pw) and the name are required; the date defaults to today (at the server); this fetches a key for this name & date; if this key has never been requested before it will be generated, else it will be retrieved from the database.
	<li>add -- add a name; the password (pw) and the name are required -- this just adds a name to the list
	<li>list -- list all the names (just the names); also shows form to add a key
</ul>
<h2>Setup</h2>
These mysql commands will create the database.
<pre>
create database if not exists keybox;
use keybox;

drop table if exists keybox;
create table keybox(
	id int not null auto_increment primary key,
	name int,
	date tinytext,
	onekey text
);

drop table if exists names;
create table names(
	id int not null auto_increment primary key,
	name tinytext
);

</pre>
EOF;
	print display_header("keybox Help", "");
	print $body;
	print display_footer("Version $version","Copyright &copy; 2015 Dave Menconi. All rights reserved.");
}
/**********************************************************
 * gen_key()
 * name -- the name of the key
 * date -- the date of the key
 * returns a generated key for this name and date
 * side effect -- none
 **********************************************************/
function gen_key($name,$date){
	Debug(__LINE__,"gen_key($name,$date)");
	$r=  mt_rand ().mt_rand ().mt_rand ().mt_rand ().mt_rand ();
	$m = $name.$date;
	Debug(__LINE__,$r);
	Debug(__LINE__,$m);

	$a = hash_hmac('sha256', $m,$r , true);
	Debug(__LINE__,$a);
	$key =  base64_encode($a);
	Debug(__LINE__,$key);
	return $key;
}
/**********************************************************
 * display_header()
 * $title -- the title you want the header to have
 * $css -- the css string you want to use
 * Return -- properly formated html header
 * side effect -- none
 **********************************************************/
function display_header($title, $css){
//	Debug(__LINE__,"display_header($title,$css)");
	$header = <<<EOF
<!DOCTYPE html>
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <meta name="description" content="$title">
   <meta name="ROBOTS" content="noindex,nofollow">
   <meta name="revisit-after" content="30 days">

   <meta name="author" content="Dave Menconi">

   <meta name="rating" content="PG-13">
    <Title>$title</title>
    <link rel="stylesheet" href="$css" type="text/css">
</head>
<body>

EOF;
	return $header;
}
/**********************************************************
 * display_footer()
 * $versionmsg -- the footer will display this version at the bottom
 * $copyright -- the footer will display this
 * Return -- properly formatted html footer
 * side effect -- none
 **********************************************************/

function display_footer($versionmsg="",$copyright=""){
//	Debug(__LINE__,"display_footer($versionmsg,$copyright)");
$footer=<<<EOF
   <hr ><small>$versionmsg</small><hr ><small>$copyright</small>
   </body>
</html>
EOF;
return $footer;
}
function FilterSQL($s){


        $result = array();
        foreach($s as $key=>$value){


                $filtered = $value;
                $filtered = str_replace("\\","\\\\",$filtered);
                $filtered = str_replace("\"","\\\"",$filtered);
                $filtered = str_replace("'","\'",$filtered);
                $filtered = str_replace("\n","\\\n",$filtered);
                $filtered = str_replace("\r","\\\r",$filtered);
                $filtered = str_replace("\x1a","\\\x1a",$filtered);
                $filtered = str_replace("\x00","\\\x00",$filtered);
                $result[$key] = $filtered;

        }
        //$result = str_replace("'","\'",$s);
        //$result = mysql_real_escape_string ($s);

    return $result;
}
/**********************************************************
 * MYSQLGetData()
 * $link -- an active link to a database
 * $sqlcommand -- a properly formatted mysql select command
 * Return -- an array with the data in it
 * side effect -- none
 **********************************************************/
function MYSQLGetData(&$link,$sqlcommand){
	Debug(__LINE__,"MYSQLGetData(link,$sqlcommand)");
    $result = mysql_query($sqlcommand,$link);
    $num_rows = mysql_num_rows($result);
    Debug(__LINE__,$num_rows);
    $data=array();
    for($i=0;$i<$num_rows;$i++){
        $data[]=mysql_fetch_array($result,MYSQL_ASSOC);
    }
    return $data;

}
function Error($line,$errno,$errmsg=""){
	global $ERRORS;
	if (!$ERRORS)exit();
	print "Error #$errno occurred on line $line.  ";

	if ($errmsg=="") {
		switch ($errno){
			case 1:
				$errmsg="No Password";
				break;
			case 2:
				$errmsg="Bad Password";
				break;
			case 4:
				break;
			case 5:
				$errmsg="We have more than one key with the same name and date!";
				break;
			case 6:
				break;
			case 7:
				$errmsg="Could not connect to database.";
				break;
			case 3:
				$errmsg="Duplicate name.";
				break;
			case 8:
				$errmsg = "Missing name";
				break;
			case 9:
				$errmsg = "Names don't match";
			case 10:
			case 11:
			default:
				$errmsg="Unknown error.";
		}
	}
	print "$errmsg</br>\n";
	exit();
}
function Debug($line,$str=""){
	global $DEBUG;
	if (!$DEBUG) return;
	print "Debug: $line, $str</br>\n";
}
/****** DO NOT WRITE BELOW THIS LINE! *****/
?>