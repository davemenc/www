<?PHP
/*
    Copyright (c) 2007 Dave Menconi

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

/**************** INCLUDES ****************/
//include_once("../library/mysql.php");
//include_once("config.php");

/**************** REGEX ****************/
$viewjob="/^.*View Job.*$/";
$salaries = "/^.*Salaries.*$/";
$ago = "/^.*ago.*$/";
$logo= "/^.*\[*.logo\].*/";
$spaces="/^ +$/";
$tab="/\t/";
$dash = "/ - /";
$easyapply = "/^.*Easy Apply.*$/";

/**************** presets****************/

/**************** PROGRAM ****************/

$PARAMS = array_merge($_POST,$_GET);
//print_r($PARAMS);

$data = $PARAMS['data'];
$lines = explode ( "\n",$data);


// replace strings we want to remove
$lines = preg_replace($viewjob, "", $lines);
$lines = preg_replace($easyapply, "", $lines);
$lines = preg_replace($salaries, "", $lines);
$lines = preg_replace ($ago, "", $lines);
$lines = preg_replace ($logo, "", $lines);
$lines = preg_replace ($spaces, "", $lines);
$lines = preg_replace ($tab, "", $lines);

foreach($lines as $idx=>$line) if(strlen($line)<2) unset($lines[$idx]);

$datestamp = date("m/d/Y");

$title =$role = "";
$joblist = "";
$titleflag=true;

foreach($lines as $idx=>$line) {
	if($titleflag){
		$title=whattitle($line);
		$role = whatrole($line);
		$joblist .=trim($line);
		$titleflag=false;
	}else{
		$titleflag=true;
		$line = preg_replace($dash, "\t",$line);
		$joblist .= "\t".trim($line)."\t$datestamp\t$title\t$role\n";
		$title =$role = "";
	}
}
print $joblist;
exit();
/**************** FUNCTIONS ****************/
function whatrole($line){
	$keywordlist=array("Test","Planning","QA","Quality","Software","SW","ce","ops","product","qa","qa eng","Hardware", "HW","sales","support","sw","marketing");
	$rolelist =  array("QA","Planning","QA","QA",     "SW",      "SW","ce","ops","product","qa","qa    ","HW",       "HW","sales","support","sw","marketing");
	foreach($keywordlist as $idx=>$word){
		$pos=strpos(strtolower ($line),strtolower ($word));
		if($pos!==false) return ($rolelist[$idx]);// we found it!
	}
	return ""; // we did not find it!
}
function whattitle($line){
	$keywordlist = array("Director","Project",  "program",  "Senior Director","Sr. Director","Senior Manager","Sr. Manager","Analyst","Manager","Engineer");
	$titlelist =   array("Director","P Manager","P Manager","S Director",     "S Director",  "S Manager",     "S Manager",  "Analyst","Manager","Engineer");
	foreach($keywordlist as $idx=>$word){
		$pos=strpos(strtolower ($line),strtolower ($word));
		if($pos!==false) return ($titlelist[$idx]);// we found it!
	}
	return ""; // we did not find it!
}
?>