<?PHP
include_once('config.php');
include_once "../library/debug.php";
include_once "../library/loc_login.php";
include_once "../library/mysql.php";
include_once "../library/date.php";
log_on();
debug_off();
debug_string("--------------------------------- START WEDDING PROGRAM -------");

//set params variable
$PARAMS = array_merge($_POST,$_GET);
debug_params($PARAMS);

// set mode variable
if (isset($PARAMS['mode'])){$mode=$PARAMS['mode'];}
else $mode = "main";
debug_string("set mode",$mode);

// set userpassphrase variable
$userpassphrase="";
if (isset($PARAMS['passphrase'])) $userpassphrase =  strtoupper($PARAMS['passphrase']);
debug_string("set passphrase",$userpassphrase);

// check for passphrase
if($mode =='passphrase' && $userpassphrase == $passphrase) {
	debug_string("passphrase",$userpassphrase);
    loc_create_auth_cookie($username,0,$magic_word,$cookiename);
    debug_string("create cookie",$username);
    jumpto();
}
// check for existing cookie
debug_string("check for existing cookie");
$c_data = loc_get_cookie_data($cookiename);
debug_array("cookie",$c_data);
if (!loc_check_auth_cookie($magic_word,0,$cookiename)){
	debug_string("display index.html.txt");
	Display_Template("index.html.txt",$delim="%",$values="");
	//print "<hr>$passphrase</br>";
	exit();
}else {
	debug_string("Cookie auth succeeded");
	loc_create_auth_cookie($username,0,$magic_word,$cookiename);
}
	$c_data = loc_get_cookie_data($cookiename);
	debug_array("cookie #2",$c_data);

switch($mode){
	case "construction":
		Display_Template("construction.txt",$delim="%",$values="");
		break;
	case "main":
		Display_Template("main.html.txt",$delim="%",$values="");
		break;
	case "registry":
		Display_Template("registry.html.txt",$delim="%",$values="");
		break;
	case "schedule":
		Display_Template("schedule.html.txt",$delim="%",$values="");
		break;
	case "maps":
		Display_Template("maps.html.txt",$delim="%",$values="");
		break;
	case "lodging":
		Display_Template("lodging.html.txt",$delim="%",$values="");
		break;
	case "rsvp":
		Display_Template("rsvp.html.txt",$delim="%",$values="");
		break;
	case "guestbook":
		showguestbook();
		//Display_Template("guestbook.html.txt",$delim="%",$values="");
		break;
	case "test":
		print test;
		Display_Template("main.html.txt",$delim="%",$values="");
		break;
	case "dorsvp":
		mailform($PARAMS);
		break;
	case "doguestbook":
		doguestbook($PARAMS);
		break;
	default:
		Display_Template("main.html.txt",$delim="%",$values="");
		exit();
		break;
}
exit();
/*************************************
 *********** FUNCTIONS ***************
 *************************************/
function showguestbook(){
	global $dbhost,$dbuser,$dbpass,$dbname;

//	print "showguestbook()";


	$link = make_mysql_connect($dbhost,$dbuser,$dbpass,$dbname);
	$sqlcommand ='select * from wed_comments order by commentTS';
	$commentlist = MYSQLGetData($link,$sqlcommand);
	break_mysql_connect($link);
	Display_Template("guestbook.html.txt",$delim="%",$values="");
	//print_r($commentlist);

	// Do the comments
	foreach($commentlist as $comment){
		$tdate = $comment['commentTS'];
		$date = date("l, n-j-Y",datearray_to_tstamp(parse_tsdate($tdate)));

		$name =  $comment['name'];
		$acomment =  $comment['comment'];
		print "<p class=\"date\">$date</p>\n";
		print "<p><span class=\"by\">$name</span> <span class=\"body\">$acomment</span></p><hr />\n\n";
	}

	print "</div><!--upper_background-->\n </div><!--middle_background-->\n";

}

function doguestbook($params){
	global $dbhost,$dbuser,$dbpass,$dbname;
//	print_r($params);
	$name=$params['realname'];
	$comment = $params['comment'];
	$link = make_mysql_connect($dbhost,$dbuser,$dbpass,$dbname);
	 $sqlinsert ="insert into wed_comments (name,comment) values ('";
	 $sqlinsert .=$name."','".$comment."')";
//	 print "Command=$sqlinsert<br>";
	 do_mysql($link,$sqlinsert);

	break_mysql_connect($link);

	showguestbook();
}
function is_forbidden($str,$check_all_patterns = true)
{
	$patterns[0] = '/content-type:/';
	$patterns[1] = '/mime-version/';
	$patterns[2] = '/multipart/';
	$patterns[3] = '/Content-Transfer-Encoding/';
	$patterns[4] = '/to:/';
	$patterns[5] = '/cc:/';
	$patterns[6] = '/bcc:/';
	$forbidden = 0;
	for ($i=0; $i<count($patterns); $i++)
	{
		$forbidden = preg_match($patterns[$i], strtolower($str));
		if ($forbidden) break;
	}
	//check for line breaks if checking all patterns
	if ($check_all_patterns AND !$forbidden) $forbidden = preg_match("/(%0a|%0d|\\n+|\\r+)/i", $str);
	if ($forbidden)
	{
		echo "<font color=red><center><h3>STOP! Message not sent.</font></h3><br><b> The text you entered is forbidden, it includes one or more of the following: <br><textarea rows=9 cols=25>";
		foreach ($patterns as $key => $value) echo trim($value,"/")."\n";
		echo "\\n\n\\r</textarea><br>Click back on your browser, remove the above characters and try again</b><br><br><br><br>Thankfully protected by phpFormMailer freely available from: <a href=\"http://thedemosite.co.uk/phpformmailer/\">http://thedemosite.co.uk/phpformmailer/</a>";
		exit();
	}
}
function mailform($param){
	debug_string( "mailform(param)<br>\n");
	debug_params($param);
	$replyemail="dave@menconi.com"; //change to your email address
	$valid_ref1="http://menconi.com/wedding/index.php?mode=rsvp"; //chamge to your domain name
	$valid_ref2="http://www.menconi.com/wedding/index.php?mode=rsvp"; //chamge to your domain name

	//
	// email variable not set - load $valid_ref1 page
	if (!isset($param['email']))
	{
		 echo "<script language=\"JavaScript\"><!--\n ";
		 echo "top.location.href = \"$valid_ref1\"; \n// --></script>";
		 exit;
	}
	$ref_page=$_SERVER["HTTP_REFERER"];
//	print "ref_page: $ref_page<br>\n";
	$valid_referrer=0;
	if($ref_page==$valid_ref1) $valid_referrer=1;
	elseif($ref_page==$valid_ref2) $valid_referrer=1;
//	print "reg page: $ref_page";
	if((!$valid_referrer) OR ($param["block_spam_bots"]!=12))//you can change this but remember to change it in the contact form too
	{
		echo '<h2>ERROR - not sent.';
		if (true) echo '<hr>"$valid_ref1" and "$valid_ref2" are incorrect within the file:<br>  contact_process.php <br><br>On your system these should be set to: <blockquote>  $valid_ref1="'.str_replace("www.","",$ref_page).'"; <br>  $valid_ref2="'.$ref_page.'";  </blockquote></h2>Copy and paste the two lines above  into the file: contact_process.php <br> (replacing the existing variables and settings)';
		exit;
	}

	//print_r($param);
	$message="";
	foreach ($param as $key => $value) //check all input
	{
		if ($key == "themessage") is_forbidden($value, false); //check input except for line breaks
		else is_forbidden($value);//check all
		$message .= $key. ": ".$value."\n";

	}
	debug_string("<hr><pre>".$message."</pre><hr>");
	$name = $param["name"];
	$email = $param["email"];
	$thesubject = "Wedding RSVP";
	$themessage = $param["themessage"];
	debug_string("name",$name);
	debug_string("email",$email);
	debug_string("thesubject",$thesubject);
	debug_string("themessage",$themessage);

	$success_sent_msg='<p align="center"><strong>&nbsp;</strong></p><p align="center"><strong>Your RSVP has been successfully sent to us.<br></p> <p align="center">Thank you for responding!.</p><p align="center"><a href="http://www.menconi.com/wedding/">Return to Main Page</a></p>';
	$themessage = "name: $name \nQuery: $themessage";
	$result = mail("$replyemail","$thesubject","$message","From: $email\nReply-To: $email");
	if ($result) echo $success_sent_msg;
	else "There was a problem.";
}
?>
