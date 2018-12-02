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
 * INDEX.PHP --  Puzzle program
 * This program does a web site that challenges you to answer questions
 *****************************************/
	include_once('config.php');
	include_once('../library/miscfunc.php');
	include_once('../library/debug.php');
	include_once('../library/date.php');
	include_once "../library/mysql.php";
	include_once('../library/list.php');
//log_on();
//debug_on();
debug_string("--------------------------------- START Flash Card PROGAM _-------");
debug_string("dbname",$dbname);
debug_string("dbuser",$dbuser);
debug_string("dbpass",$dbpass);
debug_string("dbhost",$dbhost);
debug_string("localdir",$localdir);
debug_string("imagepath",$imagepath);

$PARAMS = array_merge($_POST,$_GET);
// Create link to DB
$link = make_mysql_connect($dbhost, $dbuser, $dbpass, $dbname);
//log in user

$sql = "Select * from puzzles where active=1";
	$puzzles=MYSQLGetData($link,$sql);
	debug_array("puzzles",$puzzles[0]);


$userno = 1;
debug_array("params",$PARAMS);
// set the list

	// set the mode
	if (isset($PARAMS['mode'])){$mode=$PARAMS['mode'];}
	else $mode = "display_puzzles";

	debug_string("mode",$mode);

	switch($mode){
		case "display_puzzles":
			display_puzzles();
			break;
		case "parseanswer":

// NOTE: $puzzleno is not defined properly!
			if (isset($PARAMS['puzzleno'])){$puzzleno=$PARAMS['puzzleno'];}
			else $puzzleno = 1;
			parse_answer($PARAMS,$puzzleno);
			break;
		case "init_puzzles":
			init_puzzles();
			break;
		case "display_puzzle":
			if (isset($PARAMS['puzzleno'])){$puzzleno=$PARAMS['puzzleno'];}
			else $puzzleno = 1;
			display_puzzle($puzzleno);
			break;
		case "parse_puzzles":
			parse_puzzles($PARAMS);
			break;
		case "special":
			break;
		default:
			debug_string("mode=$mode");
			display_puzzles($puzzleno);
	}
break_mysql_connect($link);
exit();
/*****************************************
 * init_puzzles()
 * initializes the flash deck
 * INPUT: list number; user number
 * OUTPUT: just changes the db
 * RETURN: none
 *****************************************/
function init_puzzles(){
	global $link;
	debug_string("init_puzzles()");

	$sql = "update puzzles set correct=0,tries=0 where active=1 ";
	debug_string("up date puzzles",$sql);
	$result = mysql_query($sql,$link) or die(mysql_error());

	display_puzzles();
}
/*****************************************
 * parse_puzzles()
 * parse the list page results
 * INPUT: the parameters
 * OUTPUT: none (calls display_puzzles)
 * RETURN:
 *****************************************/
function parse_puzzles($PARAMS){
	debug_string("parse_puzzles(PARAMS)");
debug_array("PARAMS",$PARAMS);

	if (!array_key_exists('radioset',$PARAMS)) $puzzleno=1;
	else $puzzleno = $PARAMS['radioset'];

	display_puzzle($puzzleno);

}
/*****************************************
 * display_puzzles()
 * displays the list of flash decks
 * INPUT: user number
 * OUTPUT: Draws the form page to select a list
 * RETURN: just exits
 *****************************************/
function display_puzzles(){
	global $link;
	debug_string("display_puzzles()");

	$sql = "select * from puzzles where active=1 and correct=1";
	$puzzles=MYSQLGetData($link,$sql);
	$correctpuzzles = count($puzzles);
	$puzzlesleft = 5-$correctpuzzles;

	if ($puzzlesleft<1) display_success_page();

	$sql = "Select * from puzzles where active=1 ";
	$puzzles=MYSQLGetData($link,$sql);
	$totalpuzzles = count($puzzles);


	$sql = "Select * from puzzles where active=1 and correct=0 ";
	$puzzles=MYSQLGetData($link,$sql);

	print "<h1>Christmas Treasure Hunt</h1>\n<P><b><font size=+1> You must answer 5 of the brain teasers and you will be told where to find the treasure. Otherwise good luck finding it!</p>\n";
	print "<p><b>So far you have solved <i>$correctpuzzles </i>out of <i>$totalpuzzles</i> with only $puzzlesleft left to go!</b></font>\n";
	print "<h2>Please select a puzzle to solve by clicking on the title.</h2>\n";
	print "<table border=1>\n";
	print "<tr><th>&nbsp;Title&nbsp;</th><th>&nbsp;Tries&nbsp;</th></tr>\n";
	foreach($puzzles as $puzzle){
		$puzzleid=$puzzle['puzzleid'];
		$title = $puzzle['title'];
		print "<tr>";
		print "<td><a href=\"index.php?mode=display_puzzle&puzzleno=$puzzleid\"><b>$title	</b></a>&nbsp;</td>";
		//print "<td><center>".$puzzleid."</td>";
		//print "<td><center>".$puzzle['active']."</td>";
		//print "<td><center>".$puzzle['correct']."</td>";
		print "<td><center>".$puzzle['tries']."</td>";
		print "</tr>\n";
	}
	print "</table>\n";
	print "<hr>\n";

	$sql = "Select * from puzzles where active=1 and correct=1 ";
	$puzzles=MYSQLGetData($link,$sql);
	if (count($puzzles)>0){
		print "<h2>You Got These Right!</h2>\n";
		print "<table border=1>\n";
		print "<tr><th>&nbsp;Title&nbsp;</th><th>&nbsp;Tries&nbsp;</th></tr>\n";
		foreach($puzzles as $puzzle){
			$puzzleid=$puzzle['puzzleid'];
			$title = $puzzle['title'];
			print "<tr>";
			print "<td><b>$title	</b>&nbsp;</td>";
			//print "<td><center>".$puzzleid."</td>";
			//print "<td><center>".$puzzle['active']."</td>";
			//print "<td><center>".$puzzle['correct']."</td>";
			print "<td><center>".$puzzle['tries']."</td>";
			print "</tr>\n";
		}
		print "</table>\n";
	}
	if ($correctpuzzles>0) {
		print "</br><h3>And your interim score...</h3>\n";
		display_score($link);
	}
}

/*****************************************
 * display_puzzles()
 * displays the
 * INPUT: None (well the database)
 * OUTPUT: Draws the page
 * RETURN: just exits
 *****************************************/
function display_puzzle($puzzleno){
	global $link;
	debug_string("display_puzzle($puzzleno)");
	$sql = "Select * from puzzles where puzzleid=$puzzleno";
	$puzzles=MYSQLGetData($link,$sql);
	debug_array("puzzles",$puzzles);
	$puzzle = $puzzles[0];
	$answers[] = array("answer"=>$puzzle['answer'],"result"=>"1");
	$answers[] = array("answer"=>$puzzle['fake1'],"result"=>"0");
	$answers[] = array("answer"=>$puzzle['fake2'],"result"=>"0");
	$answers[] = array("answer"=>$puzzle['fake3'],"result"=>"0");
	$answers[] = array("answer"=>$puzzle['fake4'],"result"=>"0");
	shuffle ($answers);
debug_array("answers",$answers);

	print "<h1>".$puzzle['title']."</h1>\n";
	print "<font size=\"-2\">(".$puzzle['active'].":".$puzzle['correct'].":".$puzzle['tries'].")</font></br>\n";
	print "<p>".$puzzle['puzzletext']."</p><hr>\n";

	print "<form method=\"post\" action=\"index.php?mode=parseanswer\"> \n";
	print "<table border=0>";
	foreach($answers as $answerdata){
		$result=$answerdata['result'];
		$answer = $answerdata['answer'];
		print "<tr>";
		print "<td><input type =\"radio\" name=\"radioset\", value=\"$result\"/><b>$answer </b></td>";
		print "</tr>";
	}
	print "</table>\n";
	print "<input type=\"hidden\" name=\"puzzleno\" value=\"$puzzleno\"/>\n";
	print "<button type=\"submit\" name=\"submit\" value=\"submit\">Answer</button>\n";

	print "</form><hr>\n";

	print "<a href=\"index.php\">Skip This One</a>";
}

/*****************************************
 * parse_answer()
 * parse the answer
 * INPUT: the parameters
 * OUTPUT: Result page, set the database
 * RETURN:
 *****************************************/
function parse_answer($PARAMS,$puzzleno){
	global $link;
	debug_string("parse_answer(PARAMS,$puzzleno)");

	debug_array("params",$PARAMS);


	if (!array_key_exists('radioset',$PARAMS)) $correct=0;
	else {
		$result = $PARAMS['radioset'];
		$sql = "select *  from puzzles  where  puzzleid=$puzzleno ";
		debug_string("right answer",$sql);
		$cards=MYSQLGetData($link,$sql);
		debug_array("meanings",$cards[0]);

		if ($result==1)$correct=1;
		else $correct=0;
	}
	// up date the deck based on the answer we just saw
	$sql = "update puzzles set correct=$correct, tries=tries+1 where puzzleid=$puzzleno";
	debug_string("up date card",$sql);
	$result = mysql_query($sql,$link) or die(mysql_error());

	if($correct==1)display_forcorrect($puzzleno);
	else display_forwrong($puzzleno);
}
/*****************************************
 * display_forwrong()
 * displays the page for when you're wrong
 * INPUT: None
 * OUTPUT: Draws the page
 * RETURN: just exits
 *****************************************/
function display_forwrong($puzzleno,$title=""){
	debug_string("display_forwrong($puzzleno,$title)");
print "<body bgcolor=\"#ff6060\" >\n";
	// get the right answer

	print "<h2>Sorry, you got that wrong. </h2>\n";

	// display the card
	print "</br><a href=\"index.php\"><b>Try Again</b></a></br>\n";

}
/*****************************************
 * display_forcorrect()
 * displays the page when you're right
 * INPUT: None
 * OUTPUT: Draws the page
 * RETURN: just exits
 *****************************************/
function display_forcorrect($puzzleno,$title=""){
	global $link;
	debug_string("display_forcorrect($puzzleno,$title)");
print "<body bgcolor=\"#dfffdf\" >\n";

		// get the right answer

	// tell the user
	print "<h2>CORRECT!</h2> \n";

// display the card

	print "</br><a href=\"index.php\"><b>Next Puzzle</b></a></br>\n";

}
/*****************************************
 * display_success_page()
 * displays the sucess page
 * INPUT: None
 * OUTPUT: Draws the page
 * RETURN: none
 *****************************************/
 function display_success_page(){
 	global $link;
	debug_string("display_success_page()");

	print "<h1>YAY, you've won!</h1>";
	print "<h2>The PRIZE is in the purple game box!</h2><hr><h3>STATISTICS</h3>\n";
display_score($link);
	exit();
 }
function display_score($link){
	$sql = "select * from puzzles where active=1 and correct = 1";
	$puzzles=MYSQLGetData($link,$sql);

	print "<table border=1>\n";
	print "<tr><th>&nbsp;Title&nbsp;</th><th>&nbsp;Correct&nbsp;</th><th>&nbsp;Tries&nbsp;</th><th>Score</th></tr>\n";

	$totaltries=$totalcorrect=$totalscore=0;
	foreach($puzzles as $puzzle){
		$puzzleid=$puzzle['puzzleid'];
		$title = $puzzle['title'];
		$totalcorrect += $correct = $puzzle['correct'];
		$totaltries += $tries = $puzzle['tries'];
		if ($tries!=0)$totalscore += $score =round($correct/$tries*100,1);
		else $score = $correct;
		print "<tr>";
//		print "<td><a href=\"index.php?mode=display_puzzle&puzzleno=$puzzleid\"><b>$title	</b></a>&nbsp;</td>";
		print "<td>$title	</b>&nbsp;</td>";
		print "<td><center>".$correct."</td>";
		print "<td><center>".$tries."</td>";
		print "<td><center>".$score."%</td>";
		print "</tr>\n";
	}
		print "<tr>";
		if($totalcorrect>0)	$totalscore = $totalscore/$totalcorrect;
		else $totalscore=0;

		print "<td><b>Totals</td><td><center><i>$totalcorrect</td><td><center><i>$totaltries</td><td><center><i>$totalscore%</td></tr>\n";

	print "</table>\n";
	if($totalscore>=100) print "<h3>Congratulations! A <i>perfect</i> score!\n";
	else if ($totalscore >= 90) print "<h3>You get an <i>A</i> for that. Good Job!\n";
	else if ($totalscore >= 80) print "<h3>You get an <i>B</i>  for that. Not Bad\n";
	else if ($totalscore >= 70) print "<h3>You get an <i>C</i>  for that. Whew, eeked by!\n";
	else if ($totalscore >= 60) print "<h3>You get an <i>D</i>  for that.  Good grief!\n";
	else if ($totalscore >= 50) print "<h3>You get an <i>F</i>  for that. Is that degree <i>REAL?</i>\n";
	else  print "<h3>Where did YOU learn to drive!? (Was it really <i>THAT</i> hard!?)\n";
	print "<hr><a href=\"index.php?mode=init_puzzles\">Reset and Do It Again!</a>\n";
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
