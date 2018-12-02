<?php
/* TODO 

now the problem is that function create_flash_deck($userno,$listno) doesn't seem to do anyting excelpt. possibly, crash. THe problem seems to be in the mysql_queary command which probably jsut dies 
and then the system crashes silently leavnig me with a blank screen. But I don't know why.
*/
/*
        Copyright 2011 Dave Menconi
   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at
       http://www.apache.org/licenses/LICENSE-2.0
   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/

/* $Id$ */
/*****************************************
 * INDEX.PHP --  Flash Cards
 * This program generates flash cards to learn various lists
 *****************************************/
	include_once('config.php');
	include_once('../library/miscfunc.php');
	include_once('../library/debug.php');
	include_once('../library/loc_login.php');
	//include_once('../library/date.php');
	include_once "../library/mysql.php";
	//include_once('../library/list.php');
log_on();
//debug_on();
debug_string("--------------------------------- START Flash Card PROGAM _-------");
//debug_string("dbname",$dbname);
//debug_string("dbuser",$dbuser);
//debug_string("dbpass",$dbpass);
//debug_string("dbhost",$dbhost);
//debug_string("localdir",$localdir);

//$PARAMS = FilterSQL(array_merge($_POST,$_GET));
$PARAMS = array_merge($_POST,$_GET);
// Create link to DB
$link = make_mysql_connect($dbhost, $dbuser, $dbpass, $dbname);
//log this entry
LogStats($link,$PARAMS);

//log in user
$login = loc_GetAuthenticated($PARAMS,$link,$appword,$cookiename="login",$admin=false,$expiry=0,$title="Flash Cards", $color='#F0c0F0',"index.php",false);
if(!login){debug_string("login failed"); exit();}
$userno=loc_get_userno($link,$cookiename="login");
if(-1==$userno)JumpTo();
debug_string("USERNO",$userno);
//$userno=1;

// set the list
if (isset($PARAMS['list'])){$listno=$PARAMS['list'];}
else $listno=1;
$sql = "Select * from flash_lists where listid=$listno and (userno=$userno or userno=0)";
	$list=MYSQLGetData($link,$sql);
	debug_array("list",$list[0]);
	$imagedir = $list[0]['imagedir'];
	debug_string("imagedir",$imagedir);

unset($PARAMS['password']);
debug_array("params",$PARAMS);
//debug_string ("login succeeded");

	// set the mode
	if (isset($PARAMS['mode'])){$mode=$PARAMS['mode'];}
	else $mode = "display_lists";

	debug_string("mode **",$mode);

	switch($mode){
		case "display_page":
			display_page($listno);
			break;
		case "parseanswer":
			parse_answer($PARAMS,$listno);
			break;
		case "init_flashdeck":
			init_flashdeck($listno);
			break;
		case "parse_list":
			parse_list($PARAMS);
			break;
		case "special":
			break;
		case "display_cheat":
			debug_string("Display_cheat!!!");
			display_cheat($listno);
			break;
		case "display_lists":
			display_lists($userno);
			break;
		default:
			debug_string("mode=$mode");
			display_lists();
	}
	break_mysql_connect($link);
exit();
/*****************************************
 * init_flashdeck()
 * initializes the flash deck
 * INPUT: list number; user number
 * OUTPUT: just changes the db
 * RETURN: none
 *****************************************/
function init_flashdeck($listno,$userno=1){
	global $userno,$link;
	//print "init_flashdeck($listno,$userno)";
	debug_string("init_flashdeck($listno,$userno=1)");
	$sql = "update flash_deck set status='NotYet',correct=0,seen=0,ordval=rand() where listno=$listno and (userno=0 or userno=$userno )";
		$result = mysql_query($sql,$link) or die(mysql_error());

	display_page($listno);
}
/*****************************************
 * parse_list()
 * parse the list page results
 * INPUT: the parameters
 * OUTPUT: none (calls display_page)
 * RETURN:
 *****************************************/
function parse_list($PARAMS){
	global $userno;
	debug_string("parse_list(PARAMS)");
debug_array("PARAMS",$PARAMS);
	$userno = $PARAMS['userno'];

	if (!array_key_exists('radioset',$PARAMS)) $listno=1;
	else $listno = $PARAMS['radioset'];

	display_page($listno);

}
/*****************************************
 * display_lists()
 * displays the list of flash decks
 * INPUT: user number
 * OUTPUT: Draws the form page to select a list
 * RETURN: just exits
 *****************************************/
function display_lists($userno){
	global $link;
	debug_string("display_lists($userno)");

	print "<h1>Flash Cards</h1>\n";
	print "<h2>Select Your Deck</h2>\n";
	$sql = "Select * from flash_lists where (userno=0 or userno=$userno)";

	//print "display_lists select: $sql</br>\n";
	$lists = MYSQLGetData($link,$sql);

	print "<table border=1>";
	print "<th>List Name</th><th>Description</th><th>Show Deck</th>\n";
	foreach($lists as $list){
		$listid = $list['listid'];
		$name   = $list['name'];
		print "<tr>";
		print "	<td>Flash: <a href=\"index.php?mode=display_page&list=$listid\">$name</a></td>\n";
		print "	<td>".$list['description']."</td>\n";
		print "<td><a href=\"index.php?mode=display_cheat&list=$listid\">Show $name<a/></td>\n";
		print "</tr>";
	}
	print "</table>\n";
}
/*****************************************
 * display_cheat()
 *****************************************/
function display_cheat($listno){
	global $userno,$link;
	debug_string(" display_cheat($listno)");

	// get image page
	$sql = "Select * from flash_lists where listid=$listno and (userno=0 or userno=$userno)";
	$lists=MYSQLGetData($link,$sql);
	//debug_array("lists",$lists[0]);
	//print_r($lists[0]);
	$listname =$lists[0]['name'];
	$listdesc = $lists[0]['description'];
	$imagedir = $lists[0]['imagedir'];

	//debug_string("imagedir",$imagedir);
	$sql = "select * from flash_cards where  listno=$listno and (userno=0 or userno=$userno )";
	//debug_string("cheat select",$sql);
	$cards=MYSQLGetData($link,$sql);
	print "<Html><head></head><body>\n";
	print "<h1>$listname</h1>";
	print "<h3>$listdesc</h3>\n";
	print "<b><a href=\"index.php\">Return to Lists</a></b></br></br>\n";
	print "<table border=1>";
	print "<tr><th>Name</th><th>Image</th><th>Meaning</th>\n";
	foreach($cards as $card){
		$imageurl = $imagedir."/".$card['image'];
		$name = $card['name'];
		print "<tr><td><b>$name</b></td><td><img src=\"$imageurl\" alt=\"$imageurl\" height=\"80\"></td><td>".$card['value']."</td></td></tr>\n";
	}
	print "</table>\n";
	print "</body></html>\n";

	exit();
}
/*****************************************
 * display_page()
 * displays the
 * INPUT: None (well the database)
 * OUTPUT: Draws the page
 * RETURN: just exits
 *****************************************/
function display_page($listno){
	global $userno,$link;
	debug_string("display_page()");
	debug_string("1");

// before anything else: is there a flash deck for this set of flash cares and this user!? 
	$sql =  "select * from flash_deck where listno=$listno and userno=$userno limit 1";
	debug_string("check for flash deck sql",$sql);
	$cards=MYSQLGetData($link,$sql);
	if(count($cards)==0)create_flash_deck($userno,$listno);


// display page headers 
	print "<html>\n";
	print "<head>\n   <link rel=\"stylesheet\" type=\"text/css\" href=\"stylesheet.css\" />\n</head>\n";
	print "<body>\n";
/* some definitions
$Learning is the number of cards in our little deck, total (let's call this the "flash deck" to distinguish it from the "whole deck")
$Wrong is the number of cards in our flash deck that we haven't yet gotten right.  '
$Unseen is the number of cards we haven't gotten right that we haven't seen yet in the flash deck.
$NotYet is the number of cards that aren't in our the flash deck (which for Tarot would always be 78-$Learning).
*/

/* And the point
IF $Learning ==0 then we don't HAVE a flash deck and we need to add some cards to it. I *think* this only happens when we start out or start over.   '
If $Wrong == 0 then we've successfully learned all the cards in the flash deck and we need to add some cards to it
if $Unseen == 0 then we've seen all the cards in the deck one more time and we need to start through it again. '
If $NotYet ==0 then we've learned everything in the deck and WE'RE DONE! YAY   '
*/
	// get the flash deck 
	$sql = "select * from flash_deck where status='Learning' and listno=$listno and  userno=$userno order by ordval ";
	debug_string("Learning select",$sql);
	$cards=MYSQLGetData($link,$sql);
	$Learning = count($cards); // how many cards in the flash deck
	if ($Learning==0) AddCards($listno);// get a flash deck!

//$debugcards = array(); foreach($cards as $idx=>$card){$debugcards[$idx]=$card['falshcardsid'];} debug_array("Learning: $Learning",$debugcards);

	// get the number of cards you've gotten wrong; we want this so we can determine if we need to add more
	$sql = "select * from flash_deck where status='Learning' and correct=0 and listno=$listno and (userno=0 or userno=$userno )order by ordval  ";
	debug_string("Wrong select",$sql);
	$cards=MYSQLGetData($link,$sql);
	$Wrong = count($cards);
	if ($Wrong==0) AddCards($listno);

//$debugcards = array(); foreach($cards as $idx=>$card){$debugcards[$idx]=$card['falshcardsid'];} debug_array("Wrong: $Wrong",$debugcards);

	// get the number of unseen cards (this would be $Learning-$wrong EXCEPT that we might have added cards)
	$sql = "select * from flash_deck where status='Learning' and correct=0 and seen=0 and listno=$listno and (userno=0 or userno=$userno) order by ordval  ";
	$cards=MYSQLGetData($link,$sql);
	$Unseen = count($cards);
	if ($Unseen==0) {// we're out of cards so we reset the deck
		ResetDeck($listno);
		print "<h2>Deck Reset!</h2>\n";
	}
//$debugcards = array(); foreach($cards as $idx=>$card){$debugcards[$idx]=$card['falshcardsid'];} debug_array("Unseen: $Unseen",$debugcards);

// OK, now display that one card
	$sql = "select flash_deck.cardno,name,image,value from flash_deck,flash_cards where cardid=cardno and  status='Learning' and correct=0 and seen=0 and flash_deck.listno=$listno and flash_deck.userno=$userno  and flash_cards.listno=$listno and (flash_cards.userno=0 or flash_cards.userno=$userno) order by ordval limit 1";
	$sql = "select flash_deck.cardno,name,image,value from flash_deck,flash_cards where cardid=cardno and  status='Learning' and correct=0 and seen=0 and flash_deck.listno=$listno and (flash_deck.userno=flash_cards.userno or flash_cards.userno=0)  and flash_cards.listno=$listno and (flash_cards.userno=0 or flash_cards.userno=$userno) order by ordval limit 1";
	debug_string("Display Card select",$sql);

	$cards=MYSQLGetData($link,$sql);

	$cardcnt = count($cards);
	$card = $cards[0];
	$name = $card['name'];

	$value = $card['value'];
	$cardno = $card['cardno'];
	$imagename = $card['image'];
	$imageurl = $imagepath.$imagename;

	display_card($cardno,$listno);

	$sql = "select cardid ,value, rand() as rnd from flash_cards where cardid<>$cardno and listno=$listno and (userno=0 or userno=$userno)  order by rnd limit 4";
	debug_string("false cards",$sql);
	$cards=MYSQLGetData($link,$sql);
	$cards[]=array('cardid'=>$cardno,'value'=>$value);
	shuffle ($cards);

	print "<form method=\"post\" action=\"index.php?mode=parseanswer&list=$listno\"> \n";

	foreach($cards as $card){
		$newcardno=$card['cardid'];
		$value = $card['value'];
		print "<input type =\"radio\" name=\"radioset\", value=\"$newcardno\"/><b>$value</b></br>";
	}


	print "<input type=\"hidden\" name=\"cardno\" value=\"$cardno\"/>\n";
	print "<button type=\"submit\" name=\"submit\" value=\"submit\">Answer</button>\n";

	print "</form><hr>\n";

	// display other options
	print "<a href=\"index.php?mode=init_flashdeck&list=$listno\">Start Over</a> |\n";
	print "<a href=\"index.php?mode=display_lists\">Pick Different Flash Card Deck</a>\n";
	print "<hr>";

	display_flashdeck($listno);
	print "</body></html>\n";
}
/*****************************************
 * display_flashdeck()
 * shows a table with the flash deck
 * INPUT: $listno
 * OUTPUT: the screen
 * RETURN: NONE
 *****************************************/
function display_flashdeck($listno){
		debug_string("display_flashdeck()");
	global $userno,$link;
	// for debugging purposes, show the cards in the flash deck

	$sql = "select cardno from flash_deck where correct=1 and status='Learning'  and listno=$listno and userno=$userno ";
		debug_string("correctcards sel",$sql);
	$cards=MYSQLGetData($link,$sql);
	$correctcards = count($cards);

	$sql = "select cardno from flash_deck where seen=1 and status='Learning'  and listno=$listno and  userno=$userno ";
		debug_string("seencards sel",$sql);
	$cards=MYSQLGetData($link,$sql);
	$seencards = count($cards);

	$sql = "select flash_deck.cardno,name,correct,seen,value from flash_deck,flash_cards  where cardno=cardid and status='Learning' and flash_deck.listno=$listno and flash_deck.userno=$userno  and flash_cards.listno=$listno and (flash_cards.userno=$userno or flash_cards.userno=0) order by correct, seen , ordval";
		debug_string("display cards sel",$sql);
	$cards=MYSQLGetData($link,$sql);
	$totalcards = count($cards);
	print "<b>$totalcards</b> <i>total cards</i> including <b>$correctcards</b> <i>correct</i> out of <b>$seencards</b> <i>seen</i>. </b>\n";

	/*print "<table border=\"1\">\n";
	print "<th>Cardno</th><th>Name</th><th>Correct</th><th>Seen</th>\n";
	print "<tr><td><u><i>Totals</i></td><td><u><i>$totalcards</i></td><td><u><i>$correctcards</i></td><td><u><i>$seencards </i></td></tr>\n";
	foreach($cards as $idx=>$card){
		$cardno = $card['cardno'];
		$name = $card['name'];
		$correct = $card['correct'];
		$seen = $card['seen'];


		print "<tr><td>$cardno</td><td>$name</td><td>$correct</td><td>$seen</td></tr>\n";
	}
	print "</table>\n";
	*/
}
/*****************************************
 * display_card()
 * adds cards to the flash deck
 * INPUT: the database)
 * OUTPUT: fixes the deck so there is something in it
 * RETURN: true if it worked false if catastrophic failure
 *****************************************/
function display_card($cardno,$listno){
	global $userno,$userno,$link;
	debug_string("display_card($cardno)");

	$sql = "Select * from flash_lists where listid=$listno and (userno=0 or userno=$userno)";
	$lists=MYSQLGetData($link,$sql);
	//debug_array("lists",$lists[0]);
	$imagedir = $lists[0]['imagedir'];
	//debug_string("imagedir",$imagedir);

	$sql = "select cardno, name,image from flash_deck,flash_cards where cardno=cardid and cardno=$cardno and flash_deck.listno=$listno and flash_deck.userno=$userno and flash_cards.listno=$listno and (flash_cards.userno=0 or flash_cards.userno=$userno) ";
	debug_string("Display_Card (1card) select",$sql);
	$cards=MYSQLGetData($link,$sql);
	//debug_array("cards",$cards[0]);
	$name = $cards[0]['name'];
	$imageurl = $imagedir."/".$cards[0]['image'];
	//debug_string("imageurl",$imageurl);

	print "<img src=\"$imageurl\" alt=\"$imageurl\"></br>\n";
	print "<h2>$name</h2>\n";
	print "<hr>\n";
}
/*****************************************
 * AddCards()
 * adds cards to the flash deck
 * INPUT: the database)
 * OUTPUT: fixes the deck so there is something in it
 * RETURN: true if it worked false if catastrophic failure
 *****************************************/

function AddCards($listno){
	debug_string("AddCards()");
	global $userno,$link;

	print "<h2>Adding Cards!</h2>\n";

	$sql = "select * from flash_deck where status='NotYet' and listno=$listno and userno=$userno ";
	debug_string("NotYet select",$sql);
	$cards=MYSQLGetData($link,$sql);
//	debug_array("cards",$cards);
	$NotYet = count($cards);
	debug_string("NotYet",$NotYet );
	if ($NotYet==0) {
		$sql = "update flash_deck set status='NotYet',correct=0,seen=0,ordval=rand() where listno=$listno and userno=$userno ";
	debug_string("up date",$sql);
		$result = mysql_query($sql,$link) or die(mysql_error());
		display_success_page($listno);
		exit();
	}

	$sql = "update flash_deck set status='Learning' where status='NotYet' and listno=$listno and userno=$userno  order by ordval limit 5";
	debug_string("sql",$sql);
	$result = mysql_query($sql,$link) or die(mysql_error());
	$sql = "update flash_deck set correct=0,ordval=rand(), seen=0 where status='Learning'  and listno=$listno and userno=$userno ";
	debug_string("sql",$sql);
	$result = mysql_query($sql,$link) or die(mysql_error());
}

/*****************************************
 * ResetDeck()
 * adds cards to the flash deck
 * INPUT: the database)
 * OUTPUT: fixes the deck so there is something in it
 * RETURN: true if it worked false if catastrophic failure
 *****************************************/
function ResetDeck($listno){
	debug_string("ResetDeck()");
	global $userno,$link;

	// reset the seen value and the order of the flash deck (but leave out the ones we got correct)
	$sql = "update flash_deck set ordval=rand(), seen=0 where status='Learning'  and listno=$listno and userno=$userno ";
	debug_string("sql",$sql);
	$result = mysql_query($sql,$link) or die(mysql_error());

	// reset the ordval for the rest of the deck
	$sql = "update flash_deck set ordval=rand() where status='NotYet' and listno=$listno and userno=$userno  ";
	debug_string("sql",$sql);
	$result = mysql_query($sql,$link) or die(mysql_error());
}

/*****************************************
 * parse_answer()
 * parse the answer
 * INPUT: the parameters
 * OUTPUT: Result page, set the database
 * RETURN:
 *****************************************/
function parse_answer($PARAMS,$listno){
	debug_string("parse_answer(PARAMS,$listno)");
	global $userno,$link;
debug_array("parse_answer params",$PARAMS);
	$cardno = $PARAMS['cardno'];

	if (!array_key_exists('radioset',$PARAMS)) $correct=0;
	else {
		$answerno = $PARAMS['radioset'];
		$sql = "select *  from flash_cards  where cardid=$cardno and listno=$listno and userno=$userno ";
		debug_string("right answer",$sql);
		$cards=MYSQLGetData($link,$sql);
		debug_array("meanings",$cards[0]);
		$correctanswer = $cards[0]['value'];

		if ($answerno==$cardno)$correct=1;
		else $correct=0;
	}
	// up date the deck based on the answer we just saw
	$sql = "update flash_deck set seen=1,correct=$correct where cardno=$cardno and listno=$listno and userno=$userno ";
	debug_string("up date card",$sql);
	$result = mysql_query($sql,$link) or die(mysql_error());

	if($correct==1)display_forcorrect($cardno,$listno);
	else display_forwrong($cardno,$listno);
}
/*****************************************
 * display_forwrong()
 * displays the page for when you're wrong
 * INPUT: None
 * OUTPUT: Draws the page
 * RETURN: just exits
 *****************************************/
function display_forwrong($cardno,$listno){
	debug_string("display_forwrong($cardno)");
	global $userno,$link;
	//print "<head>\n   <link rel=\"stylesheet\" type=\"text/css\" href=\"stylesheet.css\" />\n</head>\n";
print "<body bgcolor=\"#ff6060\" >\n";
	// get the right answer
	$sql = "select * from flash_cards where cardid=$cardno and listno=$listno and (userno=0 or userno=$userno )";
	debug_string("forwrong select",$sql);
	$cards=MYSQLGetData($link,$sql);
	debug_array("wrong",$cards[0]);

	$rightanswer = $cards[0]['value'];
	print "<h2>Sorry, you got that wrong. </h2>The correct answer is: <b>$rightanswer.</b></br>\n";

	// display the card
	display_card($cardno,$listno);
	print "<a href=\"index.php?mode=display_page&list=$listno\">Next Card</a></br>\n";



}
/*****************************************
 * display_forcorrect()
 * displays the page when you're right
 * INPUT: None
 * OUTPUT: Draws the page
 * RETURN: just exits
 *****************************************/
function display_forcorrect($cardno,$listno){
	debug_string("display_forcorrect($cardno,$listno)");
	global $userno,$link;
	//print "<head>\n   <link rel=\"stylesheet\" type=\"text/css\" href=\"stylesheet.css\" />\n</head>\n";
print "<body bgcolor=\"#dfffdf\" >\n";

		// get the right answer
	$sql = "select * from flash_cards where cardid=$cardno and listno=$listno and (userno=0 or userno=$userno) ";
	debug_string("forright select",$sql);
	$cards=MYSQLGetData($link,$sql);
	debug_array("right",$cards[0]);
	$rightanswer = $cards[0]['value'];

	// now up date the deck
	$sql = "update flash_deck set correct=1 where cardno=$cardno and listno=$listno and (userno=$userno or userno=0) ";
	debug_string("for correct  ",$sql);
	$result = mysql_query($sql,$link) or die(mysql_error());



	// tell the user
	print "<h2>CORRECT!</h2> The answer is: <b>\"$rightanswer\".</b></br>\n";

// display the card
	display_card($cardno,$listno);

	print "</br><a href=\"index.php?mode=display_page&list=$listno\"><b>Next Card</b></a></br>\n";



}
/*****************************************
 * display_success_page()
 * displays the sucess page
 * INPUT: None
 * OUTPUT: Draws the page
 * RETURN: none
 *****************************************/
 function display_success_page($listno){
 	global $userno,$localdir;
	//print "<head>\n   <link rel=\"stylesheet\" type=\"text/css\" href=\"stylesheet.css\" />\n</head>\n";
 	print "<b><I><font size=+3>YAY, you've learned all the cards!</font></i></b></br>";
	print "<hr><a href=\"index.php?mode=init_flashdeck&list=$listno\"><b>Start Over</b></a> |\n";
	print "<a href=\"index.php?mode=display_lists\"><b>Pick Different Flash Card Deck</b></a>\n<hr>";

 }

/*****************************************
 * display_failure_page()
 * displays the failure page
 * INPUT: None
 * OUTPUT: Draws the page
 * RETURN: just exits
 *****************************************/
 function display_failure_page(){
	//print "<head>\n   <link rel=\"stylesheet\" type=\"text/css\" href=\"stylesheet.css\" />\n</head>\n";
 	print "<hr><b><I><font size=+3>Sorry, something bad just happened reading cards!</font></i></b>";
 	exit();
 }
/*****************************************
 * create_flash_deck()
 *****************************************/
 function create_flash_deck($userno,$listno){
 	global $link;
 	debug_string("create_flash_deck($userno,$listno) ");
	$sql = "insert ignore into flash_deck select last_insert_id() as id,cardid as cardno,\"NotYet\" as status, 0 as correct, rand() as ordval, 0 as seen, $userno as userno, listno, now() as  dailydecktt from flash_cards where listno=$listno";
	$sql =  "select last_insert_id() as id,cardid as cardno,\"NotYet\" as status, 0 as correct, rand() as ordval, 0 as seen, $userno as userno, listno, now() as  dailydecktt from flash_cards where listno=$listno";
	$carddeck = MYSQLGetData($link,$sql);
	//print_r($carddeck);
	foreach($carddeck as $card){
		//$result = mysql_query($sql,$link) or die(mysql_error());
		$sql = "insert into flash_deck (cardno,status,correct,ordval,seen,userno,listno) values ('".$card['cardno']."','".$card['status']."','".$card['correct']."','".$card['ordval']."','".$card['seen']."','".$card['userno']."','".$card['listno']."')";
		//print "</br>".$card['cardno'].": $sql</br>\n";
		$result = mysql_query($sql,$link) or die(mysql_error());
	}
 }
?>
