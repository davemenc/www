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
include_once("config.php");
include_once("debug.php");
include_once("mysql.php");
include_once("miscfunc.php");
include_once("htmlfuncs.php");
log_on();
if ($error_reporting){
	ini_set('display_errors',E_ERROR );
	ini_set('display_startup_errors', E_ERROR );
	error_reporting(E_ALL );
}
error_reporting(E_STRICT);

$PARAMS = array_merge($_POST,$_GET);
$link = make_mysql_connect($dbhost,$dbuser,$dbpass,$dbname);


/************ TEST ****************/
//Play_Playlist(1,$link);
play_5($link);
/************ TEST ****************/
break_mysql_connect($link);
exit();

/******************* FUNCTIONS ******************/
function play_files($filelist){
	$arg = "\"C:/Program Files (x86)/VideoLAN/VLC/vlc.exe\" ";
	foreach ($filelist as $file ){
		$arg .= "\"$file\" ";
	}
//	print $arg;
	exec($arg);
}
function Play_Playlist($playlistno,$link){
	$sql = "select * from playlisttrack where playlist=$playlistno";
	$track_list = MYSQLGetData($link,$sql);
	$listcount = count($PL_list);
	if ($listcount==0){
		print "Playlist #$playlistno does not exist.\n";
	}
	$filelist = array();
	foreach ($track_list as $record){
		$trackno = $record['track'];
		$sql = "select filename from track where id=$trackno";
		$filedatalist = MYSQLGetData($link,$sql);
		$filename = $filedatalist[0]['filename'];
		$filelist[] = $filename;
	}
	play_files($filelist);
}
function play_5($link){
	$filelist = array();
	$sql = "select filename from track limit 1000";
	$filedatalist = MYSQLGetData($link,$sql);
	foreach ($filedatalist as $filedata){
		$filename = $filedata['filename'];
		$filenamelist[] = $filename;
	}
	shuffle($filenamelist);
	$mylist = array_slice($filenamelist,1,5);
	play_files($mylist);
}
?>

