<?php
/* $Id */
/****************************************
* index.php -- WhyWeight
*****************************************/
include_once("config.php");
include_once("../library/miscfunc.php");
include_once("../library/debug.php");
include_once("../library/loc_login.php");
include_once("../library/mysql.php");
include_once("../library/htmlfuncs.php");
log_on();
//debug_on();
debug_string("----------------- START WHYWEIGHT --------------------------------------");
// This string is updated by the source control system and used to track changes.
$rcsversion = '$Id';
$rcsversion = str_replace("$","",$rcsversion);
$rcsversion = substr($rcsversion,strpos($rcsversion,",v")+2);
$localversion = $version . "<br>RCSversion: " . $rcsversion;

// set the params array up
$PARAMS = array_merge($_POST,$_GET);

// open a link to the database (used everywhere)
$link = make_mysql_connect($dbhost,$dbuser,$dbpass,$dbname);

//log this entry
//LogStats($link,$PARAMS);

//set monthnames
$monthnames = array('0'=>"December",'1'=>"January",'2'=>"February",'3'=>"March",'4'=>"April",'5'=>"May",'6'=>"June",'7'=>"July",'8'=>"August",'9'=>"September",'10'=>"October",'11'=>"November",'12'=>"December");

// Set the mode variable which controls what we do in app
if (!isset($PARAMS['mode'])){
    $mode = 'market';
} else{
    $mode=$PARAMS['mode'];
}

// Set the gameno value which controls which game we look at
if (!isset($PARAMS['gameno'])){
    $instanceno = 1;
} else {
    $instanceno = $PARAMS['gameno'];
}


// switch on mode to control application
switch($mode){
    case "register":
        loc_delete_cookie();
        $login = loc_GetAuthenticated($PARAMS,$link,$appword,"login",false,0,"Why Weight Game",'#F0F0c0',"index.php",true);
        if(loc_get_userno($link)==-1)JumpTo("index.php?mode=register");
//      if(!login) Display_Market();
        JumpTo();
        break;
    case "logout":
        //debug_string("logout");
        loc_delete_cookie();
        JumpTo();
        break;
    case "login":
        //debug_string("login");
        $login = loc_GetAuthenticated($PARAMS,$link,$appword,"login",false,0,"Why Weight Game",'#F0F0c0',"index.php",true);
        Display_Market();
        break;
    case "displayweight":
        debug_string("displayweight");
       // $login = loc_GetAuthenticated($PARAMS,$link,$appword,"login",false,0,"Why Weight Game",'#F0d0c0',"index.php",true);
       // if(loc_get_userno($link)==-1)JumpTo("index.php?gameno=$instanceno&mode=market");
       // if(!login) Display_Market();
        Display_EnterWeight($instanceno);
    	break;
    case "parseguess":
        //debug_string("parseguess");
        $login = loc_GetAuthenticated($PARAMS,$link,$appword,"login",false,0,"Why Weight Game",'#F0F0c0',"index.php",true);
        if(!login) Display_Market();
        Parse_GuessPage($PARAMS);
        Display_GuessPage($instanceno);
        break;
    case "market":
        //debug_string("market");
        Display_Market();
        break;
    case "help":
        //debug_string("market");
        Display_Help();
        break;
    case "stats":
        //debug_string("stats");
        Display_Stats($instanceno);
        break;
    case "test":
    	 debug_string ("test");
    	 break;
    default:
        //debug_string("default");
        Display_Market();
}
break_mysql_connect($link);
exit();
/*************************************/
/** FUNCTIONS ***********************/
/*************************************/
/*************************************
*   Display_Help()
*  Display this game's help page
*   Inputs None
*   return:
*     Nothing
*   Sideffects:
*     none
*************************************/
function Display_Help(){
    global $link,$version,$lastmodified;
    //debug_string("Display_Help()");
    // get the current round for this game
	$values = array();
    Display_Generic_Header("Why Weight Game Help","#e0FeFe");
    Display_Template("Help.temp","~",$values);

    // display the footer
    Display_Generic_Footer($version,$lastmodified);
}
/*************************************
*   Display_Market()
*  Display this game's marketing page
*   Inputs None
*   return:
*     Nothing
*   Sideffects:
*     none
*************************************/
function Display_Market(){
    global $link,$version,$lastmodified,$roundtable,$guesstable,$usertable,$gameno;
    //debug_string("Display_Market()");
    // get the current round for this game
    $gameinfo = MYSQLComplexSelect($link,$fieldnames=array("*"), $tablenames=array("tsgame"),$where=array("gameid=$gameno"),$order=array(),$debug=0);
    $gameinfo = $gameinfo[0];

    $instances = MYSQLComplexSelect($link,$fieldnames=array("*"), $tablenames=array("tsinstance"),$where=array("instgameno=$gameno","status='running'"),$order=array(),$debug=0);
    $values = Array("gamename"=>$gameinfo['gamename'],"designer"=>"Dave Menconi","rightcol"=>"");

    for ($i=0;$i<count($instances);$i++){
        $instgameno = $instances[$i]['instid'];
        $instname = $instances[$i]['instname'];
        $inststat = $instances[$i]['status'];
        $values['rightcol'] .= "<li><a href=\"http://www.turnstylegames.org/numbers/index.php?mode=guess&gameno=$instgameno\">$instname</a> (<a href=\"http://www.turnstylegames.org/numbers/index.php?mode=stats&gameno=$instgameno\">The Rail)</a> ";
    }

    Display_Generic_Header("Why Weight Game","#e0FeFe");
    Display_Template("market.temp","~",$values);

    // display the footer
    Display_Generic_Footer($version,$lastmodified);

}
//dummy function
function Display_GuessPage($instanceno){
}
/*************************************
*   Display_EnterWeight()
*   Inputs
*     $instanceno: the game number we''re doing
*   return:
*     Nothing
*   Sideffects:
*     Returns form values
*************************************/
function Display_EnterWeight($instanceno){
	global $link,$version,$lastmodified,$usertable,$monthnames;
    debug_string("Display_EnterWeight($instanceno)");

    $userno = loc_get_userno($link);
    $userno = 1;
    debug_string( "Userno=$userno");
    //$year = Date("Y");
    $year = 2011;
	$name = "Dave Menconi";
	$gameinstance = "Main";
	$roundno = 2011;
	$gamestatus = "Running";
	$gameno = 1;

	$thismonth =12;
	$prevweight = $startweight = $month = $ytdchange = $monthchange = $monthname = 0;

	$lastyear = $year-1;
	$sql = "select weight from weights where user_id=$userno and year='$lastyear' and month=12";
	$firstweight = MYSQLGetData($link,$sql);
	if (count($firstweight)!=0) $myweights[0]=$firstweight[0]['weight'];
	else $myweights = 0;

	$sql="select * from weights where user_id=$userno and year=$year";
//	print "$sql <br>";
   // $myweights = MYSQLComplexSelect ($link, array("*"),array("weights"),array("user_id=$userno","year=$year"),array(),0);
    $myweights = MYSQLGetData($link,$sql);
    foreach($myweights as $myweight){
    	$userweights[$myweight['month']] = $myweight['weight'];
    }
	$currentweight = $userweights[$thismonth];

echo <<<EOF
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;
		charset=iso-8859-1">
		<meta name="description" content="Guess a Number">
		<meta name="">
		<meta name="DISTRIBUTION" content="IU">
		<meta name="ROBOTS" content="noindex,nofollow">
		<meta name="revisit-after" content="30 days">
		<link rel="SHORTCUT ICON" href="http://http://menconi.com/images/scale.ico"/>
		<meta name="copyright" content="Copyright © 2011 Dave Menconi, All
		Rights Reserved">

		<meta name="author" content="Dave Menconi">

		<meta name="rating" content="PG-13">
		<Title>Enter Your Weight</title>
	</head>
	<body >
	<div id="mainpage">
		<h1>Why Weight</h1>

		<div class="links">
			| <a href="http://www.meissen.org/redwall/numbersgamerules.html">  Help</a> |
			<a href="index.php?mode=logout">Logout</a> |
			<a href="http://games.groups.yahoo.com/group/thenumbersgame/">Join the Forum</a>|
			<a href="index.php">Back To Main Page</a> |
		</div>

		<h2>Weight Entry for <span id="name"> $name</span></h2>
		<P id="status"><span id="game">Game: <span id="gamename"> $gameinstance </span></span>|<span id="round"> Round # <span id="roundno"> $roundno</span> </span>| <span id="gamestatus">Status: <span id="gamestatusname">$gamestatus</span></span></P>

		<p id="lastweight>Your Last Weight: $currentweight</p>
		<p id="instructions">Enter your weight and the month. You can only enter weights for the current year.<a href="whyweightrules.html">More info.</a></P>

		<div class="form">
			<form action="index.php" method="post">
			<input type=hidden name="mode" value="parseguess">
			<input type=hidden name="gameno" value="$gameno">
			<input type=hidden name="roundno" value="$roundno">
			<input type=hidden name="userno" value="$userno ">
			<br>
			<div id="month">
				<span id="monthtitle">Month:<span>
				<select name="month" >
				<option selected="selected" value="0" label = "Select Month">Select Month</option>
				<option value= "1" label = "January">January</option>
				<option value= "2" label = "February">February</option>
				<option value= "3" label = "March">March</option>
				<option value= "4" label = "April">April</option>
				<option value= "5" label = "May">May</option>
				<option value= "6" label = "June">June</option>
				<option value= "7" label = "July">July</option>
				<option value= "8" label = "August">August</option>
				<option value= "9" label = "September">September</option>
				<option value="10" label = "October">October</option>
				<option value="11" label = "November">November</option>
				<option value="12" label = "December">December</option>
				</select>
			</div>
			<div id="weight">
				<span id="weighttitle">Weight:</span>
				<input type=text name="weight" size=5>
			</div>
			<div id="input">
				<input type="submit" value="Enter Weight">
			</div>
			</form>
		</div>
	</div>
	<hr>
	<div id="stats">
		<h2 id="statstitle">Dave Menconi Weight History by Month (2011)</h2>
		<div id="statstable">
			<table border=1>
				<span id="tablehead"><tr><th>Month &nbsp;&nbsp;&nbsp;&nbsp;</th><th>Weight</th><th>% Change</th><th>% Change <br>(YTD)</th></tr></span>
EOF;
	$prevweight =	$startweight =	$userweights[1];
	if ($startweight == 0) $startweight = 1;
	foreach ($userweights as $month=>$weight){
		$ytdchange   = round(100*($startweight - $weight)/$startweight,2);
		$monthchange = round(100*($prevweight - $weight)/$prevweight,2);
		$prevweight  = $weight;
		$monthname = $monthnames[$month];

		print "<span class=\"tablebody\"><tr ><td>$monthname</td><td>$weight</td><td>$monthchange%</td><td>$ytdchange%</td></tr></span>";
	}
echo <<<EOF2

			</table>
		</div>
	</div>
	<div id="pageinfo">
		<p><a href="mailto:webmaster@menconi.com">Webmaster</a>
		<p>Version: $version <br>Last changed on $lastmodified.
		<p>This page (including all images) Copyright &copy; 2011  Dave Menconi.
	</div>

	</body>

</html>
EOF2;
}
/*************************************
*   Parse_GuessPage()
*   Inputs
*     $PARAMS: the parameters
*   return:
*     Nothing
*   Sideffects:
*     adds guess to database
*************************************/
function Parse_GuessPage($PARAMS){
    global $link,$guesstable,$roundtable;

    // get data from params
    $instanceno = $PARAMS['gameno'];
    $userno = $PARAMS['userno'];

    // get the current round for this game
    $rounds = MYSQLComplexSelect($link,$fieldnames=array("max(roundno) as roundno"), $tablenames=array($roundtable),$where=array("gameno=$instanceno"),$order=array(),$debug=0);
    $roundno = $rounds[0]['roundno'];
    //debug_string("roundno",$roundno);

    //debug_string("Parse_GuessPage()");
    //debug_array("params",$PARAMS);

    //extract a valid value from the user's guess
    $guess = extractguess($PARAMS['guess']);
    //debug_string("guess",$guess);

//debug_string("gameno",$instanceno);
//debug_string("roundno",$roundno);
//debug_string("userno",$userno);

//get the current guess
    $guesses = MYSQLComplexSelect($link, $fieldnames=array("*"),$tablenames=array($guesstable),$where=array("gameno=$instanceno","roundno=$roundno","userno=$userno"),$order=array(),$debug=0);
    if (count($guesses)<1)
    {
        $sql ="insert $guesstable (gameno,roundno,userno,guess) values ($instanceno,$roundno,$userno,$guess)";
        mysql_insert($link,$sql);
    } else {
        $sql="update $guesstable set guess=$guess where roundno=$roundno and gameno=$instanceno and userno=$userno";
        mysql_update($link,$sql);
    }
//debug_string("sql",$sql);
//$result = mysql_query($sql,$link);
//displaywinarray($instanceno,$roundno);

}
function extractguess($guess){
    //debug_string("<br>extractguess($guess)");
    // remove non-numeric characters
    $digits = "1234567890.";
    $result="";
    for ($i=0;$i<strlen($guess);$i++){
        $s = substr($guess,$i,1);
        if (strpos($digits,$s) === false) {
        } else {
            $result = $result.$s;
        }
    }//for
    // add an extra decimal place so algorythms work
    $result = $result.".";
    // now truncate down to 3.3
    $decimal = strpos($result,".");
    if($decimal>3){
        $l=strlen($result);
        $first = $decimal-3;
        $newlen = $l-$first;
        $result = substr($result,$first,$newlen);
    }
    $result = $result+0;// remove extra decimal points
    return $result;
}
/*************************************
*   Display_Stats()
*   Inputs
*     $instanceno: the game number we're doing
*   return:
*     Nothing
*   Sideffects:
*     Returns form values
*************************************/
function Display_Stats($instanceno){
    global $link,$version,$lastmodified,$roundtable,$guesstable,$usertable;
    //debug_string("Display_Stat()");

    //get instance information
    $instance = MYSQLComplexSelect ($link, array("*"),array("tsinstance"),array("instanceno=$instanceno","instgameno=1"),array(),0);
    $instancename = $instance[0]['instname'];
    $instancestatus = $instance[0]['status'];

    // get the current round for this game
    $rounds = MYSQLComplexSelect($link,$fieldnames=array("max(roundno) as roundno"), $tablenames=array($roundtable),$where=array("gameno=$instanceno"),$order=array(),$debug=0);
    $roundno = $rounds[0]['roundno'];
    //debug_string("roundno",$roundno);

// get the user number & name by looking in cookie
    $userno = loc_get_userno($link);

    //debug_string("userno",$userno);
//debug_string("Display_GuessPage2");
    $users = MYSQLComplexSelect($link, $fieldnames=array("name"),$tablenames=array($usertable),$where=array("id=$userno"),$order=array(),$debug=0);
    $name = $users[0]['name'];
//debug_string("name",$name);

//get the current guess
//debug_string("Display_GuessPage2");
    $guesses = MYSQLComplexSelect($link, $fieldnames=array("*"),$tablenames=array($guesstable),$where=array("gameno=$instanceno","roundno=$roundno","userno=$userno"),$order=array(),$debug=0);
    if (count($guesses)<1)
    {
        $guess = "None";
//debug_string ("no guess");
    } else {
        $guess = $guesses[0]['guess'];
//debug_string ("guess",$guess);
    }
//debug_string("guess",$guess);
    $values = Array("game"=>$instancename,"instno"=>$instanceno,"round"=>$roundno ,"currentguess"=>$guess,"userno"=>$userno,"name"=>$name);
//debug_array("values",$values);
    Display_Generic_Header("Statistics","#d0ffe0");

// display some history
//debug_array("userhist",$userhist);

    $winnerhist =  MYSQLComplexSelect($link, $fieldnames=array("$roundtable.roundno","username,winval","guess"),$tablenames=array($roundtable, $usertable,$guesstable),$where=array("winner=userno","$guesstable.gameno=$roundtable.gameno","$roundtable.roundno=$guesstable.roundno","id=winner","$roundtable.gameno=$instanceno"),$order=array("$roundtable.roundno desc limit 15"),0);
    $topwinners =  MYSQLComplexSelect($link, $fieldnames=array("username","count(roundno) as wins","roundno"),$tablenames=array($roundtable, $usertable),$where=array("id=winner","gameno=$instanceno group by username"),$order=array("wins desc","roundno desc limit 15"),0);
    $topwinners =  MYSQLComplexSelect($link, $fieldnames=array("username","count(roundno) as wins","roundno"),$tablenames=array($roundtable, $usertable),$where=array("id=winner","gameno=$instanceno group by username"),$order=array("wins desc","roundno desc limit 15"),0);
    $topplayers =  MYSQLComplexSelect($link, $fieldnames=array("username","count(roundno) as  plays"),$tablenames=array("guessguess","rwuser"),$where=array("id=userno","gameno=$instanceno group by username"),$order=array("plays desc limit 15"),0);
//debug_array("winnerhist",$winnerhist);
//debug_array("topwinners",$topwinners);
//debug_array("topplayers",$topplayers);
// *****************************************************
// * Draw the page
// ****************************************************
print "<center><font size=+2><b> The Numbers Game </b> </font>\n";
print "<center><font size=+1><b> The Rail</b> </font>\n";
print "<center><font size=+0><b>(AKA Statistics)</b> </font>\n";
    print "<center><font size=\"-1\"><a href=\"index.php?mode=guess&gameno=$instanceno\">Back To The Guess Page</a></font><center>\n";
    print "<center><hr width=\"20%\"><br><font size=+3><b><i>$instancename</i></b></font><center>\n";
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
    print "<td valign=\"top\">\n";
    print "<center><font size=\"+2\"><b>$instancename Winner History </b></font><br><font size=\"+1\"><b>by Round (Last 15)</b></font></center>\n";
    print "<center><table border=1>\n";
    print "<tr><td><b><center>Round</b></td><td><b><center>Winning Guess</b></td><td><b><center>Winner</b></td></tr>\n";
    for ($i=0;$i<count($winnerhist);$i++){
        $roundno=$winnerhist[$i]['roundno'];
        $guess = $winnerhist[$i]['guess'];
        $username = $winnerhist[$i]['username'];
        print "<tr><td><center>$roundno</td><td><center>$guess</td><td><center>$username</td></tr>\n";
    }
    print "</table></center>\n";
// far right column
// far right column
    if ($instanceno<3){
        print "<td>\n";
        print "<center><font size=\"+2\"><b>$instancename Average Guess Graph</b></font><br><font size=\"+1\"><b>by Round (Guesses > 3)</b></font></center>\n";
        print "<center><table border=1>\n";
        if ($instanceno==1){
            print "<center><img src=\"http://www.turnstylegames.org/numbers/midnight.jpg\"></center>\n";
        }else if ($instanceno==2){
            print "<center><img src=\"http://www.turnstylegames.org/numbers/noon.jpg\"></center>\n";
        }
        print "</table></center>\n";
    }

// ---
    print "</td>\n";
    print "</tr></table>\n";

// display the footer
    Display_Generic_Footer($version,$lastmodified);
}
function displaywinarray($instanceno,$roundno){
global $link,$guesstable,$usertable;
//debug_string("displaywinarray");
    $avg = MYSQLComplexSelect($link,$fieldnames=array("avg(guess) as avgguess"),$tablenames=array($guesstable),$where=array("gameno=$instanceno","roundno=$roundno"),$order=array(),$debug=0);

    $avgguess = $avg[0]['avgguess'];
    $guesscount = count($avg);

    //debug_string("avgguess",$avgguess);
    //debug_string("guesscount",$guesscount);

    $windata =  MYSQLComplexSelect($link,$fieldnames=array("username","name","id","userno","guessid","guess","abs(guess-$avgguess) as guessdiff","$guesstable.ts as gts"),$tablenames=array($guesstable,$usertable),$where=array("gameno=$instanceno","roundno=$roundno","id=userno"),$order=array("guessdiff","$guesstable.ts"),$debug=0);
    print "<table border=1>";
    print "<tr><td><b> Username</b></td><td><b> Name</b></td><td><b> userno</b></td><td><b> id</b></td><td><b> guessid</b></td><td><b> guess</b></td><td><b> guessdiff</b></td><td><b> gts</b></td>\n";
    for($i=0;$i<count($windata);$i++){
        print "<tr> <td>".$windata[$i]['username']."</td><td>".$windata[$i]['name']."</td><td>".$windata[$i]['userno']."</td><td>".$windata[$i]['id']."</td><td>".$windata[$i]['guessid']."</td><td>".$windata[$i]['guess']."</td><td>".$windata[$i]['guessdiff']."</td><td>".$windata[$i]['gts']."</td></tr>\n";
    }
    print "</table>\n";
}
/*
 * $Log
 */
?>
