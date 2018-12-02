<?PHP
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
/************************************************************
 * Tuna is a program for managing and playing audio files in a simple way.
 * The goals are to be easy to use, easy to install and to support multiple user.
 * The initial reality falls far short of this but I believe it is a good foundation.
************************************************************/
//================================================================
//====================== Includes ================================
//================================================================
include_once "../library/debug.php";
include_once "config.php";
include_once("mysql.php");
include_once("miscfunc.php");
include_once("htmlfuncs.php");
//================================================================
//=================== Initialize Vars ============================
//================================================================
debug_off();
$version = ".1";
$PARAMS = array_merge($_POST ,$_GET);
debug_string("-------------------- PROGRAM START --------------------");
debug_array("PARAMS",$PARAMS);
if (!isset($PARAMS['mode'])) $mode = 'main';
else $mode = $PARAMS['mode'];
if (!isset($PARAMS['search'])) $search = "";
else $search = $PARAMS['search'];
debug_string("search",$search);
// OPEN DATABASE
$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);

//================================================================
//================= Main Controlling Switch ======================
//================================================================
debug_string("mode",$mode);
switch($mode){
	case "playlists":
		ListPlaylists();
		break;
	case "playplaylist":
		if (!isset($PARAMS['playlistno']) $playlistno = $PARAMS('playlistno');
		else {ListPlaylists(); exit();}
		Play_Playlist($playlistno);
	case "albums":
		ListAlbums($search);
		break;
	case "groups":
		ListGroups($search);
		break;
	case "tracks":
		$album="";
		$group="";
		if (isset($PARAMS['album'])) $album=$PARAMS['album'];
		if (isset($PARAMS['group'])) $group=$PARAMS['group'];
		ListTracks($album, $group);
		break;
	case "main":
	default:
		DisplayMain();

}
mysqli_close($link);
exit();
//================================================================
//========================= FUNCTIONS ============================
//================================================================
/**********************************
 * OneMysql
 *
 ***********************************/
function OneMysql($sql){
	global $link;
	$olddebug = debug_set(0);
	if (strlen($sql)>1){
		debug_string("1msql",$sql);
		$result = mysqli_query($link,$sql) or die(mysql_error());
	}
	debug_set($olddebug);
	return $result;
}
function AddIfNotExist($table,$name,$fields,$data){
	global $link;
	debug_string("AddIfNotExist($table,$name)");
	debug_array("fields",$fields);
	debug_array("data",$data);
	$sql = "select * from $table where name='$name'";
	debug_string("sql (add)",$sql);
	$result = mysqli_query($link,$sql) or die(mysql_error());
	if (mysqli_num_rows($result)> 0){
		$row = mysql_fetch_assoc($result);
		$id = $row['id'];
		debug_string("found $name",$id);
	} else {
		$sql = "insert into $table ($fields) values ($data)";
		debug_string("sql (add)",$sql);
		$result = mysqli_query($link,$sql) or die(mysql_error());
		$id = mysqli_insert_id ( );
	}

	debug_string("id",$id);
	return $id;
}
/**********************************
 * HTMLHEADER()
 *
 ***********************************/
function HTMLHeader($title=""){
$header = <<< EOF
<!DOCTYPE html>
<html lang="en">
<head>
<title>$title</title>
<meta name="description" content="Tuna is a web based music management and playing app."
<meta name="keywords" content="music managment tuna play audio mp3 itunes"
</head>\n<body>
<h1>$title</h1>

EOF;
	return $header;
}
/**********************************
 * HTMLFooter()
 *
 ***********************************/
function HTMLFooter(){
	global $version;
$footer = <<< EOF

		<div class="footer">
			<center><hr width="80%"></center>
			<center><p><font size=-2>Version: $version</font></center>
			<center><p><font size=-2>This page (including all images) Copyright &copy; 2016  Dave Menconi.</font></center>
		</div>
	</body>
</html>

EOF;
	return $footer;
}


/**********************************
 * DisplayMain()
 *
 ***********************************/
function DisplayMain(){
	global $link;
	$olddebug=debug_set(0);
	debug_string("DisplayMain()");
	print HTMLHeader("Tuna Music Manager");
	$MY_URL = "http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
echo <<< EOF
<a href=$MY_URL?mode=playlists">Browse Playlists</a></br>
 <a href="$MY_URL?mode=groups">Browse Groups</a>
<form  action="$MY_URL" method="get">
SEARCH GROUPS: <input type="text" name="search">
 <input type="hidden" name="mode" value="groups">
<input type="submit" value="Submit">
</form>
 <a href="$MY_URL?mode=albums">Browse Albums</a>
<form action="$MY_URL" method="get">
 SEARCH ALBUMS: <input type="text" name="search">
 <input type="hidden" name="mode" value="albums">
<input type="submit" value="Submit">
</form>
EOF;
	print "</br></br></br></br></br></br></br></br></br></br></br>";
	print HTMLFooter();

	debug_set($olddebug);

}
/**********************************
 * ListAlbums()
 *
 ***********************************/
function ListAlbums($search=""){
	global $link;
	$MY_URL = "http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];

	$olddebug = debug_set(0);
	debug_string("ListAlbums($search)");

	if ($search == "") $whereclause = "";
	else $whereclause = "where name like '%$search%'";

	$sql = "select albums.id as albumid, albums.name as name, albums.year as year from albums ".$whereclause;
	debug_string("albumsql ",$sql);
	$result = OneMysql($sql);
	//============= DO THE PAGE ===============================
	print HTMLHeader("Tuna Music Manager Albums");
	print "<b>Click on the name to see tracks.</b></br>\n";
	print ('<table border="1" style="width:100%">'."\n");
	print ('<tr><th>Album</th><th>Year</th></tr>'."\n");
	while ($row=mysqli_fetch_assoc($result)){
		$albumid = $row['albumid'];
		print "<tr><td><a href=\"$MY_URL?mode=tracks&album=$albumid\">".$row['name'].'</a></td><td>'.$row['year'].'</td></tr>'."\n";
	}
	print "</table>";


	print HTMLFooter();
	debug_set($olddebug);
}
/**********************************
 * play_files()
 *
 ***********************************/
function play_files($filelist){
	$arg = "\"C:/Program Files (x86)/VideoLAN/VLC/vlc.exe\" ";
	foreach ($filelist as $file ){
		$arg .= "\"$file\" ";
	}
//	print $arg;
	exec($arg);
}
/**********************************
 * Play_Playlist()
 *
 ***********************************/
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
/**********************************
 * ListPlaylists()
 *
 ***********************************/
function ListPlaylists($search=""){
	global $link;
	$MY_URL = "http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];

	$olddebug = debug_set(0);
	debug_string("ListAlbums($search)");
/*		user int,
		playlistname tinytext,
		shared bool,
		sharedwith int default 0,
		*/
	$sql = "select * from playlist";
	debug_string("playlsitsql ",$sql);
	$playlists = MYSQLGetData($link,$sql);

	//============= DO THE PAGE ===============================
	print HTMLHeader("Tuna Music Manager Playlists");
	print "<b>Click on the name to see tracks.</b></br>\n";
	print ('<table border="1" style="width:100%">'."\n");
	print ('<tr><th>Playlist</th></tr>'."\n");
	foreach ($playlists as $playlist){
		$playlistno = $playlist['id'];
		$playlistname = $playlist['playlistname'];
		print "<tr><td><a href=\"$MY_URL?mode=playplaylist&playlistno=$playlistno\">".$playlistname.'</a></td></tr>'."\n";
	}
	print "</table>";


	print HTMLFooter();
	debug_set($olddebug);
}
/**********************************
 * ListGroups()
 *
 ***********************************/
function ListGroups($search=""){
	global $link;
	$MY_URL = "http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
	$olddebug = debug_set(0);
	debug_string("ListGroups($search)");
	if ($search == "") $whereclause = "";
	else $whereclause = "where name like '%$search%'";

	$sql = "select groups.id as groupid, groups.name as name from groups ".$whereclause;
	debug_string("groupsql ",$sql);
	$result = OneMysql($sql);

	//============= DO THE PAGE ===============================
	print HTMLHeader("Tuna Music Manager Groups");
	print "<b>Click on the name to see tracks.</b></br>\n";
	print ('<table border="1" style="width:100%">'."\n");
	print ('<tr><th>Group</th></tr>'."\n");
	while ($row=mysqli_fetch_assoc($result)){
		$groupid = $row['groupid'];
		print "<tr><td><a href=\"$MY_URL?mode=tracks&group=$groupid\">".$row['name'].'</a></td></tr>'."\n";
	}
	print "</table>";

	print HTMLFooter();
	debug_set($olddebug);
}
/**********************************
 * LISTTRACKS()
 *
 ***********************************/
function ListTracks($album="",$group=""){
	global $link;
	$olddebug = debug_set(0);
	debug_string("ListTracks($album,$group)");
	//================= Get Data From Database =======================
	if ($album == "" && $group ==""){
		$sql = "select tracks.name as trackname,tracks.trackno as trackno,tracks.url as trackurl,AudioTypes.name as type,albums.name as albumname, groups.name as groupname from tracks,audiotypes,albums,groups, GroupTrack,AlbumGroup,AlbumTrack where audiotypes.id=tracks.audiotypeno and albumtrack.trackno=tracks.id and albumtrack.albumno=albums.id and grouptrack.trackno=tracks.id and grouptrack.groupno=groups.id and albumgroup.albumno=albums.id and albumgroup.groupno=groups.id order by tracks.name limit 200";
	}
	else if ($album!=""){
		$sql = "select tracks.name as trackname,tracks.trackno as trackno,tracks.url as trackurl,AudioTypes.name as type,albums.name as albumname, groups.name as groupname from tracks,audiotypes,albums,groups, GroupTrack,AlbumGroup,AlbumTrack where audiotypes.id=tracks.audiotypeno and albumtrack.trackno=tracks.id and albumtrack.albumno=albums.id and grouptrack.trackno=tracks.id and grouptrack.groupno=groups.id and albumgroup.albumno=albums.id and albumgroup.groupno=groups.id and albums.id='$album' order by groups.name, albums.name,tracks.trackno,tracks.name";
	}
	else if ($group!=""){
		$sql = "select tracks.name as trackname,tracks.trackno as trackno,tracks.url as trackurl,AudioTypes.name as type,albums.name as albumname, groups.name as groupname from tracks,audiotypes,albums,groups, GroupTrack,AlbumGroup,AlbumTrack where audiotypes.id=tracks.audiotypeno and albumtrack.trackno=tracks.id and albumtrack.albumno=albums.id and grouptrack.trackno=tracks.id and grouptrack.groupno=groups.id and albumgroup.albumno=albums.id and albumgroup.groupno=groups.id and groups.id='$group' order by groups.name, albums.name,tracks.trackno,tracks.name ";
	}
	$result = OneMysql($sql);
	$htmldata = "";
	while ($row=mysqli_fetch_assoc($result)){
		$rowstring ='<tr><td>'.$row['type'].'</td><td>'.$row['groupname'].'</td><td>'.$row['albumname'].'</td><td>'.$row['trackno'].'</td><td><a href="'.$row['trackurl'].'" target="_blank">'.$row['trackname'].'</a></td></tr>'."\n";
		$htmldata.=$rowstring;
	}


	//============= DO THE PAGE ===============================
	print (HTMLHeader("Tuna Music Manager Tracks"));
	print ("<form>\n");
	print ('SEARCH: <input type="text" name="search">'."\n");
	print ('<input type="submit" value="Submit">'."\n");
	print ('</form>'."\n\n");

	print ('<table border="1" style="width:100%">'."\n");
	print ('<tr><th>Type</th><th>Group</th><th>Album</th><th>#</th><th>Track</th></tr>'."\n");

	print $htmldata;

	print('</table>'."\n");
	print HTMLFooter();
	debug_set($olddebug);
}