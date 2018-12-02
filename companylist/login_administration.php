<?php
/* $Id: login_administration.php,v 1.2 2005/11/29 20:26:03 dave Exp $ */
        ini_set('display_errors', 'Off');
		 
/*******************************
 * LOCAL LOGIN USER ADMIN 
 * 
 * Copyright 2005 Dave Menconi
 * All rights reserved. 
 *******************************/
 // INCLUDES
	include_once("config.php");
	include_once("../library/miscfunc.php");
	include_once("../library/debug.php");
	include_once("../library/loginutil.php");
	include_once("../library/mysql.php");
	include_once("../library/htmlfuncs.php");
	include_once("../library/loc_login.php");

	//Connect to the database 
	 $link = make_mysql_connect($dbhost,$dbuser,$dbpass,$dbname);
	// GET THE POST AND GET VARIABLES
	$PARAMS = array_merge($HTTP_POST_VARS,$HTTP_GET_VARS);	
	$appcookie = get_cookie_name($appnum);
	GetAuthenticated($appnum, $appword);
	//debug_on();

	//debug_array("PARAMS",$PARAMS);
    // This string is updated by the source control system and used to track changes.
	$rcsversion = '$Id: login_administration.php,v 1.2 2005/11/29 20:26:03 dave Exp $';
    $rcsversion = str_replace("$","",$rcsversion);
    $rcsversion = substr($rcsversion,strpos($rcsversion,",v")+2);
    $localversion = $version . "<br>RCSversion: " . $rcsversion;
	if (!isset($PARAMS['mode'])) $mode="Main";
	else $mode=$PARAMS['mode'];
	//debug_string("mode",$mode);
	switch($mode){
		case "Main":
			//Display_Main();
			break;
		case "parseNewUser":
			//debug_string("parseNewUser" );
			parseNewUser();
			break;
		case "parseUser":
			//debug_string("parseUser" );
			parseUser();
			break;
	}//switch

	//Close Conection to database
	Display_Main();
	break_mysql_connect($link);
	exit();
function parseNewUser(){
	global $link,$PARAMS;
	//debug_string("parseNewUser()");
	$actword = GenerateRandomWord();

	$sql = "insert into user (username,userpass,email,name,temppw,type,actword) values ('".$PARAMS['UserName']."','".$PARAMS['Password']."','".$PARAMS['Email']."','".$PARAMS['Name']."','','".$PARAMS['type']."','".$actword."')";
    //debug_string("sql",$sql);
    $result = mysql_query($sql,$link) or die(mysql_error());
}
function parseUser(){
	global $link,$PARAMS;
	$sql = "update user set username='".$PARAMS['username']."',userpass='".$PARAMS['userpass']."',name='".$PARAMS['Name']."',type='".$PARAMS['type']."',email='".$PARAMS['email']."'   where id='".$PARAMS['userno']."'";
	//debug_string("sql",$sql);
	$result = mysql_query($sql,$link) or die(mysql_error());
}
function Display_Main(){
	global $link,$localversion;
	//debug_string("Display_Main()");
	Display_Login_Admin_Form($localversion,$lastmodified);
}

function Display_Login_Admin_Form($version,$lastmodified){
	global $PARAMS,$link;
	//debug_string("Display_Login_Admin_Form");
	$users = MYSQLComplexSelect($link,array("*"),array("user"),array(),array(),0);
	//$apps = MYSQLComplexSelect($link,array("*"),array("applications"),array(),array(),0);
	//$privs = MYSQLComplexSelect($link,array("appnum","appname","description","user.userno","username","email"),array("applications","user","userprivs"),array("applications.appnum=appno","userprivs.userno=user.userno"),array("username,appname"),0);

	$thisapp = $_SERVER['SCRIPT_NAME'];
	$pos = strpos($thisapp,"/",1)+1;
	//debug_string("pos",$pos);
	$thisapp = substr($thisapp,$pos);
//	debug_string("thisapp",$thisapp);

	Display_Generic_Header("Local User Admin Form","#cca5FF");
	echo <<< EOF
	<h1>Local User Administration Page</h1>
	<h2>Edit Existing Users</h2>
	<table border=0>
	<tr>
		<td><b>User Name</td>
		<td><b>Password</td>
		<td><b>Email</td>
		<td><b>Name</td>
		<td><b>Type</td>
	</tr>
EOF;
//debug_array("users",$users);
	foreach($users as $user){
	//debug_array("user",$user);
		$userno = $user['id'];
		$username = $user['username'];
	//debug_string("username",$username);	
		$userpass = $user['userpass'];
		$email = $user['email'];
		$name = $user['name'];
		//debug_string("type",$user['type']);
		$inactiveselect=$activeselect=$adminselect="";
		switch ($user['type']){
			case "active":
				$activeselect=' selected="selected" ';
				//debug_string("type active",$user['type']);
				break;
			case "admin":
				$adminselect=' selected="selected" ';
				//debug_string("type admin",$user['type']);
				break;
			case "inactive":
			default:
				//debug_string("type inactive",$user['type']);
				$inactiveselect = ' selected="selected" ';
			
				break;
		};
	echo <<< EOF2
		<form action="$thisapp" method="post">
		<input type=hidden name="mode" value="parseUser">
		<tr>
			<input type="hidden" name="userno"  value="$userno">
			<td><input type="text" name="username" size="20" value="$username"></td>
			<td><input type="text" name="userpass" size="20" value="$userpass"></td>
			<td><input type="text" name="email" size="30" value="$email"></td>
			<td><input type="text" name="Name" size="30" value="$name"></td>
			<td><select name="type"><option value="inactive" $inactiveselect>Inactive</option><option value="active" $activeselect>Active</option><option value="admin" $adminselect>Admin</option></select>
			<td colspan=2 align=center><input type=submit value="Save User"></td>
		</tr>
		</form>
EOF2;
	}
		print "</table> <hr>";
		$inactiveselect=$activeselect=$adminselect="";
	echo <<< EOF5
		<hr>
		<h2>Create New User</h2>
		<table border=0>
		<form action="$thisapp" method="post">
		<input type="hidden" name="mode" value="parseNewUser">
		<tr>
			<td><b>User Name</td>
			<td><b>Password</td>
			<td><b>Email</td>
			<td><b>Name</td>
			<td><b>Type</td>
		</tr>
		
		<tr>
		<td>
		<td>
		
		</tr>
		<tr>
			<td><input type="text" name="UserName" size="20" ></td>
			<td><input type="text" name="Password" size="20" ></td>
			<td><input type="text" name="Email" size="30" ></td>
			<td><input type="text" name="Name" size="30"></td>
			<td><select name="type"><option value="inactive" $inactiveselect>Inactive</option><option value="active" $activeselect>Active</option><option value="admin" $adminselect>Admin</option></select>
			<td><input type=submit value="Create User"></td>
		</tr>
		</form>
		</table>
EOF5;
	//Display_Generic_Footer($version,$lastmodified);
    Display_Generic_Footer($version,date("F d Y H:i:s", getlastmod()));
}
/*function GenerateRandomWord($minlen=10,$maxlen=25){
    $charset = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $charsetlen = strlen($charset);
    // generate a length value that various widely but tends to be around 20 characters
	$maxlen = $maxlen-12;
	if ($minlen<1) $minlen=1;
	if ($maxlen<6) $maxlen=6;
    $len=mt_rand($minlen,$maxlen)+mt_rand(0,4)+mt_rand(0,4)+mt_rand(0,4);
    for ($i = 1; $i <= $len; $i++){
        $n = mt_rand(0,$charsetlen-1);
        $word = $word.$charset[$n];
    }
    //debug_string("word",$word);
    return $word;
}
*/

/*
function parseNewApp(){
	global $link,$PARAMS;
	debug_string("parseNewApp()");
	$appword = GenerateRandomWord(10,18);
	$expire = 0;

	$sql = "insert into applications (appname,appnum,appword,description,expire) values ('".$PARAMS['AppName']."','".$PARAMS['AppNum']."','".$appword."','".$PARAMS['Desc']."','".$expire."')";
	debug_string("sql",$sql);
	$result = mysql_query($sql,$link) or die(mysql_error());
}
function parseApp(){
	global $link,$PARAMS;
//	Array ( [mode] => parseApp [appnum] => 3 [appname] => Test2 [appword] => U1qzdB5Jd2tu [description] => Test Application [expire] => 0 )
	$sql = "update applications set expire='".$PARAMS['expire']."',description='".$PARAMS['description']."',appname='".$PARAMS['appname']."',appword='".$PARAMS['appword']."'   where appnum='".$PARAMS['appnum']."'";
	debug_string("sql",$sql);
	$result = mysql_query($sql,$link) or die(mysql_error());

}
*/
/*function parseAddPrivs(){
	global $link,$PARAMS;
	debug_string("parseAddPrivs()");	
	$userno = substr($PARAMS['user'],1);
	$appno = substr($PARAMS['app'],1);
	debug_string("userno",$userno);
	debug_string("appno",$appno);
	$sql = "insert userprivs (userno,appno) values ('$userno','$appno')";
	debug_string("sql",$sql);
	$result = mysql_query($sql,$link) or die(mysql_error());
}
function parseDelPrivs(){
	global $link,$PARAMS;
	debug_string("parseDelPrivs()");
	foreach ($PARAMS as $key=>$value){
		debug_string("$key",$value);
		debug_string("substr0",substr($key,0,1));
		debug_string("strpos1", strpos($key,"U"));
		if (substr($key,0,1)=="A" && strpos($key,"U")>0){
			$pos = strpos($key,"U");
			$appno = substr($key,1,$pos-1);
			debug_string("appno",$appno);
			$userno = substr($key,$pos+1);
			debug_string("userno",$userno);
			$sql = "delete from userprivs where userno='$userno' and appno='$appno' limit 1";
			debug_string("sql",$sql);
			$result = mysql_query($sql,$link) or die(mysql_error());
		}
	}
}
*/
/* 
$Log: login_administration.php,v $
Revision 1.2  2005/11/29 20:26:03  dave
final changes

Revision 1.3  2005/11/16 22:47:01  dave
added activation word
removed all debug lins

Revision 1.2  2005/11/16 22:39:22  dave
fixed for current status

Revision 1.1  2005/11/16 21:14:18  dave
Initial revision

*/
?>
