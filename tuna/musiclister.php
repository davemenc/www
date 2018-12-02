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

/************ TEST ****************/
$directory_list = array("D:\newmusic","D:\My Music");

scan_for_songs($directory_list,"artist_album_track",$link);

break_mysql_connect($link);
exit();

/******************* FUNCTIONS ******************/
function scan_for_songs($directory_list,$mode,$link){
	debug_string( "scan_for_songs(directory_list,$mode,link)\n");
	switch ($mode){
		case "artist_album_track":
			$limit = 200;
			$count = 0;
			$sql = "select * from root_dir";
			debug_string( "SQL0: $sql\n");
			$rootlist = MYSQLGetData($link,$sql);
			debug_array("rootlist",$rootlist);

			$rootlist=array();

			$rootlist[0] = array('id'=>1,'dir_name'=>"D:\\newmusic","td"=>"BOGUS");
			foreach ($rootlist as $rootrecord){
				$id = $rootrecord['id'];
				$dir_name = $rootrecord['dir_name'];
				debug_string("scandir root", $dir_name);
				$artistlist = scandir($dir_name);
				foreach ($artistlist as $artist){

					if ($artist == "." || $artist == "..") continue;
					$ardir = $dir_name."\\".$artist;
					$artist = addslashes($artist);
					debug_string("scandir artist", $ardir);
					$albumlist = scandir($ardir);
					foreach ($albumlist as $album){
						if ($album == "." || $album == "..") continue;
						$aldir = $ardir."\\".$album;

						$album = addslashes($album);
						$tracklist = scandir($aldir);
						if (strpos($album,"mp3")){
							debug_string("*****************************");
							debug_string("ardir",$ardir);
							debug_string("aldir",$aldir);
							debug_string("new album",$album);
							debug_string( "scandir Album",$aldir);
							debug_string("tracklist count",count($tracklist));
							debug_string("____________________________________");
						}
						foreach ($tracklist as $track){
							if ($track == "." || $track == "..") continue;
							$tdir = $aldir."\\".$track;
							$track = addslashes($track);
							debug_string( "$tdir\n");
							save_track($link,$dir_name,$artist,$album,$tdir,$track);
						}
					}
				}
			}
			break;
		default:
			print "error: Invalid mode: $mode";
			exit();
	}
}


function add_new_element($link, $table,$field,$value){
	debug_string( "add_new_element(link, $table,$field,$value)\n");

	$value = addslashes($value);
	$sql = "insert into $table ($field) values ('$value')";
	debug_string( "SQL3: $sql\n");

	$result = do_mysql($link,$sql,$die=true);
	$new_id = mysqli_insert_id($link);
	return $new_id;
}
function get_element_id($link,$table,$field,$value,$id_field="id"){
	debug_string( "get_element_id(link,$table,$field,$value,$id_field=\"id\")\n");
	$sql = "select * from $table where $table.$field='$value'";
	debug_string( "SQL4: $sql\n");

	$list = MYSQLGetData($link,$sql);
	$count = count($list);
	if ($count==0){ // there is no record: add it & get an index #
		$id = add_new_element($link,$table,$field,$value);
	}else { // there is at least one
		if ($count>1) print "ERROR: in rootlist there are multiple records for $dir_name!"; // there should't be duplicates
		$id = $list[0][$id_field];
	}
	return $id;
}
function parse_trackfname($trackfname,$link){
	$result = array("format_id"=>"","trackno"=>"","trackname"=>"");
	//format
	$extpos = strrpos($trackfname,".");
	$format = substr($trackfname,$extpos+1);
	$format_id = get_element_id($link,"format","formatname",$format);
	$result["format_id"] = $format_id;

	// track no
	$trackpos = strpos($trackfname,"-");
	$tracknostring = substr($trackfname,0,$trackpos);
	$trackno = (float)$tracknostring;
	$result["trackno"] = $trackno;

	// track name
	$namelen = $extpos-$trackpos-1;
	$trackname = substr($trackfname,$trackpos+1,$namelen);
	$result['trackname'] = $trackname;

	return $result;
}
function save_track($link,$dir_name,$artist,$album,$trackfname){
	debug_string( "save_track(link,$dir_name,$artist,$album,$trackfname)\n");

	$trackfname_esc = addslashes($trackfname);
	$sql = "select * from track where filename='$trackfname_esc'";
	debug_string( "SQL1", $sql);
	$list = MYSQLGetData($link,$sql);
	$count = count($list);
	if ($count>0) {
		debug_string( "***** ERROR: DUPLICATE TRACK *****\n");
		return;
	}

	// parse filename

	$track_info = parse_trackfname($trackfname,$link);
	$format_id = $track_info['format_id'] ;
	$trackno = $track_info['trackno'];
	$trackname = $track_info['trackname'];

	// get (or save) dir_name out of "root_dir"
	$root_id = get_element_id($link,"root_dir","dir_name",$dir_name);

	// get (or save) artist # out of "artist"
	$artist_id = get_element_id($link,"artist","artistname",$artist);

	// get (or save) album # out of "album"
	$album_id = get_element_id($link,"album","albumname",$album);

	// get (or save) path # out of "path"
	$pathname = addslashes($trackfname);
	debug_string("trackfname",$trackfname);
	debug_string("pathname",$pathname);
	debug_string("dir_name",$dir_name);
	debug_string("artist",$artist);

	$path_id = get_element_id($link,"path","pathname",$pathname);

	$trackname = addslashes($trackname);
	$trackfname = addslashes($trackfname);
	$pathname = addslashes($pathname);
	$sql = "insert into track (trackname,root_dir,artist,album,filename,pathname,trackno,format)
		values ('$trackname','$root_id','$artist_id','$album_id','$trackfname','$pathname','$trackno','$format_id')";
	debug_string( "SQL2:", $sql);
	$result = do_mysql($link,$sql,$die=true);
}
?>

