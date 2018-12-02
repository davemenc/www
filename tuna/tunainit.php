<?PHP
/************************************************************
 * Tuna is a program for managing and playing mp3 files in a simple way.
 * The goals are to be easy to use, easy to install and to support multiple user.
 * The initial reality falls far short of this but I believe it is a good foundation.
************************************************************/
//================================================================
//====================== Includes ================================
//================================================================
include_once "../library/debug.php";
include_once "../library/mysql.php";
//include_once "../library/miscfunc.php";
//include_once "../library/htmlfuncs.php";
//include_once "../library/loc_login.php";
include_once "config.php";
//================================================================
//=================== Initialize Vars ============================
//================================================================
debug_on();
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
$version = ".1";
debug_string("-------------------- PROGRAM START --------------------");
$link = mysql_connect($dbhost, $dbuser, $dbpass);
//================================================================
//================= Main Controlling Switch ======================
//================================================================

InitDB("database.txt","audiolist.txt");
break_mysql_connect($link);
exit();
//================================================================
//========================= FUNCTIONS ============================
//================================================================
/**********************************
 * InitDB()
 *
 ***********************************/
function OneMysql($sql){
	global $link;
	if (strlen($sql)>1){
		debug_string("1msql",$sql);
		$result = mysql_query($sql,$link) or die(mysql_error());
		debug_string("result",$result);
	}
	return $result;
}
function AddIfNotExist($table,$name,$fields,$data){
	global $link;
	debug_string("AddIfNotExist($table,$name)");
	debug_array("fields",$fields);
	debug_array("data",$data);
	$sql = "select * from $table where name='$name'";
	debug_string("sql (add)",$sql);
	$result = mysql_query($sql,$link) or die(mysql_error());
	if (mysql_num_rows($result)> 0){
		$row = mysql_fetch_assoc($result);
		$id = $row['id'];
		debug_string("found $name",$id);
	} else {
		$sql = "insert into $table ($fields) values ($data)";
		debug_string("sql (add)",$sql);
		$result = mysql_query($sql,$link) or die(mysql_error());
		$id = mysql_insert_id ( );
	}

	debug_string("id",$id);
	return $id;
}
function InitDB($databasefile,$datafile){
	global $link,$PARAMS;
	$dbin = fopen($databasefile,"r");
	if (!$dbin){
		debug_string("COULD NOT OPEN FILE $databasefile for reading!");
		return;
	}
	debug_off();

	while(($line = fgets($dbin,1000))!==false){
		//debug_string("line",$line);
		if (strlen($line)<10) continue;
		OneMysql($line);
	}
	fclose($dbin);

	// add test users
	OneMysql("insert into users (name,password,priv) values ('Admin','p00psi','admin'),('Dave','p00psi','user'),('Dorita','p00psi','user')");

	// now get actual data
	$datain = fopen($datafile,"r");
	if (!$datain){
		debug_string("COULD NOT OPEN FILE $datafile for reading!");
		exit();
	}
	while (($line = fgets($datain,1000)) !==false){
		debug_off();
		//echo $line;
		$fields = explode("\t",$line);
		if (count($fields)<6)continue; // skip short lines
		if ($fields[0]=="Type" && $fields[1]=="Group") continue; // skip the header line

		$track = str_replace("'","''",$fields[3]);
		$trackno = $fields[4];
		$url = str_replace("'","''",$fields[5]);
		$type = str_replace("'","''",$fields[0]);
		$group = str_replace("'","''",$fields[1]);
		$album = str_replace("'","''",$fields[2]);

		$groupid = AddIfNotExist('groups',$group,"name","'$group'");
		$typeid = AddIfNotExist('AudioTypes',$type,"name","'$type'");
		$result = OneMysql("insert into tracks (name,url,audiotypeno, trackno) values ('$track','$url','$typeid','$trackno')");
		$trackid = mysql_insert_id ( );
		$albumid = AddIfNotExist('albums',$album,"name","'$album'");

		debug_string("Track: $track; trackno = $trackid; groupno=$groupid; albumno=$albumid");
		// Add multi <-> multi records

		// GroupTrack
		$sql = "select * from GroupTrack where groupno = '$groupid' and trackno = '$trackid'";
		$result = OneMysql($sql);
		$count = mysql_num_rows($result);
		debug_string("Count",$count);
		if ($count<1){
			$sql = "insert into GroupTrack (groupno,trackno) values ($groupid,$trackid)";
			$result = OneMysql($sql);
		}

		//AlbumGroup
		$sql = "select * from AlbumGroup where groupno = '$groupid' and albumno = '$albumid'";
		$result = OneMysql($sql);
		$count = mysql_num_rows($result);
		debug_string("Count",$count);
		if ($count<1){
			$sql = "insert into AlbumGroup (groupno,albumno) values ($groupid,$albumid)";
			$result = OneMysql($sql);
		}

		//AlbumTrack
		$sql = "select * from AlbumTrack where trackno = '$trackid' and albumno = '$albumid'";
		$result = OneMysql($sql);
		$count = mysql_num_rows($result);
		debug_string("Count",$count);
		if ($count<1){
			$sql = "insert into AlbumTrack (trackno,albumno) values ($trackid,$albumid)";
			$result = OneMysql($sql);
		}
		//debug_on();
		$sql = "select tracks.name as trackname,tracks.url as trackurl,AudioTypes.name as type,albums.name as albumname, groups.name as groupname
				from tracks,audiotypes,albums,groups, GroupTrack,AlbumGroup,AlbumTrack
				where tracks.id=$trackid
				and audiotypes.id=tracks.audiotypeno
				and albumtrack.trackno=tracks.id
				and albumtrack.albumno=albums.id
				and grouptrack.trackno=tracks.id
				and grouptrack.groupno=groups.id
				and albumgroup.albumno=albums.id
				and albumgroup.groupno=groups.id";
		$result = OneMysql($sql);
		$count = mysql_num_rows($result);
		if ($count!=1){
			debug_string("Problem: bad count for $trackid",$count);
		}else{
			$row = mysql_fetch_assoc($result);
			debug_array("Test Row $trackid",$row);
		}
	}
	fclose($datain);


}
