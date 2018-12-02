<?php
/*
        Copyright 2018 Dave Menconi
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
/*****************************************
 * INDEX.PHP -- ROUND EARTH
 *****************************************/

	include_once 'config.php';
	include_once 'header.php';
	include_once 'footer.php';
	include_once 'debug.php';
	include_once "mysql.php";
log_on();
//debug_on();
debug_string("--------------------------------- START CHOOSE YOUR OWN ADVENTURE PROGAM _-------");
debug_string("usertable", $usertable);
$PARAMS = array_merge($_POST,$_GET);
// Create link to DB
$link = make_mysql_connect($dbhost, $dbuser, $dbpass, $dbname);

debug_array("params",$PARAMS);

// set the mode
if (isset($PARAMS['mode'])){$mode=$PARAMS['mode'];}
else $mode = "listadventures";

// check for admin
$admin = False;
$admin = True;


//if (isset($PARAMS['admin']) && $PARAMS['admin']==$adminpassword) 	$admin=True;

debug_string("mode before switch",$mode);

switch($mode){
	case "listadventures":
		debug_string("Case: Display_page");
		ListAdventures($link,$admin);
		break;
	case "playadventure":
		debug_string("Case: playadventure");
		if(isset($PARAMS['adventure'])) $adventure = $PARAMS['adventure'];
		else ListAdventures($link,$admin);
		PlayAdventure($link,$adventure,$admin);
		break;
	case "editadventure":
		debug_string("Case: editadventure");
		debug_string("admin",$admin);
		if(isset($PARAMS['adventure']) && $admin) $adventure = $PARAMS['adventure'];
		else ListAdventures($link,$admin);
		EditAdventure($link,$adventure,$admin);
		break;
	case "showsituation":
		debug_string("Case: showsituation");
		if(isset($PARAMS['situation'])) $situation = $PARAMS['situation'];
		else ListAdventures($link,$admin);
		ShowSituation($link,$situation,$admin);
		break;
	case "createsituation":
		debug_string("Case: createsituation");
		if(isset($PARAMS['adventure']) && $admin) $adventure = $PARAMS['adventure'];
		else ListAdventures($link,$admin);
		CreateSituation($link,$adventure,$admin);
		break;
	case "createchoice":
		debug_string("Case: createchoice");
		if(isset($PARAMS['situation']) && isset($PARAMS['adventure']) && $admin){
			$situation = $PARAMS['situation'];
			$adventure = $PARAMS['adventure'];
			CreateChoice($link,$adventure,$situation,$admin);

		}
		ListAdventures($link,$admin);
		break;
	case "editsituation":
		debug_string("Case: editsituation");
		$situation = $adventure = 0;
		if(isset($PARAMS['situation']) && $admin) $situation = $PARAMS['situation'];
		if(isset($PARAMS['adventure']) && $admin) $adventure = $PARAMS['adventure'];
		if ($situation == 0 || $adventure == 0) ListAdventures($link,$admin);
		else EditSituation($link,$adventure,$situation,$admin);
		break;
	case "listsituationsinadventure":
		debug_string("Case: listsituationsinadventure");
		if(isset($PARAMS['adventure']) && $admin) $adventure = $PARAMS['adventure'];
		else ListAdventures($link,$admin);
		ListSituations($link,$adventure,$admin);
		break;
	case "testadventure":
		debug_string("Case: testadventure");
		if(isset($PARAMS['adventure']) && $admin) $adventure = $PARAMS['adventure'];
		else ListAdventures($link,$admin);
		TestAdventure($link,$adventure,$admin);
		break;
	case "createadventure":
		debug_string("case: createadventure");
		if($admin) CreateAdventure($link);
		else  ListAdventures($link,$admin);
		break;
	case "parse_e_adventure":
		debug_string("case: parse_e_adventure");
		if ($admin) ParseEAdventure($link,$PARAMS,$admin);
		else  ListAdventures($link,$admin);
		break;
	case "parse_c_adventure":
		debug_string("case: parse_c_adventure");

		if ($admin) ParseCAdventure($link,$PARAMS,$admin);
		else  ListAdventures($link,$admin);
		break;
	case "parse_e_situation":
		debug_string("case: parse_e_adventure");
		if ($admin) ParseESituation($link,$PARAMS,$admin);
		else  ListAdventures($link,$admin);
		break;
	case "parse_c_situation":
		debug_string("case: parse_c_adventure");
		if ($admin) ParseCSitation($link,$PARAMS,$admin);
		else  ListAdventures($link,$admin);
		break;
	case "parse_e_choice":
		debug_string("case: parse_e_adventure");
		if ($admin) ParseEChoice($link,$PARAMS,$admin);
		else  ListAdventures($link,$admin);
		break;
	case "parse_c_choice":
		debug_string("case: parse_c_choice");
		if ($admin) ParseCChoice($link,$PARAMS,$admin);
		else  ListAdventures($link,$admin);
		break;
	case "playsituation":
		debug_string("case: playsituation");
		PlaySituation($link,"",$PARAMS,$admin);
	case "test":
		debug_string("Case: test");
		$adventure = $PARAMS['adventure'];
		EditAdventure($link,$adventure,$admin);
		break;
	default:
		debug_string("case default: mode=$mode");
		ListAdventures($link,$admin);
}
break_mysql_connect($link);
exit();
/******************************** FUNCTIONS *************************/
/********************************************
 * FixString
 ********************************************/
function FixString($s){
	debug_string("FixString($s)");
	 return str_replace( "'", "\\'" , $s);
}
/********************************************
 * ParseCSitation
 ********************************************/
function ParseCSitation($link,$PARAMS,$admin){
	debug_string("ParseCSitation(link,PARAMS)");
	debug_array("PARAMS",$PARAMS);
	if (isset($PARAMS['name']) && isset($PARAMS['type']) && isset($PARAMS['adventure']) && isset($PARAMS['textblock'])){
		debug_string("setting up insert in ParseCSitation");
		$name = FixString($PARAMS['name']);
		$type  = FixString($PARAMS['type']);
		$adventure = FixString($PARAMS['adventure']);
		$textblock = FixString($PARAMS['textblock']);
		$sql = "insert situation (name,type,adventure,textblock) values('$name','$type','$adventure','$textblock')";
		debug_string("sql9",$sql);
 		$result = do_mysql($link,$sql,true);
 		EditAdventure($link,$adventure,$admin);
 		exit();
	}
	else {
		debug_string("ParseCSitation failed for lack of PARAMS");
		ListAdventures($link,$admin);
	}
}
/********************************************
 * ParseCChoice
 ********************************************/
function ParseCChoice($link,$PARAMS,$admin){
	debug_string("ParseCChoice(link,PARAMS)");
	debug_array("PARAMS",$PARAMS);
	if (isset($PARAMS['seq']) && isset($PARAMS['parent']) && isset($PARAMS['child']) && isset($PARAMS['textblock'])){
		debug_string("setting up insert in ParseCChoice");
		$seq = FixString($PARAMS['seq']);
		$parent  = FixString($PARAMS['parent']);
		$child = FixString($PARAMS['child']);
		$textblock = FixString($PARAMS['textblock']);
		$adventure = $PARAMS['adventure'];
		$sql = "insert choice (seq,parent,child,textblock) values('$seq','$parent','$child','$textblock')";
		debug_string("sql10",$sql);
 		$result = do_mysql($link,$sql,true);
	}
	else {
		debug_string("ParseCChoice failed for lack of PARAMS");
	}
	EditAdventure($link,$adventure,$admin);
}
/********************************************
 * status_menu
 ********************************************/
function status_menu($mystatus){
	debug_string("type_menu($mystatus)");
	$return = "<select name=\"status\"> \n";
	$statuslist = array("hidden","active","deleted");
	foreach ($statuslist as $status){
		$selected = "";
		if ($status==$mystatus) $selected = " selected ";
		$return .= "\t\t\t\t\t<option  value=\"$status\"$selected>$status</option>\n";
	}
	$return .= "\t\t\t\t</select>";
	return $return;
}
/********************************************
 * type_menu
 ********************************************/
function type_menu($mytype){
	debug_string("type_menu($mytype)");
	$return = "<select name=\"type\"> \n";
	$typelist = array("start","middle","end");
	foreach ($typelist as $type){
		$selected = "";
		if ($type==$mytype) $selected = " selected ";
		$return .= "\t\t\t\t\t<option  value=\"$type\"$selected>$type</option>\n";
	}
	$return .= "\t\t\t\t</select>";
	return $return;
}

/********************************************
 * situation_menu
 ********************************************/
function situation_menu($situations,$current=0,$name="child"){
	debug_string("situation_menu(situations,$current)");
	//debug_array("situations",$situations);
	$return = "<select name=\"$name\"> \n";
	foreach ($situations as $situation){
		$id = $situation['id'];
		$name = $situation['name'];

		$selected = "";
		if ($id==$current) $selected = " selected ";
		$return .= "\t\t\t\t\t<option  value=\"$id\"$selected>$name</option>\n";
	}
	$return .= "\t\t\t\t</select>";
	return $return;
}
/********************************************
 * EditAdventure
 ********************************************/
function EditAdventure($link,$adventure,$admin){
	global $header,$footer;
	debug_string("EditAdventure(link,$adventure)");
	if (!$admin) ListAdventures($link,$admin);

	print $header;

	/* ADVENTURE */
	$sql = "select * from adventure where id = '$adventure'";
	debug_string("sqltest1",$sql);
	$adventures = MYSQLGetData($link,$sql);
	debug_array("adventure",$adventures[0]);
	$adv_id = $adventures[0]['id'];
	$title = $adventures[0]['title'];
	$descr = $adventures[0]['descr'];
	$status = $adventures[0]['status'];
	print "<h1>Adventure: $title </h1>\n";
	print "<h2>Edit Adventure</h2>\n";
	$status_menu = status_menu($status);
	print "<form method=\"POST\" action=\"index.php\">\n
		<input type=\"hidden\" name=\"mode\" value=\"parse_e_adventure\"/>\n
		<input type=\"hidden\" name=\"adventure\" value=\"$adventure\"/>\n
		<input type=text name=\"title\" value=\"$title\" size=80>\n
		<textarea name=\"descr\" rows=\"1\" cols=\"60\" >$descr</textarea>\n
		$status_menu\n
		<input type=\"submit\" value=\"Update Adventure Changes\">\n
		</form>";
	print "<a href=\"index.php?adventure=$adventure&mode=createsituation\">Create a New Situation</a></br>";

	/* SITUATION */
	$sql = "select id,name from situation where adventure='$adventure'";
	debug_string("sqltest0",$sql);
	$allsituations = MYSQLGetData($link,$sql);

	$sql = "select * from situation where adventure='$adventure' order by type";

	debug_string("sqltest2",$sql);
	$situations = MYSQLGetData($link,$sql);
	$lasttype = "";

	foreach ($situations as $situation){
		$sit_id = $situation['id'];
		$name = $situation['name'];
		$textblock = $situation['textblock'];
		$type = $situation['type'];
		if ($type!=$lasttype){
			$lasttype = $type;
			switch ($type){
				case "start":
					print "<h2>EDIT START SITUATION</h2>\n";
					break;
				case "middle":
					print "<h2>EDIT MIDDLE SITUATIONS</h2>\n";
					break;
				case "end":
					print "<h2>EDIT END SITUATIONS</h2>\n";
					break;
			}
		}
		$type_menu=type_menu($type);
		print "<h3>Situation #$sit_id: $name</h3>\n";
		print "<form method=\"POST\" action=\"index.php\">\n
			<input type=\"hidden\" name=\"mode\" value=\"parse_e_situation\"/>\n
			<input type=\"hidden\" name=\"adventure\" value=\"$adventure\"/>\n
			<input type=\"hidden\" name=\"situation\" value=\"$sit_id\"/>\n
			<input type=text name=\"name\" value=\"$name\" size=80>\n
			<textarea name=\"textblock\" rows=\"4\" cols=\"70\" >$textblock</textarea>\n
			$type_menu\n
			<input type=\"submit\" value=\"Update Situation Changes\">\n
			</form>";
		/* CHOICES */
		if ($type != 'end'){
			print "</br><a href=\"index.php?adventure=$adventure&situation=$sit_id&mode=createchoice\">Create A New Choice</a></br>\n";
			print "<h3>Choices</h3>";
			$sql = "select choice.id as ch_id,choice.seq as seq,choice.textblock as choicetext,situation.name as newsituation,child from choice,situation  where parent='$sit_id'  and choice.child=situation.id order by seq";
			debug_string("sqltest2a",$sql);
			$choices = MYSQLGetData($link,$sql);
			foreach ($choices as $choice){
				$ch_id = $choice['ch_id'];
				$choicetext = $choice['choicetext'];
				$newsituation = $choice['newsituation'];
				$child = $choice['child'];
				$seq = $choice['seq'];
				$situation_menu = situation_menu($allsituations,$child);
				print "\n<form method=\"POST\" action=\"index.php\">\n
					<input type=\"hidden\" name=\"mode\" value=\"parse_e_choice\"/>\n
					<input type=\"hidden\" name=\"adventure\" value=\"$adventure\"/>\n
					<input type=\"hidden\" name=\"situation\" value=\"$sit_id\"/>\n
					<input type=\"hidden\" name=\"choice\" value=\"$ch_id\"/>\n
					<input type=text name=\"seq\" value=\"$seq\" size=5>\n";
				//$choicetext=str_replace( '"', "&quot;" , $choicetext);
				//print "<input type=text name=\"choicetext\" value=\"$choicetext\" size=90>\n";
				print "<textarea name=\"choicetext\" rows=\"1\" cols=\"90\">$choicetext</textarea>\n";
				print "$situation_menu\n
					<input type=\"submit\" value=\"Update Choice Changes\"> \n
				</form>";
			}
		}
	}
}

/********************************************
 * ListAdventures
 ********************************************/
function ListAdventures($link,$admin=false){
	global $header,$footer;
	debug_string(" ListAdventures(link)");
	print $header;
	if ($admin) $sql = "select * from adventure";
	else $sql = "select * from adventure where status = 'active'";
	debug_string("sql1",$sql);
	$adventures = MYSQLGetData($link,$sql);
	debug_array("adventures list",$adventures);
	print "WHA THT HECK";
	print "<table border=\"1\">\n";
	print "<tr><th>Title</th><th>Descr</th><th>Action</th></tr>\n";
	foreach($adventures as $adventure){
		debug_array("adventure",$adventure);
		$title = $adventure['title'];
		$descr = $adventure['descr'];
		$id = $adventure['id'];
		print "<tr><td>$title</td><td>$descr</td><td><a href=\"index.php?adventure=$id&mode=playadventure\">Play</a>";
		if ($admin) print " | <a href=\"index.php?adventure=$id&mode=editadventure\">Edit</a> | <a href=\"index.php?adventure=$id&mode=testadventure\">Test</a>";
		print "</td></tr>\n";
	}
	print "</table>";
	print "<a href=\"index.php?mode=createadventure&admin=$admin\">Create New Adventure </a></br>\n";
	print $footer;

	//This is a terminal function so we exit here.
	break_mysql_connect($link);
	exit();
}

/********************************************
 * PlayAdventure
 ********************************************/
function PlayAdventure($link,$adventure,$admin){
	debug_string("PlayAdventure(link,$adventure)");
	$sql = "select * from  situation where type='start' and  adventure='$adventure'";
	debug_string("sql4",$sql);
	$firstsituation = MYSQLGetData($link,$sql);
	//debug_array("firstsituation",$firstsituation);
	$id = $firstsituation[0]['id'];
	//debug_string("ID",$id);
	//debug_string("calling playsituation with $id");
	PlaySituation($link,$id,"",$admin);
}
/********************************************
 * PlaySituation
 ********************************************/
function PlaySituation($link,$situation ="",$params="",$admin=false){
	global $header,$footer;
	if ($params != "")debug_array("params",$params);
	debug_string("situation",$situation);
	if ($situation == ""){
		if (isset($params['situation'])) $situation = $params['situation'];
		else ListAdventures($link,$admin);
		debug_string("situation",$situation);
	}
	debug_string("PlaySituation(link,$situation)");
	$sql = "select name, textblock,format,type from situation where id='$situation'";
	//debug_string("sql5a",$sql);
	$currentsituation = MYSQLGetData($link,$sql);
	$currentsituation  = $currentsituation[0];
	//debug_array("currentsituation 0",$currentsituation);
	$name = $currentsituation['name'];
	$type = $currentsituation['type'];
	$format = $currentsituation['format'];
	$sittext = ConvertTextBlock($currentsituation['textblock'],$format);


	print $header;
	print "<h2>$name</h2>\n";
	print "<p>$sittext</br>\n";

	if ($currentsituation['type'] != "end") {//if it is not the end, play the choices
		$sql = "select child,textblock,format from choice where parent='$situation' order by seq";
		debug_string("sql5b",$sql);
		$choices = MYSQLGetData($link,$sql);
		print "\n<ul>\n";
		foreach ($choices as $choice){
			//debug_array("choice",$choice);
			$nextsituation = $choice['child'];
			$text = convertTextBlock($choice['textblock'],$choice['format']);
			print "<li><a href=\"index.php?mode=playsituation&situation=$nextsituation\"> $text</a></li>\n";
		}
		print "</ul></br>\n";
	}
	print $footer;
	exit();
}

/********************************************
 * GetData
 ********************************************/
function GetData($link,$sql,$line){
	debug_string("sql $line",$sql);
	$result =  MYSQLGetData($link,$sql);
	if (count($result>0)) debug_array("sql result",$result);
	return $result;
}
/********************************************
 * DisplayError
 ********************************************/
function DisplayError($test,$msg){
	if (!$test) print "$msg</br>\n";
}
/********************************************
 * TestAdventure
 ********************************************/
function TestAdventure($link,$adventure,$admin){
	global $header,$footer;
	debug_string(" TestAdventure(link,$adventure)");
	if (!$admin)exit();
	$MAX_LENGTH = 10;

	//print $header;
	$PathQueue = array();
	$start = GetData($link,"select id from situation where adventure='$adventure' and type='start'",__LINE__);
	DisplayError(count($start)>0,"Error: Adventure $adventure has no starting situation.");
	if (count($start)<1) exit();
	$id = $start[0]['id'];
	array_push($PathQueue,array($id));
	$complete_path = array();
	while (count($PathQueue)>0){
		//take a path off the queue.
		$this_path = array_pop($PathQueue);

		//check the last situation on the path. If it's an end situation this path is complete; put it on the complete list.
		$last = end($this_path);
		$type = GetData($link,"select type from situation where id='$last'",__LINE__);
		$type = $type[0]['type'];
		if ($type == "end") {
			$complete_path[] = $this_path;
			continue;
		}

		//Get the choices for that last situation (to wit, the choices with that situation as their parent).
		$choices = GetData($link,"select child from choice where parent='$last'",__LINE__);
		DisplayError(count($choices)>0,"Situation $last has no choices.");


		foreach ($choices as $choice){
			//Make a duplicate path for each choice with the child situation as the last entry on the list
			$new_path = array_merge($this_path,array($choice['child']));

			//If the resulting path has duplicate situations throw it away.
			 if(count($new_path) !== count(array_unique($new_path))) continue;

			//if the resulting path is more than some long length (5? 10?), it's a problem and should be flagged.
			if (count($new_path)>$MAX_LENGTH){
				DisplayError(False,"Error: The path has more than $MAX_LENGTH steps. ");
				print_r($new_path);
				continue;
			}

			//else put the new path onto the queue.
			array_push($PathQueue,$new_path);
		}
	}
//	print "$complete_path: "; print_r($complete_path);print "</br>\n";
	print "Completed Paths</br>\n";
	$count = 0;
	foreach ($complete_path as $path){
		print "Path #$count: ";
		foreach ($path as $id){
			print $id;
			print ",";
		}
		print "</br>\n";
		$count++;
	}
	//print $footer;
}

/********************************************
 *ListSituations
 ********************************************/
function ListSituations($link,$adventure,$admin){
	global $header,$footer;
	debug_string(" ListSituations(link,$adventure)");
	print $header;
	print $footer;
}
/********************************************
 * CreateSituation
 ********************************************/
function CreateSituation($link,$adventure,$admin){
	global $header,$footer;
	debug_string(" CreateSituation(link,$adventure)");
	print $header;
	print "<form method=\"POST\" action=\"index.php\">";
	print "<input type=\"hidden\" name=\"mode\" value=\"parse_c_situation\"/>";
	print "<input type=\"hidden\" name=\"adventure\" value=\"$adventure\"/>";

	print "<h3>Create Situation</h3>";
	print "Name</br>";
	print "<input type=text name=\"name\" size=80></br></br>";
	print "Type</br>";
	print "<select  name=\"type\">\n
	<option value=\"start\" >Start<option value=\"Middle\" selected>Middle<option value=\"end\">End</select></br></br>";
	print "TextBlock</br>";
	print " <textarea name=\"textblock\" rows=\"9\" cols=\"80\" ></textarea></br>";
	print "<input type=\"submit\" value=\"Create\"></br>";
	print " </form>";

	print $footer;
}
/********************************************
 * CreateChoice
 ********************************************/
function CreateChoice($link,$adventure,$situation,$admin){
	global $header,$footer;
	debug_string(" CreateChoice(link,$situation)");

	$sql = "select id,name from situation where adventure='$adventure'";
	debug_string("sql11a",$sql);
	$allsituations = MYSQLGetData($link,$sql);
	$situation_menu = situation_menu($allsituations,$situation);

	$sql = "select id,name from situation where id='$situation'";
	debug_string("sql11c",$sql);
	$thissituation = MYSQLGetData($link,$sql);
	$name = $thissituation[0]['name'];
	$id = $thissituation[0]['id'];

	$sql = "select seq, textblock from choice where parent='$situation' order by seq desc limit 1";
	debug_string("sql11b",$sql);
	$maxseq = MYSQLGetData($link,$sql);
	$seq = 1;
	$newseq = $seq;

	$prev_choice = "(none)";
	if (count($maxseq)>0) {
		$seq = $maxseq[0]['seq'];
		$newseq = $seq + 1;
		$prev_choice = $maxseq[0]['textblock'];
	}

	print $header;
	print "\n<form method=\"POST\" action=\"index.php\">\n";
	print "<input type=\"hidden\" name=\"mode\" value=\"parse_c_choice\"/>\n";
	print "<input type=\"hidden\" name=\"adventure\" value=\"$adventure\"/>\n";
	print "<input type=\"hidden\" name=\"situation\" value=\"$situation\"/>\n";
	print "<h1>Create Choice</h1>\n";
	print "<h2>For Situation: $name($id)</h2>\n";
	print "<b>Seq:</b> \n";
	print "<input type=text name=\"seq\" value=\"$newseq\" size=5>\n";
	print "&nbsp;&nbsp;&nbsp;&nbsp;<b>Type:</b>\n";
	print "<select  name=\"type\">\n
			<option value=\"start\" >Start\n
			<option value=\"Middle\" selected>Middle\n
			<option value=\"End\" >End\n
			</select>\n
			</br>";

	$situation_menu = situation_menu($allsituations,$situation,"parent");
	print "<b>PARENT:</b> $situation_menu</br>";
	$situation_menu = situation_menu($allsituations,$situation,"child");
	print "<b>CHILD: &nbsp;&nbsp;&nbsp;</b>$situation_menu</br>";
	print "<b>Choice Text</b></br>";
	print " <textarea name=\"textblock\" rows=\"1\" cols=\"50\" ></textarea></br>";
	print "<input type=\"submit\" value=\"Create\"></br>";
	print " </form>";
	print "<h4>Previoius Choice: $prev_choice (seq $seq)</h4>";

	print $footer;
	exit();
}
/********************************************
 *
 ********************************************/
function EditSituation($link,$adventure,$situation,$admin){
	global $header,$footer;
	debug_string(" EditSituation(link,$situation)");
		global $header,$footer;
		debug_string(" EditSituation(link,$adventure)");

		$sql = "select * from situation where adventure = '$adventure' and situation='$situation'";
		debug_string("sql7",$sql);
		$sitfields = MYSQLGetData($link,$sql);
		$sitfields = $sitfields[0];
		debug_array("situation $adventure:$situation",$sitfields);
		$id = $sitfields['id'];
		$name = $sitfields['name'];
		$type = $sitfields['type'];
		$adventure = $sitfields['adventure'];
		$textblock = $sitfields['textblock'];
		$format = $sitfields['format'];
		$deleted = $sitfields['deleted'];
		$ts = $sitfields['ts'];

		print $header;
		print "<form method=\"POST\" action=\"index.php\">";
		print "<input type=\"hidden\" name=\"mode\" value=\"parse_e_adventure\"/>";
		print "<input type=\"hidden\" name=\"adventure\" value=\"$adventure\"/>";
		print "<input type=\"hidden\" name=\"situation\" value=\"$id\"/>";
		print "<h3>Edit Situation</h3>";
		print "Type</br>";
		print "<select  name=\"type\"><option value=\"start\" >Start<option value=\"middle\" selected>Middle<option value=\"end\" selected>End</select></br></br>";
		print "Name</br>";
		print "<input type=text name=\"name\" value =\"$name\" size=80></br>";
		print "Textblock</br>";
		print " <textarea name=\"textblock\" rows=\"9\" cols=\"80\" >$textblock</textarea></br>";
		print "<input type=\"submit\" value=\"Update Changes\">";
		print " </form>";
		print $footer;

}
/********************************************
 *
 ********************************************/
function CreateAdventure($link){
	global $header,$footer;
	debug_string("CreateAdventure(link)");
	print $header;
	print "<form method=\"POST\" action=\"index.php\">";
	print "<input type=\"hidden\" name=\"mode\" value=\"parse_c_adventure\"/>";
	print "<h3>Create Adventure</h3>";
	print "Status</br>";
	print "<select  name=\"status\"><option value=\"Active\" >Active<option value=\"Hidden\" selected>Hidden</select></br></br>";
	print "Title</br>";
	print "<input type=text name=\"title\" size=80></br></br>";
	print "Descrition</br>";
	print " <textarea name=\"description\" rows=\"9\" cols=\"80\" ></textarea></br>";
	print "<input type=\"submit\" value=\"Create\"></br>";
	print " </form>";
	print $footer;
}
/********************************************
 *
 ********************************************/
function ParseCAdventure($link,$PARAMS,$admin){
	debug_string("ParseCAdventure(link,PARAMS)");
	debug_array("PARAMS",$PARAMS);
	if (isset($PARAMS['status']) && isset($PARAMS['title']) && isset($PARAMS['description'])){
		debug_string("setting up inser in ParseCAdventure");
		$status = FixString($PARAMS['status']);
		$title  = FixString($PARAMS['title']);
		$descr = FixString($PARAMS['description']);
		$sql = "insert adventure (status,title,descr) values('$status','$title','$descr')";
		debug_string("sql6",$sql);
 		$result = do_mysql($link,$sql,true);

	}
	else {
		debug_string("failed for lack of PARAMS");
	}
	ListAdventures($link,$admin);
}
/********************************************
 * ParseEAdventure
 ********************************************/
function ParseEAdventure($link,$PARAMS,$admin){
	debug_string("ParseEAdventure(link,PARAMS)");
	debug_array("PARAMS",$PARAMS);
	if (!$admin) ListAdventures($link,$admin);
	if (!isset($PARAMS['adventure'])) ListAdventures($link,$admin);
	else $id = $PARAMS['adventure'];
	debug_string("E: id",$id);
	if (isset($PARAMS['status'])){
		$sql = "update adventure set status='".$PARAMS['status']."' where id='$id'";
		debug_string("sql3a",$sql);
	 	mysqli_update($link,$sql,True);
	}
	if (isset($PARAMS['title'])){
		$sql = "update adventure set title='".$PARAMS['title']."' where id='$id'";
		debug_string("sql3b",$sql);
 		mysqli_update($link,$sql,True);
	}
	if (isset($PARAMS['descr'])){
		$sql = "update adventure set descr='".$PARAMS['descr']."' where id='$id'";
		debug_string("sql3c",$sql);
 		mysqli_update($link,$sql,True);
	}
	EditAdventure($link,$id,$admin);
}
/********************************************
 * ParseESituation
 ********************************************/
function ParseESituation($link,$PARAMS,$admin){
	debug_string("ParseESituation(link,PARAMS)");
	if (!$admin) ListAdventures($link,$admin);
	debug_array("PARAMS",$PARAMS);
	$ad_id = $PARAMS['adventure'];
	$sit_id = $PARAMS['situation'];

	debug_string("Sit E: ad_id",$ad_id);
	debug_string("Sit E: sit_id",$sit_id);
	if (isset($PARAMS['name'])){
		$name = FixString($PARAMS['name']);
		$sql = "update situation set name='".$name."' where id='$sit_id'";
		debug_string("sql12a",$sql);
 		mysqli_update($link,$sql,True);
	}
	if (isset($PARAMS['textblock'])){
		$textblock = FixString($PARAMS['textblock']);
		$sql = "update situation set textblock='".$textblock."' where id='$sit_id'";
		debug_string("sql12b",$sql);
 		mysqli_update($link,$sql,True);
	}
	if (isset($PARAMS['type'])){
		$sql = "update situation set type='".$PARAMS['type']."' where id='$sit_id'";
		debug_string("sql12c",$sql);
 		mysqli_update($link,$sql,True);
	}
	//ListAdventures($link,$admin);
	EditAdventure($link,$ad_id,$admin);
}
/********************************************
 * ParseEChoice
 ********************************************/
function ParseEChoice($link,$PARAMS,$admin){
	debug_string("ParseEChoice(link,PARAMS)");
	if (!$admin) ListAdventures($link,$admin);
	debug_array("PARAMS",$PARAMS);
	$ad_id = $PARAMS['adventure'];
	$sit_id = $PARAMS['situation'];
	$ch_id = $PARAMS['choice'];


	debug_string("choice E: ad_id",$ad_id);
	debug_string("choice E: sit_id",$sit_id);
	debug_string("choice E: ch_id",$ch_id);

	if (isset($PARAMS['choicetext'])){
		$choicetext = FixString($PARAMS['choicetext']);
		debug_string("choicetext",$choicetext);
		$sql = "update choice set textblock='$choicetext' where id='$ch_id'";
		debug_string("sql8a",$sql);
 		mysqli_update($link,$sql,True);
	}
	if (isset($PARAMS['child'])){
		$child = FixString($PARAMS['child']);
		$sql = "update choice set child='$child' where id='$ch_id'";
		debug_string("sql8b",$sql);
		mysqli_update($link,$sql,True);
	}
	if (isset($PARAMS['seq'])){
		$sql = "update choice set seq='".$PARAMS['seq']."' where id='$ch_id'";
		debug_string("sql8c",$sql);
		mysqli_update($link,$sql,True);
	}

	//ListAdventures($link,$admin);
	EditAdventure($link,$ad_id,$admin);
}
/********************************************
 * ConvertTextBlock
 ********************************************/
function ConvertTextBlock($s,$format){
	switch ($format){
		case 2:
			return $s;
			break;
		case 1:
			return nl2br ($s);
			break;
		default:
			print "ERROR: Unknown format $format!";
			exit();
	}
}
?>