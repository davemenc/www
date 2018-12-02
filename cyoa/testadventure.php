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

	include_once 'config.php';
	include_once 'header.php';
	include_once 'footer.php';
	include_once 'debug.php';
	include_once "mysql.php";
log_on();
$PARAMS = array_merge($_POST,$_GET);
// Create link to DB
$link = make_mysql_connect($dbhost, $dbuser, $dbpass, $dbname);
if (isset($PARAMS['mode'])){$mode=$PARAMS['mode'];}
else $mode = "testadventure";
$admin = True;


debug_string("mode before switch",$mode);

switch($mode){
	case "testadventure":
		debug_string("Case: testadventure");
		if(isset($PARAMS['adventure']) && $admin){
			$adventure = $PARAMS['adventure'];
			TestAdventure($link,$adventure,$admin);
		}
		else print "Missing Adventure";
		break;
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
	DisplayError(count($start)>0,"Error: Adventure $adventure has not starting situation.");
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

?>