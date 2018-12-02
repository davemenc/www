<?PHP
/* $Id: index.php,v 1.18 2005/11/29 20:26:03 dave Exp $ */
//================================================================
//====================== Includes ================================
//================================================================
include_once "../library/debug.php";
//debug_on();
include_once "../library/mysql.php";
include_once "../library/miscfunc.php";
include_once "../library/htmlfuncs.php";
include_once "../library/loc_login.php";
include_once "config.php";

//================================================================
//=================== Initialize Vars ============================
//================================================================
// define version information
$rcsversion = '$Id: index.php,v 1.18 2005/11/29 20:26:03 dave Exp $';
$version = FilterVersion($rcsversion,$version="");
debug_string("-------------------- PROGRAM START --------------------");
// parameters
$PARAMS = array_merge($HTTP_POST_VARS,$HTTP_GET_VARS);
if (isset($PARAMS['test'])){
}
debug_array("Params",$PARAMS);
if (!isset($PARAMS['mode'])) $mode = 'list';
else $mode = $PARAMS['mode'];
$link = make_mysql_connect($dbhost,$dbuser,$dbpass,$dbname);
//================================================================
//================= Main Controlling Switch ======================
//================================================================
//debug_string("mode",$mode);
switch($mode){
	case "newuser":
	debug_on();
		debug_string("newuser");
		$message = loc_parse_newuser($link,$PARAMS);
		debug_string("message",$message);
		if(""==$message){
			debug_string("login successful");
			ListCompanies();
		}else{
			debug_string("login unsuccessful");
			displayloclogin("Company List","#e0e0e0",$mode,$message,$PARAMS);
		}



		break;
	case "logout":
		loc_delete_cookie($appcookie);
		ListCompanies();
		break;
	case "detail":
		ShowDetail($PARAMS['company']);
		break;
	case "tips":
		ShowTips();
		break;
	case "showtypes":
		ShowTypes();
		break;
	case "parseedit":
		loc_GetAuthenticated($PARAMS['username'],$PARAMS['password'],$link,$appword,$appcookie,$admin=false,$appexpiry,$title="Application",$color='#e0e0e0',$mode);
		ParseEdit($PARAMS);
		ListCompanies();
		break;
	case "parseadd":
		loc_GetAuthenticated($PARAMS['username'],$PARAMS['password'],$link,$appword,$appcookie,$admin=false,$appexpiry,$title="Application",$color='#e0e0e0',$mode);
		ParseAdd($PARAMS);
		ListCompanies();
		break;
	case "add":
		loc_GetAuthenticated($PARAMS['username'],$PARAMS['password'],$link,$appword,$appcookie,$admin=false,$appexpiry,$title="Application",$color='#e0e0e0',$mode);
		AddCompany();
		break;
	case "edit":
		$PARAMS['mode']="login";
		$username = $PARAMS['username'];
		loc_GetAuthenticated($PARAMS,$link,$appword,$appcookie,$admin=false,$appexpiry,$title="Application",$color='#e0e0e0');
		EditCompany($PARAMS['company']);
		break;
	case "list":
	default:
		ListCompanies();
}

break_mysql_connect($link);
exit();
//================================================================
//========================= FUNCTIONS ============================
//================================================================
function ShowTips(){
	global $version,$lastmodified;
	Display_Generic_Header("Tips for using Company List","#eFefe0");
echo <<< EOF
<b><center>| <a href="index.php?mode=list">Company List</a> | <a href="mailto:colist@menconi.com">Contact Author</a> | <a href="index.php?mode=showtypes">Types List</a>|</center></b>

 <table> <tr><td width=8%>&nbsp;</td> <td>
 <h2>Company List Intro</h2>
 <b>By Dave Menconi</b>
 <h3>What is this? </h3>
 <P>This is a WIKI list of software companies in the Silicon Valley. It's WIKI in the sense that anyone can edit or add companies. The hope is that the list, which currently consists of about <a href="index.php?mode=list&Filter=0">170 companies</a> that have been resarched and <a href="index.php?mode=list&Filter=2">500 companies</a> that may or may not belong on the list, will get better and more comprehensive as time goes on because people will add and edit the information.
 <h3>Why are there so few companies? </h3>
 <P>Initially, I added about 500 companies that had jobs listings for software engineers in the Bay Area. Many of these aren't in the Bay Area themselves, were actually recruiting firms or for some other reason don't belong on the list. These are all listed as <a href="index.php?mode=showtypes">Raw</a> and are not shown in the general list. Check out the <a href="index.php?mode=list&Filter=2">unchecked company list</a>. <b>Please, look a few of these up and enter the information about them!</b> Only those that have been checked by someone actually show up on the list.
 <h3>What is the status of this site?  </h3>
 This site is running on my personal development server (which is RH9 Linux) using PHP & MYSQL. I've put about 15 hours of work into it and I'd characterize it as a decent alpha. It probably has a lot of bugs and other annoying things in it; please tell me about any that you find using the <a href="mailto:colist@menconi.com">Contact Author</a> link at the top of the page.
 <P>I'm reworking about 25 <a href="index.php?mode=list&Filter=2">raw company names </a> a day which means I'll have processed the 500 initial companies in about 3 weeks; if everyone who knows about this does just one a day we can be done in about 10 days!
 <h3>Do I have to pay? </h3>
 Nope. It's totally free. However, I really need help researching the "raw" data that's in the system. It will take me weeks to work through it all and, as the saying goes "Many hands make light work". <b>Please, contribute to the site!</b>
 <h3>Tips</h3>
 <ul>
 <li>The list contains a lot of entries that have not been checked; these have type "Raw".
 <li>To see the type Raw entries, pull down the "Filter by" menu ans select "Raw" and then click on "Display" to the right.
 <li>Please click on the company names to see and enter more information.
 <li>This site is free but please add and edit companies to make the list better.
 <li>To add a company, click on the "Add Company" link at the top of the list page.
 <li>To sort by a field, use the "Sort By" menu or click on the the "sort by" link at the top of the columns.
 <li>The system remembers the last listing parameters you entered.
 <li>If you don't like any of the types that are listed, select "Other" and input the type you think this company is. I'll review these "Other" entries and add the types to the list, as appropriate.

</ul>
</td> <td width=8%>&nbsp;</td></tr></table>
<!-- " -->
<!-- ' -->
EOF;
	//Display_Generic_Footer($version,$lastmodified);
	Display_Generic_Footer($version,date ("F d Y H:i:s", getlastmod()));
}
function ShowTypes(){
	global $version,$lastmodified;
	global $link;
	$types = MYSQLComplexSelect($link,array("*"),array("type"),array(),array("typeid desc"),0);
	$typelist="";
	for($i=0;$i<count($types);$i++){
		$typelist .= "<tr><td>".$types[$i]['typename']."</td><td>".$types[$i]['typedesc']."</td><tr>";
	}
	Display_Generic_Header("Display of Valid Types","#eFefe0");
echo <<< EOF
<b><center>| <a href="index.php?mode=list">Company List</a> | <a href="mailto:colist@menconi.com">Contact Author</a> | <a href="index.php?mode=tips">Tips</a> |</center></b>
<h2>Valid Types Of Companies</h2>
<table border=1>
<tr><td><b>Name</b></td><td><b>Description</b></td></tr>
$typelist
</table>

EOF;
	//Display_Generic_Footer($version,$lastmodified);
	Display_Generic_Footer($version,date ("F d Y H:i:s", getlastmod()));
}
function ParseAdd($PARAMS){
	global $link;
debug_array("PARAMS",$PARAMS);
	$id = $PARAMS['company'];
debug_array("id",$id);

	$sql = "insert  company (name,url,typeno,othername,description,address,city,state,zip,mainphone,note,email) values ('".$PARAMS['name']."','".$PARAMS['url']."','".$PARAMS['typeno']."','".$PARAMS['othername']."','".$PARAMS['description']."','".$PARAMS['address']."','".$PARAMS['city']."','".$PARAMS['state']."','".$PARAMS['zip']."','".$PARAMS['mainphone']."','".$PARAMS['note']."','".$PARAMS['email']."')";
	debug_string("sql",$sql);
  $result = mysql_query($sql,$link) or die(mysql_error());
  return $id;
}
function AddCompany(){
	global $version,$modified,$link;

//get the types
	$types = MYSQLComplexSelect($link,array("*"),array("type"),array(),array("typeid desc"),0);

// option to let user select type
	$typeoption = "<tr><td><b>Type:</b> </td> <td><select name=\"typeno\">";
	for ($i=0;$i<count($types);$i++){
		$optionname = $types[$i]['typename'];
		$optionvalue = $types[$i]['typeid'];
		$typeoption .= "<option value=\"".$optionvalue."\"";
		if ($optionname == "Raw") $typeoption .= " selected ";
		$typeoption .= ">".$optionname;
	}
	$typeoption .= "</select> </td>";

	Display_Generic_Header("Software Company Add","#eFfff0");
echo <<< EOF
<b><center>| <a href="index.php?mode=list">Company List</a> | <a href="mailto:colist@menconi.com">Contact Author</a> |</center></b>
<b> <h2>Add Company </h2><br>

 <FORM action="index.php" method="post">
 <input type="hidden" name="mode" value="parseadd">
 <input type="submit" value="Add">
 <table border=0>
 <tr><td><b>Name</b></td><td> <input type="text" name="name" size=50></td></tr>
 $typeoption
 <td><b>Other Type</b></td><td> <input type="text" name="othername" size=20></td></tr>
 <tr><td><b>URL</b></td><td> <input type="text" name="url" size=50></td></tr>
 <tr><td><b>Description</b></td><td> <input type="text" name="description" size=50></td></tr>
 <tr><td><b>Address</b></td><td> <input type="text" name="address" size=50></td></tr>
 <tr><td><b>City</b></td><td> <input type="text" name="city" size=50></td></tr>
 <tr><td><b>State</b></td><td> <input type="text" name="state" size=50></td></tr>
 <tr><td><b>Zip</b></td><td> <input type="text" name="zip" size=50></td></tr>
 <tr><td><b>Email</b></td><td> <input type="text" name="email" size=50></td></tr>
 <tr><td><b>Main Phone</b></td><td> <input type="text" name="mainphone" size=50></td></tr>

 </table>
 <table><tr><td valign=top><b>Note&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td> <textarea name="note" rows="8" cols="80"></textarea></td></tr>
 </table>
 <input type="submit" value="Add">
 </form>
EOF;
	//Display_Generic_Footer($version,$lastmodified);
	Display_Generic_Footer($version,date ("F d Y H:i:s", getlastmod()));
}
function ParseEdit($PARAMS){
	global $link;
//debug_array("PARAMS",$PARAMS);
	$id = $PARAMS['company'];
//debug_array("id",$id);
	$sql = "update  company set  name='".$PARAMS['name']."',url='".$PARAMS['url']."',typeno='".$PARAMS['typeno']."',othername='".$PARAMS['othername']."',description='".$PARAMS['description']."',address='".$PARAMS['address']."',city='".$PARAMS['city']."',state='".$PARAMS['state']."',zip='".$PARAMS['zip']."',mainphone='".$PARAMS['mainphone']."',email='".$PARAMS['email']."',note='".$PARAMS['note']."'where id='".$id."' limit 1";
//	debug_string("sql",$sql);
  $result = mysql_query($sql,$link) or die(mysql_error());
  return $id;
}
function ShowDetail($company){
	global $link,$version,$modified;
	//debug_string('version',$version);
	//debug_string('modified',$modified);
	//function MYSQLComplexSelect(&$link,$fieldnames=array("*"),$tablenames=array(),$where=array(),$order=array(),$debug=0){

	if(!isset($company) || $company<1) {// bad value; nice try; list the companies and bail
		ListCompanies();
		break_mysql_connect($link);
		exit();
	}
	$filterclause = array("id='".$company."'","typeno=typeid");
	$companies = MYSQLComplexSelect($link, array("*"),array("company","type"),$filterclause,array(),0);
	//debug_array("companies",$companies);
	$company = $companies[0];
// collect values from Database
	$name = $company['name'];
	$id = $company['id'];
	$url = $company['url'];
	$othername = $company['othername'];
	$description = $company['description'];
	$address = $company['address'];
	$city = $company['city'];
	$state = $company['state'];
	$zip = $company['zip'];
	$mainphone = $company['mainphone'];
	$email = $company['email'];
	$note = $company['note'];
	$typename = $company['typename'];

//get the types
	$types = MYSQLComplexSelect($link,array("*"),array("type"),array(),array("typeid desc"),0);

// option to let user select type
	$typeoption = "<tr><td><b>Type:</b> </td> <td><select name=\"typeno\"><option value=\"0\">All";
	for ($i=0;$i<count($types);$i++){
		$optionname = $types[$i]['typename'];
		$optionvalue = $types[$i]['typeid'];
		$selected = "";
		if ($optionvalue==$company['typeno']) $selected ="selected";
		$typeoption .= "<option $selected value=\"".$optionvalue."\">".$optionname;
	}
	$typeoption .= "</select> &nbsp;&nbsp; <i>(was ".$company['typename'].")</i></td>";

	Display_Generic_Header("Display Company Detail","#FFffe0");
echo <<< EOF
<b><center>| <a href="index.php?mode=edit&company=$id">Edit Company</a> | <a href="index.php">List Companies</a> | <a href="mailto:colist@menconi.com">Contact Author</a> | <a href="index.php?mode=showtypes">Types List</a> | <a href="index.php?mode=tips">Tips</a> | <a href="index.php?mode=logout">Logout</a> |</center></b>
<center><h2>Display Company Detail: <u>$name</u></h2></center>
<center>
 <table border=0>
 <tr><td>Name</b></td><td> <b>$name</td></tr>
 <tr><td>Type</b></td><td><b>$typename</td></tr>
 <tr><td>Other</b></td><td> <b>$othername</td></tr>
 <tr><td>URL</b></td><td> <b>$url</td></tr>
 <tr><td>Description</b></td><td> <b>$description</td></tr>
 <tr><td>Address</b></td><td> <b>$address</td></tr>
 <tr><td>City</b></td><td> <b>$city</td></tr>
 <tr><td>State</b></td><td><b>$state</td></tr>
 <tr><td>Zip</b></td><td> <b>$zip</td></tr>
 <tr><td>Email</b></td><td> <b>$email</td></tr>
 <tr><td>Main Phone&nbsp;&nbsp;</b></td><td> <b>$mainphone</td></tr>
 <tr><td>Note</b></td><td><b>$note</td></tr>
 </table>
 <table>
 </table>
 </center>
EOF;
	//Display_Generic_Footer($version,$lastmodified);
	Display_Generic_Footer($version,date ("F d Y H:i:s", getlastmod()));
}
function EditCompany($company){
	global $link,$version,$modified;
	//debug_string('version',$version);
	//debug_string('modified',$modified);
	//function MYSQLComplexSelect(&$link,$fieldnames=array("*"),$tablenames=array(),$where=array(),$order=array(),$debug=0){

	if(!isset($company) || $company<1) {// bad value; nice try; list the companies and bail
		ListCompanies();
		break_mysql_connect($link);
		exit();
	}
	$filterclause = array("id='".$company."'","typeno=typeid");
	$companies = MYSQLComplexSelect($link, array("*"),array("company","type"),$filterclause,array(),0);
	//debug_array("companies",$companies);
	$company = $companies[0];
// collect values from Database
	$name = $company['name'];
	$id = $company['id'];
	$url = $company['url'];
	$othername = $company['othername'];
	$description = $company['description'];
	$address = $company['address'];
	$city = $company['city'];
	$state = $company['state'];
	$zip = $company['zip'];
	$mainphone = $company['mainphone'];
	$email = $company['email'];
	$note = $company['note'];

//get the types
	$types = MYSQLComplexSelect($link,array("*"),array("type"),array(),array("typeid desc"),0);

// option to let user select type
	$typeoption = "<tr><td><b>Type:</b> </td> <td><select name=\"typeno\"><option value=\"0\">All";
	for ($i=0;$i<count($types);$i++){
		$optionname = $types[$i]['typename'];
		$optionvalue = $types[$i]['typeid'];
		$selected = "";
		if ($optionvalue==$company['typeno']) $selected ="selected";
		$typeoption .= "<option $selected value=\"".$optionvalue."\">".$optionname;
	}
	$typeoption .= "</select> &nbsp;&nbsp; <i>(was ".$company['typename'].")</i></td>";

	Display_Generic_Header("Software Company Edit","#FFffe0");
echo <<< EOF
<b><center>| <a href="index.php">List Companies</a> | <a href="mailto:colist@menconi.com">Contact Author</a> | <a href="index.php?mode=showtypes">Types List</a> | <a href="index.php?mode=tips">Tips</a> |</center></b>
<center><h2>Edit Company <u>$name</u></h2></center>

 <FORM action="index.php" method="post">
 <input type="hidden" name="mode" value="parseedit">
 <input type="hidden" name="company" value="$id">
 <input type="submit" value="Submit">
 <table border=0>
 <tr><td><b>Name</b></td><td> <input type="text" name="name" value="$name" size=50></td></tr>
 $typeoption
 <td><b>Other Type</b></td><td> <input type="text" name="othername" value="$othername" size=50></td></tr>
 <tr><td><b>URL</b></td><td> <input type="text" name="url" value="$url" size=50></td></tr>
 <tr><td><b>Description</b></td><td> <input type="text" name="description" value="$description" size=50></td></tr>
 <tr><td><b>Address</b></td><td> <input type="text" name="address" value="$address" size=50></td></tr>
 <tr><td><b>City</b></td><td> <input type="text" name="city" value="$city" size=50></td></tr>
 <tr><td><b>State</b></td><td> <input type="text" name="state" value="$state" size=50></td></tr>
 <tr><td><b>Zip</b></td><td> <input type="text" name="zip" value="$zip" size=50></td></tr>
 <tr><td><b>Email</b></td><td> <input type="text" name="email" value="$email" size=50></td></tr>
 <tr><td><b>Main Phone</b></td><td> <input type="text" name="mainphone" value="$mainphone" size=50></td></tr>

 </table>
 <table><tr><td valign=top><b>Note&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td><td> <textarea name="note" rows="8" cols="80">$note</textarea></td></tr>
 </table>
 <input type="submit" value="Submit">
 </form>
EOF;
	Display_Generic_Footer($version,date ("F d Y H:i:s", getlastmod()));
	//Display_Generic_Footer($version,$lastmodified);
}
function ListCompanies(){
	global $typeno,$filterfield,$version,$modified,$sortfield,$link, $note,$PARAMS,$fieldinfo,$PageLengths;
	debug_string("ListCompanies()");
	//================= Define Some Constant Tables ======================
	// define table headings and field names
	$fieldinfo[] = array("title"=>"Name","field"=>"name");
	$fieldinfo[] = array("title"=>"Type","field"=>"typename");
	$fieldinfo[] = array("title"=>"City","field"=>"city");
	$fieldinfo[] = array("title"=>"Zip","field"=>"zip");
	$fieldinfo[] = array("title"=>"Description","field"=>"description");
	$PageLengths = array(25,50,75,100,200,300);
	$listkeywords = array("First","Page","Sort","Filter");
	$listdefaults = array("0",    "25",  "0",   "0");
	//================= get the basic list parameters =======================
	for($i=0;$i<count($listkeywords);$i++){
		$keyword = $listkeywords[$i];
		if(isset($PARAMS[$keyword])) {
			$listparams[$keyword]=$PARAMS[$keyword];
		}
		else if(isset($_COOKIE['CoListParams'])){
			$listparams[$keyword]=$_COOKIE['CoListParams'][$i];
		}else{
			$listparams[$keyword]=$listdefaults[$i];
		}
		setcookie('CoListParams['.$i.']',$listparams[$keyword],0);
	}
		//debug_string("sort result",$listparams['Sort']);

	// get the list of types

	//================= Get Data From Database =======================
	//debug_string("typeno",$typeno);
	$typeno=$listparams['Filter'];
	$sortfield = $fieldinfo[$listparams['Sort']]['field'];
	//$sortfield = $fieldinfo[$PARAMS['Sort']]['field'];
	//debug_string("sortfield",$sortfield);

	if ($typeno!=0) $filterclause[] = "typeno='".$typeno."'";
	else {
		$filterclause[]="typename!='Hide'";
		$filterclause[]="typename!='Recruiter'";
		$filterclause[]="typename!='Dup'";
		$filterclause[]="typename!='Raw'";
	}
	$filterclause[] = "typeno=typeid";
	//debug_array("filterclause",$filterclause);
//debug_string("sortfield",$sortfield	);
	if (!isset($sortfield)) $sortclause = "name";
	else $sortclause = $sortfield ;//. " limit 20";
	//debug_string("sortclause",$sortclause);

	$companies = MYSQLComplexSelect($link, array("*"),array("company","type"),$filterclause,array($sortclause),0);
	$companycount = count($companies)-1;
	//debug_string("companycount",$companycount);

	$alphamenu = alphalist($companies);
	//================= Calculate paging information ======================
	// $end -- we are at the end of the file, there is no next
	// $start -- we are at the start of the file, there is no start
	// $pagelen -- how many line show on a page
	// $firstline -- the first line to show on the page
	// $lastline -- the last line to show on the page
	// $prevline -- if the user clicks on previous, what is the first line going to be?
	// $nextline -- if the user clicks on next what is the first line going to be?
	// $pagelist -- array of line starts and ends for page menu
	// $pagemenu -- actual page menu in HTML
	// $startline -- first line of the firt page
	// $endline -- first line of thelast page

	$firstline = $listparams['First'];
	$pagelen = $listparams['Page'];
	$end = false;
	$start = false;
	if ($pagelen<=25) $pagelen=25;
	if ($firstline<0) $firstline=0;
	if ($pagelen>$companycount) $pagelen=$companycount;
	$lastline = $firstline+$pagelen-1;
	if ($lastline>$companycount) $lastline=$companycount;
	if ($lastline >= $companycount-$pagelen)$end=true;
	if ($firstline == 0) $start = true;

	debug_string("pagelen",$pagelen);
	debug_string("firstline",$firstline);
	debug_string("lastline",$lastline)+1;
//if($start)debug_string("start=true");
//if($end)debug_string("end=true");

	$prevline = $firstline-$pagelen;
	if ($prevline<0) $prevline=0;
	$nextline = $firstline+$pagelen;
	if ($nextline>$companycount)$nextline=$companycount;
	$endline = $companycount-$pagelen+1;
	$startline = 0;

	$previousparams = "First=$prevline";
	$firstparams = "First=$startline";
	if (!$start){
		$prevhtml = "<a href=\"index.php?".$previousparams."\">Previous</a>";
		$firsthtml = "<a href=\"index.php?".$firstparams."\">First</a>";
	} else {
		$prevhtml = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$firsthtml = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	$nextparams = "First=$nextline";
	$lastparams = "First=$endline";
	if (!$end) {
		$nexthtml = "<a href=\"index.php?".$nextparams."\">Next</a>";
		$lasthtml = "<a href=\"index.php?".$lastparams."\">Last</a><br>";
	} else {
		$nexthtml = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		$lasthtml = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>";
	}

	// page menu calculations
	$totalpages = intval($companycount/$pagelen)+1;
	$p=1;
	//$pagemenu ="Pages: ";
	$pagemenu = "";
	//debug_string("firstline",$firstline);
	for($l=0;$l<$companycount;$l+=$pagelen){
		$pagelist[$p]['start'] = $l;
		$pagelist[$p]['end']= $l+$pagelen-1;
		if($firstline>=$l && $firstline<=$l+$pagelen-1){
			$currentpage=$p;
			$pagemenu .= " $p ";
		}else{
			$pagemenu .= " <a href=\"index.php?First=$l\">$p</a> ";
		}
		$p++;
	}
	//debug_string("currentpage",$currentpage);
	//debug_string("<hr>pagemenu",$pagemenu."<hr>");
	//debug_string("totalpages",$totalpages);
	//debug_string("p",$p);
	//debug_string("l",$l);
	//debug_array("pagelist",$pagelist);

//debug_string("firstline",$firstline);
//debug_string("lastline",$lastline);
//debug_string("prevline",$prevline);
//debug_string("nextline",$nextline);
//debug_string("startline",$startline);
//debug_string("endline",$endline);
	//debug_array("companies",$companies);
	//debug_string("company count",$companycount);

	$types = MYSQLComplexSelect($link,array("*"),array("type"),array(),array("typeid desc"),0);
	//debug_array("types",$types);
	//debug_string("type count",$typecount);

	//======================== Draw Screen ===========================
	Display_Generic_Header("Software Company List","#FFe0FF");
	//WriteBody($firstline,$lastline);

	$self=$_SERVER['PHP_SELF'];
//create filter pulldown menu
	$filteroption = "&nbsp;&nbsp;<b>Filter by:</b> <select name=\"Filter\"><option value=\"0\">All";
	for ($i=0;$i<count($types);$i++){
		$optionname = $types[$i]['typename'];
		$optionvalue = $types[$i]['typeid'];
		$filteroption .= "<option value=\"".$optionvalue."\">".$optionname;
	}
	$filteroption .= '</select>';
 $filteroption .= '<input type="hidden" name="First" value="0">';

//create sort pulldown menu
	//	debug_string("<hr>filteroption",$filteroption);
	$sortoption = "&nbsp;&nbsp;<b>Sort by:</b> <select name=\"Sort\">";
	for ($i=0;$i<count($fieldinfo);$i++){
		$sortoption .= "<option value=\"".$i."\">" . $fieldinfo[$i]['title'];
	}
	$sortoption .= '</select>';
	$headings = "";
	for ($i=0;$i<count($fieldinfo);$i++){
		$headings .= "<td><center><b><a href=\"index.php?Sort=" . $i . "\">" . $fieldinfo[$i]['title'] . "</b></font></a></td>";
	}
//create page length pulldown menu
	$pageoption =  "&nbsp;&nbsp;<b>Page Size:</b> <select name=\"Page\">";
	for ($i=0;$i<count($PageLengths);$i++){
		$pageoption .= "<option value=\"" . $PageLengths[$i] . "\">" . $PageLengths[$i];
	}
	$pageoption .= '</select>';

	// create company list html
	$colist = "";
	for($i=$firstline;$i<=$lastline;$i++){
		// modify the company information before we try to output it (hey, that's cheating!)
		if (isset($companies[$i]['city'])&&isset($companies[$i]['state'])){
			$companies[$i]['city'] = $companies[$i]['city'] . ", " . $companies[$i]['state'];
		}else{
			$companies[$i]['city'] = $companies[$i]['city'] . $companies[$i]['state'] . "&nbsp";
		}
		if ($companies[$i]['typename']=="Other"){
			$companies[$i]['typename'] = $companies[$i]['othername'];
		}
		$companies[$i]['rawname'] = $companies[$i]['name'];
		$companies[$i]['name'] = "</center><a href=\"index.php?mode=detail&company=" . $companies[$i]['id'] . "\">" . $companies[$i]['name']. "</a> <a href=\"index.php?mode=edit&company=" . $companies[$i]['id'] . "\"><font size=-2><i>Edit</font></i></a>";
		if (isset($companies[$i]['description'])){
			$companies[$i]['description'] = substr($companies[$i]['description'],0,60);
		} else {
			$companies[$i]['description'] = "&nbsp";
		}
		// now step through the fields and output information
		$colist .= "<tr>\n";
		for($j=0;$j<count($fieldinfo);$j++){
			if(!isset( $companies[$i][$fieldinfo[$j]['field']])){
				$companies[$i][$fieldinfo[$j]['field']]="&nbsp;";
			}
			$colist .= "\t<td>" . $companies[$i][$fieldinfo[$j]['field']] . "</td>\n";
		}
		if($companies[$i]['typename']=="Raw"){
			$colist .= "\t<td><a href=\"http://clusty.com/search?tb=firefox-1.0.3&query=".$companies[$i]['rawname']."\"><font size=-2><i>Web Search</i></font></a></td>";
		}
		$colist .= "</tr>\n\n";
	}
	//debug_string("<hr>",$colist);
	//debug_string("<hr>");
echo <<< EOF
<b><center>| <a href="index.php?mode=add">Add Company</a> | <a href="mailto:colist@menconi.com">Contact Author</a> | <a href="index.php?mode=showtypes">Types List</a> | <a href="index.php?mode=tips">Tips</a> | <a href="index.php?mode=logout">Logout</a> |</center></b>

 <center><h1>Software Company List</h1></center>
<center>
<center><table><tr><td width=10%>&nbsp;</td>
<td>
<P>This is a list of companies who employ software engineers.
Click on the company name to see detailed information; click on the "Edit" link to edit that information. This is a Wiki site: anyone can edit information if they believe it to be incorrect or incomplete.
</td>
<td width=10%>&nbsp;</td>
</tr></table></center>
<center><h2>Display Settings</h2></center>

<center> <FORM action="index.php" method="get">
 <input type="hidden" name="mode" value="list">
$sortoption
$filteroption
$pageoption
 <input type="submit" value="Display">
 </form>
</center>
<center><font size=+2><b>Company List </b></font></center>
<center><font size=+1> <i>($companycount lines)</i></font></h2></center>
$alphamenu<br>
$firsthtml &nbsp; &nbsp; &nbsp; &nbsp; $prevhtml &nbsp; &nbsp; $pagemenu &nbsp; &nbsp; $nexthtml&nbsp; &nbsp; &nbsp; &nbsp; $lasthtml

<center><table border=1>
	<tr>
		$headings
	</tr>
	$colist

</table>
</center>
$firsthtml &nbsp; &nbsp; &nbsp; &nbsp; $prevhtml &nbsp; &nbsp; &nbsp; &nbsp; $nexthtml&nbsp; &nbsp; &nbsp; &nbsp; $lasthtml
<br><br>
EOF;
	//Display_Generic_Footer($version,$lastmodified);
	Display_Generic_Footer($version,date ("F d Y H:i:s", getlastmod()));
}
function Change_Request($request, $key,$value){
	//debug_string ("request",$request);
	if (!isset($request) ||!isset($key)||!isset($value)) return $request;
	$reqar = explode("&",$request);
	foreach($reqar as $reqel){
		$ar=explode("=",$reqel);
		$newreq[$ar[0]]=$ar[1];
	}
	if(isset($newreq[$key])) unset($newreq[$key]);
	$newreq[$key]=$value;
	$first=true;
	$result ="";
	foreach ($newreq as $key=>$value){
		if ($first) $first=false;
		else $result .= "&";
		$result .= $key."=".$value;
	}
	return $result;
}
function alphalist($companies){
	$alpha=array();
	for ($i=count($companies)-1;$i>=0;$i--){
		$name=ucfirst($companies[$i]['name']);

		$initial = substr($name,0,1);
		$alpha[$initial] = $i;
	}
	asort($alpha);

	$alphamenu = "";
	foreach ($alpha as $initial=>$count){
		$alphamenu .= "<a href=\"index.php?First=$count\">$initial</a> ";
	}
	return($alphamenu);
}
function localauthentication(){
}
function checkauthcookie(){
}
function setauthcookie(){
}
function updateauthcookie(){
}
function displaylogin($mode,$message=""){
	global $version,$lastmodified;
	Display_Generic_Header("Software Company List Login","#e0ffeo");
echo <<< EOF
	<center>| <a href="index.php?mode=join">Register</a> | <a href="index.php?mode=list">Return to Company List </a> |</center>
	<br><br>
	<br><br>
<font color=red size=+1>$message</font>
	<br><br>

	<h2>Enter Username & Password</h2>
	<h3></h3>
<form method="post" action="index.php">
<table><tr><td><b>Username: </b></td> <td><input type="text" name="username"><br></td></tr>
<tr><td><b>Password: </b></td><td>	<input type="password" name="password"><br></td></tr>
</table>	<input type="submit"  value="Submit"><br>
	<input type="hidden" name="mode" value="$mode">
</form>
EOF;
	//Display_Generic_Footer($version,$lastmodified);
	Display_Generic_Footer($version,date ("F d Y H:i:s", getlastmod()));
}
/*
 * $Log: index.php,v $
 * Revision 1.18  2005/11/29 20:26:03  dave
 * final changes
 *
 * Revision 1.16  2005/11/22 03:27:26  dave
 * check point
 *
 * Revision 1.15  2005/11/08 08:04:02  dave
 * fixed bug where it doesn't show last line
 *
 * Revision 1.14  2005/11/08 07:59:58  dave
 * added the alpha menu to list
 * added "selected" to raw in the add company type select so that raw would be the default for added companies
 *
 * Revision 1.13  2005/11/02 01:20:32  dave
 * changed the text message on the listing page
 * changed some of the headers and menus on the listing page -- nothing functional, just arrangement
 *
 * Revision 1.12  2005/10/28 23:56:17  dave
 * fixed a bug in the edit company link in the detail page
 * added a link to the raw list that does a search on clusty!
 *
 * Revision 1.11  2005/10/28 20:43:48  dave
 * added detail page and links to it
 *
 * Revision 1.10  2005/10/28 20:20:07  dave
 * added links to edit page to return to list page to fix bug 393
 *
 * Revision 1.9  2005/10/27 17:36:24  dave
 * hiding the recruiters, too
 *
 * Revision 1.8  2005/10/27 16:59:44  dave
 * added email field to database
 * added to company add
 * company edit
 * display
 *
 * Revision 1.7  2005/10/25 21:34:34  dave
 * fixed some small things
 * now use cookies to store list options
 *
 * Revision 1.6  2005/10/25 06:26:21  dave
 * spiffing entry screens
 *
 * Revision 1.5  2005/10/25 05:38:26  dave
 * all the basic functionality is there
 *
 * Revision 1.4  2005/10/24 17:12:45  dave
 * added add company functionality
 * added menu to top of page
 * added screen to show types
 * added mailto
 *
 * Revision 1.3  2005/10/24 08:14:15  dave
 * got edit nominally working
 *
 * Revision 1.2  2005/10/24 07:03:42  dave
 * added edit page
 *
 * Revision 1.1  2005/10/24 02:54:51  dave
 * Initial revision
 *
 */
?>
