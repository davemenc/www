<?php
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
 * INDEX.PHP -- Tarot Flash Cards
 * This program generates flash cards to learn the meanings of tarot cards
 *****************************************/
	include_once('config.php');
	include_once('../library/miscfunc.php');
	include_once('../library/debug.php');
	include_once('../library/loc_login.php');
	include_once('../library/date.php');
	include_once "../library/mysql.php";
	include_once('../library/list.php');
//log_on();

debug_off();
debug_string("--------------------------------- START Flash Card PROGAM _-------");
//$PARAMS = FilterSQL(array_merge($_POST,$_GET));
$PARAMS = array_merge($_POST,$_GET);
// Create link to DB
$link = make_mysql_connect($dbhost, $dbuser, $dbpass, $dbname);
//log in user
/*
$login = loc_GetAuthenticated($PARAMS,$link,$appword,$cookiename="login",$admin=false,$expiry=0,$title="Todo List", $color='#F0F0F0',"index.php",false);
if(!login){debug_string("login failed"); exit();}
$userno=loc_get_userno($link,$cookiename="login");
if(-1==$userno)JumpTo();
*/

unset($PARAMS['password']);
debug_array("params",$PARAMS);
//debug_string ("login succeeded");

	// set the mode
	if (isset($PARAMS['mode'])){$mode=$PARAMS['mode'];}
	else $mode = "display_page";

	debug_string("mode",$mode);

	switch($mode){
		case "display_page":
			display_page();
			break;
		case "parseanswer":
			parse_answer($PARAMS);
			break;
		case "special":
			break;
		default:
			debug_string("mode=$mode");
			display_page();
	}
break_mysql_connect($link);
exit();

/*****************************************
 * display_page()
 * displays the
 * INPUT: None (well the database)
 * OUTPUT: Draws the page
 * RETURN: just exits
 *****************************************/
function display_page(){
	global $link,$imagepath;
	debug_string("display_page()");


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

	$sql = "select * from  tarot_flashcards where status='Learning'";
	debug_string("Learning select",$sql);
	$cards=MYSQLGetData($link,$sql);
	//debug_array("cards",$cards);
	$Learning = count($cards);
	//debug_string("Learning",$Learning);
	if ($Learning==0){
		AddCards();
		print "<h2>Adding Cards!<h2>\n";
	}

	$sql = "select * from  tarot_flashcards where status='Learning' and correct=0";
	debug_string("Wrong select",$sql);
	$cards=MYSQLGetData($link,$sql);
	//debug_array("cards",$cards);
	$Wrong = count($cards);
	//debug_string("Wrong",$Wrong);
	if ($Wrong==0){
		AddCards();
		print "<h2>Adding Cards!<h2>\n";
	}

	$sql = "select * from  tarot_flashcards where status='Learning' and correct=0 and seen=0";
	debug_string("Unseen select",$sql);
	$cards=MYSQLGetData($link,$sql);
	//debug_array("cards",$cards);
	$Unseen = count($cards);
	//debug_string("Unseen",$Unseen);
	if ($Unseen==0) {
		ResetDeck();
		print "<h2>Deck Reset!</h2>\n";
	}

// OK, now display that one card
	$sql = "select  tarot_flashcards.cardno,name,image,value from  tarot_flashcards,tarot_cardlist where cardid=cardno and  status='Learning' and correct=0 and seen=0 order by ordval limit 1";

	debug_string("Display Card select",$sql);
	$cards=MYSQLGetData($link,$sql);
	//debug_array("cards",$cards);
	$cardcnt = count($cards);
	//debug_string("Should be 1",$cardcnt);
	$card = $cards[0];
	$name = $card['name'];
	$value = $card['value'];
	$cardno = $card['cardno'];
	$imagename = $card['image'];
	$imageurl = $imagepath.$imagename;
	display_card($cardno);
	$sql = "select cardid ,value, rand() as rnd from tarot_cardlist where cardid<>$cardno order by rnd limit 4";
	debug_string("false cards",$sql);
	$cards=MYSQLGetData($link,$sql);
$cards[]=array('cardid'=>$cardno,'value'=>$value);
	debug_array("cards2",$cards);
	shuffle ($cards);
	debug_array("cards3",$cards);

	print "<form method=\"post\" action=\"index.php?mode=parseanswer\"> \n";

	foreach($cards as $card){
		$newcardno=$card['cardid'];
		$value = $card['value'];
		print "<input type =\"radio\" name=\"radioset\", value=\"$newcardno\"/><b>$value</b></br>";
	}


	//print "<input type=\"text\" name=\"answer\" size=50 maxlength=50/>\n";
	print "<input type=\"hidden\" name=\"cardno\" value=\"$cardno\"/>\n";
	print "<button type=\"submit\" name=\"submit\" value=\"submit\">Answer</button>\n";

	print "</form><hr>\n";

display_flashdeck();
}
/*****************************************
 * display_flashdeck()
 * shows a table with the flash deck
 * INPUT: the database
 * OUTPUT: the screen
 * RETURN: NONE
 *****************************************/
function display_flashdeck(){
	global $link;
	// for debugging purposes, show the cards in the flash deck

	$sql = "select cardno from  tarot_flashcards where correct=1 and status='Learning' ";
		debug_string("display list",$sql);
	$cards=MYSQLGetData($link,$sql);
	$correctcards = count($cards);

	$sql = "select cardno from  tarot_flashcards where seen=1 and status='Learning' ";
		debug_string("display list",$sql);
	$cards=MYSQLGetData($link,$sql);
	$seencards = count($cards);

	$sql = "select  tarot_flashcards.cardno,name,correct,seen,value from  tarot_flashcards,tarot_cardlist  where cardno=cardid and status='Learning'  order by correct, seen , ordval";
		debug_string("display list",$sql);
	$cards=MYSQLGetData($link,$sql);
	$totalcards = count($cards);

	print "<table border=\"1\">\n";
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
}
/*****************************************
 * display_card()
 * adds cards to the flash deck
 * INPUT: the database)
 * OUTPUT: fixes the deck so there is something in it
 * RETURN: true if it worked false if catastrophic failure
 *****************************************/
function display_card($cardno){
	debug_string("display_card($cardno)");
	global $link,$imagepath;
	$sql = "select cardno, name,image from  tarot_flashcards,tarot_cardlist where cardno=cardid and cardno=$cardno";
	debug_string("Display Card select",$sql);
	$cards=MYSQLGetData($link,$sql);
	debug_array("cards",$cards);
	$card = $cards[0];
	$name = $cards[0]['name'];
	$imageurl = $imagepath.$card['image'];

	print "<hr></br><h2>$name</h2></br>\n";
	print "<img src=\"$imageurl\"/ alt=\"$imageurl\"></br>\n";
	print "<hr>\n";
}
/*****************************************
 * AddCards()
 * adds cards to the flash deck
 * INPUT: the database)
 * OUTPUT: fixes the deck so there is something in it
 * RETURN: true if it worked false if catastrophic failure
 *****************************************/

function AddCards(){
	debug_string("AddCards()");
	global $link;

	$sql = "select * from  tarot_flashcards where status='NotYet'";
	debug_string("NotYet select",$sql);
	$cards=MYSQLGetData($link,$sql);
//	//debug_array("cards",$cards);
	$NotYet = count($cards);
	//debug_string("NotYet",$NotYet);
	if ($NotYet==0) {
		$sql = "update  tarot_flashcards set status='NotYet',correct=0,seen=0,ordval=rand()";
	debug_string("update",$sql);
		$result = mysql_query($sql,$link) or die(mysql_error());
		display_success_page();
		exit();
	}




	$sql = "update  tarot_flashcards set status='Learning' where status='NotYet' order by ordval limit 5";
	debug_string("sql",$sql);
	$result = mysql_query($sql,$link) or die(mysql_error());
	$sql = "update  tarot_flashcards set correct=0,ordval=rand(), seen=0 where status='Learning' ";
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
function ResetDeck(){
	debug_string("ResetDeck()");
	global $link;

	// reset the seen value and the order of the flash deck (but leave out the ones we got correct)
	$sql = "update  tarot_flashcards set ordval=rand(), seen=0 where status='Learning' ";
	debug_string("sql",$sql);
	$result = mysql_query($sql,$link) or die(mysql_error());

	// reset the ordval for the rest of the deck
	$sql = "update  tarot_flashcards set ordval=rand() where status='NotYet' ";
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
function parse_answer($PARAMS){
	debug_string("parse_answer(PARAMS)");
	global $link;

	$answerno = $PARAMS['radioset'];
	$cardno = $PARAMS['cardno'];
	$sql = "select *  from tarot_cardlist  where cardid=$cardno";
	debug_string("right answer",$sql);
	$cards=MYSQLGetData($link,$sql);
	debug_array("meanings",$cards);
	$correctanswer = $cards[0]['value'];

	if ($answerno==$cardno)$correct=1;
	else $correct=0;
	// update the deck based on the answer we just saw
		$sql = "update  tarot_flashcards set seen=1,correct=$correct where cardno=$cardno";
		debug_string("update card",$sql);
		$result = mysql_query($sql,$link) or die(mysql_error());

	if($correct==1)display_forcorrect($cardno);
	else display_forwrong($cardno);
}
/*****************************************
 * display_forwrong()
 * displays the page for when you're wrong
 * INPUT: None
 * OUTPUT: Draws the page
 * RETURN: just exits
 *****************************************/
function display_forwrong($cardno){
	debug_string("display_forwrong($cardno)");
	global $link;
print "<body bgcolor=\"#ff6060\" >\n";
	// get the right answer
	$sql = "select * from tarot_cardlist where cardid=$cardno";
	debug_string("forwrong select",$sql);
	$cards=MYSQLGetData($link,$sql);
	debug_array("wrong",$cards);

	$rightanswer = $cards[0]['value'];
	print "<h2>Sorry, you got that wrong. </h2>The correct answer is: <b>$rightanswer.</b></br>\n";

	// display the card
	display_card($cardno);
	print "<a href=\"index.php\">Next Card</a></br>\n";



}
/*****************************************
 * display_forcorrect()
 * displays the page when you're right
 * INPUT: None
 * OUTPUT: Draws the page
 * RETURN: just exits
 *****************************************/
function display_forcorrect($cardno){
	debug_string("display_forcorrect($cardno)");
	global $link;
print "<body bgcolor=\"#dfffdf\" >\n";

		// get the right answer
	$sql = "select * from tarot_cardlist where cardid=$cardno";
	debug_string("forright select",$sql);
	$cards=MYSQLGetData($link,$sql);
	debug_array("right",$cards);
	$rightanswer = $cards[0]['value'];

	// now update the deck
	$sql = "update  tarot_flashcards set correct=1 where cardno=$cardno";
	debug_string("update",$sql);
	$result = mysql_query($sql,$link) or die(mysql_error());



	// tell the user
	print "<h2>CORRECT!</h2> The answer is: <b>\"$rightanswer\".</b></br>\n";

// display the card
	display_card($cardno);

	print "</br><a href=\"index.php\"><b>Next Card</b></a></br>\n";



}
/*****************************************
 * display_success_page()
 * displays the sucess page
 * INPUT: None
 * OUTPUT: Draws the page
 * RETURN: none
 *****************************************/
 function display_success_page(){
 	global $localdir;
 	print "<hr><b><I><font size=+3>YAY, you've learned all the cards!</font></i></b></br>";
 	print "Do you want to try again? <a href=\"$localdir/tarot/index.php\">TRY AGAIN</a>";
 }

/*****************************************
 * display_failure_page()
 * displays the failure page
 * INPUT: None
 * OUTPUT: Draws the page
 * RETURN: just exits
 *****************************************/
 function display_failure_page(){
 	print "<hr><b><I><font size=+3>Sorry, something bad just happened reading cards!</font></i></b>";
 	exit();
 }
?>
