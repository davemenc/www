<?php
	include_once "../library/debug.php";
	include_once "../library/miscfunc.php";
function registry(){
	return "$Id: loginutil.php,v 1.2 2005/02/07 18:39:31 dave Exp $";
}
function reg_check_auth_cookie($appcookie,$appword,$username=""){
	//debug_string("check_auth_cookie()");
	//debug_string("appcookie",$appcookie);
	//debug_string("appword",$appword);
	//debug_string("username",$username);
	//debug_array("cookie",$_COOKIE);
	//debug_array("server",$_SERVER);
	if (!isset($_COOKIE[$appcookie])){
		//debug_string("no cookie by that name");
		return false;
	}
	$c_username = $_COOKIE[$appcookie][0];
	if ($username==""){$username=$c_username;}
	//debug_string("c_username",$c_username);
	$c_time = $_COOKIE[$appcookie][1];
	//debug_string("c_time",$c_time);
	$c_hash = $_COOKIE[$appcookie][2];
	//debug_string("c_hash",$c_hash);

	$hash = md5($appword.$username.$c_time);
	if(strcmp($c_username,$username)==0 && strcmp($hash,$c_hash)==0){
		return true;
	}else{
		return false;
	}
}
function reg_get_cookie_name(){
		//debug_string("reg_get_cookie_name()");
	$cookie_name="regauth";
		//debug_string("cookie_name",$cookie_name);
	return $cookie_name;
}

function reg_get_username($appnum=0){
//	debug_string("get_username()");
//	debug_string("appnum",$appnum);

	$appcookie = reg_get_cookie_name();

	if(isset($_COOKIE[$appcookie])){
		return $_COOKIE[$appcookie][0];
	}else{
		return "";
	}
}
function reg_GetAuthenticated($appnum,$appword){
	if (!isset($appnum) || !isset($appword)){
		print "<h1>Error in Application!</h1>";
		print "<P>The application number is not set in GetAuthenticated.  Please contact administrator.";
		exit();
	}
	$appcookie = reg_get_cookie_name($appnum);
	if (reg_check_auth_cookie($appcookie,$appword)){
		return true;
	}	
}
function reg_DisplayLogin(){
    print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"\n";
    print "    \"http://www.w3.org/TR/html4/loose.dtd\">\n";
    print "\n";
    print "<html>\n";
    print "\n";
    print "<head>\n";
    print "     <title>RedWall Registry User Login</title>\n";
    print "\n";
    print "</head>\n";
    print "<body >\n";
    print " <br><br>\n";
    print " <br><br>\n";
    print " <br><br>\n";
    print " <h2>Enter Username & Password</h2>\n";
    print " <h3>$subtitle</h3>\n";
    print "<form method=\"post\" action=\"index.php\">\n";
    print "<table>";
    print "<tr><td><b>Username: </b></td>";
    print " <td><input type=\"text\" name=\"username\"><br></td></tr>\n";
    print "<tr><td><b>Password: </b></td>";
    print "<td> <input type=\"password\" name=\"password\"><br></td></tr>\n";
    print "</table>";
    print " <input type=\"submit\"  value=\"Submit\"><br>\n";
    print " <input type=\"hidden\" name=\"mode\" value=\"authenticate\">\n";
    print $hidden_fields;
    print "</form>\n";
    print "</body>\n";
    print "\n";
    print "</html>\n";
    print "\n";
}
/*
 * $Log$
 */
?>
