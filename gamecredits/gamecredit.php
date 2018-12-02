<?php
    include_once('config.php');
    include_once('header.php');
    include_once('../library/debug.php');
    include_once "../library/mysql.php";
    include_once('footer.php');
    include_once('../library/filtersql.php');
log_on();
debug_string("--------------------------------- START GAMECREDIT PROGAM _-------");
$PARAMS = FilterSQL(array_merge($_POST,$_GET));
// Create link to DB
$link = make_mysql_connect($dbhost, $dbuser, $dbpass, $dbname);
//log in user

//debug_on();

unset($PARAMS['password']);
debug_array("params",$PARAMS);
debug_string ("login succeeded");

    // set the mode
    if (isset($PARAMS['mode'])){$mode=$PARAMS['mode'];}
    else $mode = "showform";

    debug_string("mode",$mode);
    switch($mode){
        case "showform":
        	debug_string("mode=$mode");
      		display_credit_form();
			break;
        default:
            debug_string("mode=$mode");
    }
break_mysql_connect($link);
exit();
/***********************************************
 * display_credit_form
 ***********************************************/
function display_credit_form(){
	global $link;
debug_string("display_credit_form()");

// GET REPORTER NAME SELECT
$sql = "select * from reporters  order by name";
$reporters = MYSQLGetData($link,$sql);
$nameselect ="\n\t\t<div>\n\t\t\t<label for=\"team_member\">Your Name</label>\n\t\t\t<select id=\"team_member\" name=\"team_member\">\n\t\t\t\t<option value=\"\">Select</option>\n";
foreach($reporters as $reporter){
	$nameselect .= "\t\t\t\t<option value=\"".$reporter['id']."\">".$reporter['name']."</option>\n";
}
$nameselect .="\t\t\t\t<option value=\"-1\">My Name's Not On The List</option>\t\t\t</select>\t\t</div>\n";

// NOW DO THE BODY OF THE FORM INTO A STRING
// Get Projects
$sql = "select * from projects order by name";
$projects = MYSQLGetData($link,$sql);

// Get games
$sql = "select * from games  order by name";
$games = MYSQLGetData($link,$sql);

$body="";

for($i=0;$i<5;$i++){
		$body.="\t\t\t\t<tr";
		if($i%2==0)$body.=' class="alt"';
		$body.=">\n\t\t\t\t\t<td>\n\t\t\t\t\t\t<select id=\"game_$i\" name=\"game\" >\n\t\t\t\t\t\t\t<option value=\"\">Select</option>\n";
	foreach($games as $game){
		$body.="\t\t\t\t\t\t\t<option value=\"".$game['id']."\">".$game['name']."</option>\n";
	}
	$body.= "\t\t\t\t\t\t</select>\n\t\t\t\t\t</td>\n\t\t\t\t<td>\n";
	$body.= "\t\t\t\t\t\t<select name=\"tnt_project\" id=\"tnt_project_$i\">\n";
	$body.= "\t\t\t\t\t\t\t<option value=\"\">Select</option>\n";
	foreach($projects as $project){
		$body.="\t\t\t\t\t\t\t".'<option value="'.$project['id'].'">'.$project['name'].'</option>\n';
	}
	$body.="\t\t\t\t\t\t";

$body.=<<<EOF
						</select>
					</td>
					<td>
						<select name="year" id="year_$i">
							<option value="">Select</option>
							<option value="2004">2004</option>
							<option value="2005">2005</option>
							<option value="2006">2006</option>
							<option value="2007">2007</option>
							<option value="2008">2008</option>
							<option value="2009">2009</option>
							<option value="2010">2010</option>
							<option value="2011">2011</option>
							<option value="2012">2012</option>
							<option value="2013">2013</option>
						</select>
					</td>
					<td>
 <input type="text" name="evidence" size=80 />
</td>
				</tr>
EOF;
}

//print_r($reporters);
//print_r($projects);
//print_r($games);

print display_header("Game Credit Form", "form.css");
print "<fieldset>\n";
print "<form method=\"POST\" action=\"index.php\"> \n";
print $nameselect;
print "Your Name (if not on the list): <input type=\"text\" name=\"firstname\" />\n";
print "\t\t<table>\n\t\t\t<thead>\n\t\t\t\t<tr>\n\t\t\t\t\t<th>Game</th>\n\t\t\t\t\t<th>TNT Project</th>\n\t\t\t\t\t<th>Year</th>\n\t\t\t\t\t<th>Evidence (Why do you think this?)</th>\n\t\t\t\t</tr>\n\t\t\t</thead>\n\t\t<tbody>\n";
print $body;
echo<<<EOF2
			</tbody>
		</table>
</br> <b>ADD TO LISTS: &nbsp;&nbsp;&nbsp;</b>Add Game: <input type="text" name="newgame"/>&nbsp;&nbsp;&nbsp;Add Project: <input type="text" name="newproject"/>
		<div class="formActions">
			<input type="submit" value="Submit" />
		</div>
	</fieldset>
EOF2;

print display_footer();
}
 // Create, read, update and delete

/***************************************
 * display_list()
 * Displays a list of data from a teble such that it can be edited
 * INPUT:
 *	$link -- open link to database table is in
 *	$table -- table that the data is in -- one simple flat table
 *	$key -- name of field that contains the unary index (there needs to be one for this to work)
 *	$fields -- these are teh fields in the table that you want to edit; if this is blank we just use all the fields
 *	$titles -- if you don't want us to use the names of the fields in the database as labels, this is the chance to give us a different set
 * NOTE for $fields and $titles these are simple arrays with a numeric index and they need to match up.
 * $create,$read,$update,$delete -- URLS to each of these functions
 * OUTPUT: It's going to display a table with this information in it.
 * RE
 ***************************************/
function display_list($link, $table, $key, $create,$read,$update,$delete, $fields="", $headers=""){
	//convert $fields from array to string

	$sql = "select * from $table";
	$data = MYSQLGetData($link,$sql);

	print "<table border=\"1\">";

}
?>