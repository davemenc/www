<?php
/* $Id: stats.php,v 1.1 2007/04/18 17:38:45 dave Exp $ */
/****************************************
* stats.php - rail page for numbers game
*****************************************/
include_once("config.php");
include_once("library/miscfunc.php");
include_once("library/debug.php");
include_once("library/loc_login.php");
include_once("library/mysql.php");
include_once("library/htmlfuncs.php");
//debug_on();

// This string is updated by the source control system and
// used to track changes.
$rcsversion = "$Id: stats.php,v 1.1 2007/04/18 17:38:45 dave Exp $";
$rcsversion = str_replace("$","",$rcsversion);
$rcsversion = substr($rcsversion,strpos($rcsversion,",v")+2);
$localversion = $version . "<br>RCSversion: " . $rcsversion;

// set the params array up
$PARAMS = array_merge($HTTP_POST_VARS,$HTTP_GET_VARS);

// open a link to the database (used everywhere)
$link = make_mysql_connect($dbhost,$dbuser,$dbpass,$dbname);

debug_string("Numbers Game Rail Page");
debug_array("params",$PARAMS);

//log this entry
debug_string("LogStats entry");
LogStats($link,$PARAMS);
debug_string("LogStats complete");

// verify login against database; also allows for registration through this app
//$login = loc_GetAuthenticated($PARAMS,$link,$appword,"login",false,0,"Number Guessing Game",'#F0F0c0',"index.php",true);
//if(!login) exit();

// if we just set a cookie, reenter to get a new one
//if (loc_get_userno($link)==-1) JumpTo();
//debug_array("PARAMS",$PARAMS);

// Set the mode variable which controls what we do in app
if (!isset($PARAMS['mode'])){
	$mode = 'stats';
} else{
	$mode=$PARAMS['mode'];
}
//debug_string("MODE",$mode);

// Set the gameno value which controls which game we look at
if (!isset($PARAMS['gameno'])){
	$gameno = 1;
} else {
	$gameno = $PARAMS['gameno'];
}
//debug_string("gameno",$gameno);

// switch on mode to ctontrol application
switch($mode){
	case "logout":
		loc_delete_cookie();
		JumpTo();
		break;
	case "displayguess":
		//Display_GuessPage($gameno);
		break;
	case "parseguess":
		//Parse_GuessPage($PARAMS);
		//Display_GuessPage($gameno);
		break;
	case "stats":
		Display_Stats($gameno);
		break;
	default: 
		Display_GuessPage($gameno);
}	
		
/*************************************/
/** FUNCTIONS ***********************/
/*************************************/

/*************************************
*	Display_Stats()
*	Inputs
*	  $gameno: the game number we're doing
*	return: 
*	  Nothing
*	Sideffects:
*	  Returns form values
*************************************/
function Display_Stats($gameno){
	global $link,$version,$lastmodified,$roundtable,$guesstable,$usertable;
	debug_string("Display_Stat()");
	// get the current round for this game
	$rounds = MYSQLComplexSelect($link,$fieldnames=array("max(roundno) as roundno"), $tablenames=array($roundtable),$where=array("gameno=$gameno"),$order=array(),$debug=0);
	$roundno = $rounds[0]['roundno'];
    //debug_string("roundno",$roundno);

// get the user number & name by looking in cookie
	$userno = loc_get_userno($link);

	debug_string("userno",$userno);
//debug_string("Display_GuessPage2");
	$users = MYSQLComplexSelect($link, $fieldnames=array("name"),$tablenames=array($usertable),$where=array("id=$userno"),$order=array(),$debug=0);
	$name = $users[0]['name'];
debug_string("name",$name);
	
//get the current guess
//debug_string("Display_GuessPage2");
	$guesses = MYSQLComplexSelect($link, $fieldnames=array("*"),$tablenames=array($guesstable),$where=array("gameno=$gameno","roundno=$roundno","userno=$userno"),$order=array(),$debug=0);
	if (count($guesses)<1) 
	{
		$guess = "None";
debug_string ("no guess");
	} else {
		$guess = $guesses[0]['guess'];
debug_string ("guess",$guess);
	}
debug_string("guess",$guess);
	$values = Array("game"=>$gameno,"round"=>$roundno ,"currentguess"=>$guess,"userno"=>$userno,"name"=>$name);
//debug_array("values",$values);
	Display_Generic_Header("Public Rail Page","#d0ffe0");

// display some history
//debug_array("userhist",$userhist);

	$winnerhist =  MYSQLComplexSelect($link, $fieldnames=array("$roundtable.roundno","username,winval","guess"),$tablenames=array($roundtable, $usertable,$guesstable),$where=array("winner=userno","$guesstable.gameno=$roundtable.gameno","$roundtable.roundno=$guesstable.roundno","id=winner","$roundtable.gameno=$gameno"),$order=array("$roundtable.roundno desc limit 15"),0);
	$topwinners =  MYSQLComplexSelect($link, $fieldnames=array("username","count(roundno) as wins","roundno"),$tablenames=array($roundtable, $usertable),$where=array("id=winner","gameno=$gameno group by username"),$order=array("wins desc","roundno desc limit 15"),0);
	$topwinners =  MYSQLComplexSelect($link, $fieldnames=array("username","count(roundno) as wins","roundno"),$tablenames=array($roundtable, $usertable),$where=array("id=winner","gameno=$gameno group by username"),$order=array("wins desc","roundno desc limit 15"),0);
	$topplayers =  MYSQLComplexSelect($link, $fieldnames=array("username","count(roundno) as  plays"),$tablenames=array("guessguess","rwuser"),$where=array("id=userno","gameno=$gameno group by username"),$order=array("plays desc limit 15"),0);
//debug_array("winnerhist",$winnerhist);
//debug_array("topwinners",$topwinners);
//debug_array("topplayers",$topplayers);
// *****************************************************
// * Draw the page
// ****************************************************
print "<center><font size=+2><b> The Numbers Game $gameno</b> </font>\n";
print "<center><font size=+1><b> Public Rail Page</b> </font>\n";
	print "<center><font size=\"-1\"><a href=\"index.php?gameno=$gameno\">Guess Page</a></font><center>\n";
	print "<table border=\"0\" cellpadding=\"25\"><tr>\n";
//left column
	print "<td valign=\"top\">\n";
	print "<center><font size=\"+2\"><b> Hall of Fame </b></font><br> <font size=\"+1\"><b>Top 15 Winners</b></font></center>\n";
	print "<center><table border=1>\n";
	print "<tr><td><b><center>&nbsp;&nbsp;&nbsp;&nbsp;Winner&nbsp;&nbsp;&nbsp;&nbsp;</center></b></td><td><b><center>Wins &nbsp;&nbsp;&nbsp;&nbsp;</center></b></td></tr>\n";
	for ($i=0;$i<count($topwinners);$i++){
		$roundno=$topwinners[$i]['roundno'];
		$winner = $topwinners[$i]['username'];
		$wins = $topwinners[$i]['wins'];
		print "<tr ><td><center>$winner </center></td><td><center>$wins&nbsp;</center></td></tr>\n";
	}
	print "</table></center>\n";
	print "</td>\n";
// middle column
	print "<td valign=\"top\">\n";
	print "<center><font size=\"+2\"><b> Most Guesses</b></font><br> <font size=\"+1\"><b>Top 15 Players</b></font></center>\n";
	print "<center><table border=1>\n";
	print "<tr><td><b><center>&nbsp;&nbsp;&nbsp;&nbsp;Player&nbsp;&nbsp;&nbsp;&nbsp;</center></b></td><td><b><center>Plays &nbsp;&nbsp;&nbsp;&nbsp;</center></b></td></tr>\n";
	for ($i=0;$i<count($topplayers);$i++){
		$player = $topplayers[$i]['username'];
		$plays = $topplayers[$i]['plays'];
		print "<tr ><td><center>$player </center></td><td><center>$plays&nbsp;</center></td></tr>\n";
	}
	print "</table></center>\n";
	print "</td>\n";
	//print "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
//right column
	print "<td>\n";
	print "<center><font size=\"+2\"><b>Game $gameno Winner History </b></font><br><font size=\"+1\"><b>by Round (Last 15)</b></font></center>\n";
	print "<center><table border=1>\n";
	print "<tr><td><b><center>Round</b></td><td><b><center>Winning Guess</b></td><td><b><center>Winner</b></td></tr>\n";
	for ($i=0;$i<count($winnerhist);$i++){
		$roundno=$winnerhist[$i]['roundno'];
		$guess = $winnerhist[$i]['guess'];
		$username = $winnerhist[$i]['username'];
		print "<tr><td><center>$roundno</td><td><center>$guess</td><td><center>$username</td></tr>\n";
	}
	print "</table></center>\n";
// ---
	print "</td>\n";
	print "</tr></table>\n";

// display the footer
	Display_Generic_Footer($version,$lastmodified);
}
