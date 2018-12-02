<?php
 /*
    Copyright (c) 2013 Dave Menconi

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
//include_once("mysql.php");
/******************* TEST CODE **********************/
include_once("../library/mysql.php");
include_once("config.php");


$regex=setRegex();

$workers = CheckArray(setWorkers(),'workers',false);
$companies = CheckArray(setCompanies(),'companies',false);
//$invoices = CheckArray(setInvoices(),'invoices',false);
//$invoicelines = CheckArray(setInvoicelines(),'invoice',false);
$PARAMS = array_merge($_POST,$_GET);
$link = make_mysql_connect($dbhost, $dbuser, $dbpass, $dbname);


   // set the mode

 	$mode = "showmenu";
 	$action="";
 	$table="";
 	$index=0;

 	if(isset($PARAMS['action'])) $action=$PARAMS['action'];
 	if(isset($PARAMS['table'])) $table=$PARAMS['table'];
	if (isset($PARAMS['index']))$index=$PARAMS['index'];


	if($action!="" && $table!="") $mode=$action.$table;

/*
$actions = array("create","read","update","delete","parse","list");
$tables = array("companies","workers","invoices","invoicelines");
foreach($actions as $action)
	foreach($tables as $table)
		print $action.$table."<br/>\n";
*/

	switch($mode){
    	case "showmenu";
    		ShowMenu();
    		break;
		case "createcompanies":
			DBShowCreateForm($companies);
			break;

		case "createworkers":
			DBShowCreateForm($workers);
			break;

		case "createinvoices":
			DBShowCreateForm($invoices);
			break;

		case "createinvoicelines":
			DBShowCreateForm($invoicelines);
			break;

		case "readcompanies":
			DBShowReadView($companies,$index);
			break;

		case "readworkers":
		print "<LI>index=$index".__LINE__."</li>\n";
			DBShowReadView($workers,$index);
			break;

		case "readinvoices":
			DBShowReadView($invoices,$index);
			break;

		case "readinvoicelines":
			DBShowReadView($invoicelines,$index);
			break;

		case "updatecompanies":
			DBShowEditForm($companies,$index);
			break;

		case "updateworkers":
			DBShowEditForm($workers,$index);

			break;

		case "updateinvoices":
			DBShowEditForm($invoices,$index);

			break;

		case "updateinvoicelines":
			DBShowEditForm($invoicelines,$index);
			break;

		case "deletecompanies":
			break;

		case "deleteworkers":
			break;

		case "deleteinvoices":
			break;

		case "deleteinvoicelines":
			break;

		case "parsecompanies":
			DBParseForm($PARAMS, $companies);
			DBShowListView($companies);
			break;

		case "parseworkers":
			DBParseForm($PARAMS, $workers);
			DBShowListView($workers);
			break;

		case "parseinvoices":
			DBParseForm($PARAMS, $invoices);
			DBShowListView($invoices);
			break;

		case "parseinvoicelines":
			DBParseForm($PARAMS, $invoicelines);
			DBShowListView($invoicelines);
			break;

		case "listcompanies":
			DBShowListView($companies);
			break;

		case "listworkers":
			DBShowListView($workers);
			break;

		case "listinvoices":
			DBShowListView($invoices);
			break;

		case "listinvoicelines":
			DBShowListView($invoicelines);
			break;
        default:
            print("BAD MODE: $mode");
            ShowMenu();

    }
break_mysql_connect($link);
exit();
/************** TEST FUNCTIONS *************************/
/*********
 *
 *********/
 function ShowMenu(){
	print "<li>Company |<a href=\"index.php?action=list&table=companies\">List Companies</a>|<a href=\"index.php?action=create&table=companies\">Add Company</a>|\n";
	print "<li>Workers |<a href=\"index.php?action=list&table=workers\">List Workers</a>|<a href=\"index.php?action=create&table=workers\">Add Worker</a>|\n";
	print "<li>Invoices |<a href=\"index.php?action=list&table=invoices\">List Invoices</a>|<a href=\"index.php?action=create&table=invoices\">Add Invoice</a>|\n";
	print "<li>InvoiceLines |<a href=\"index.php?action=list&table=invoicelines\">List Invoice Lines</a>|<a href=\"index.php?action=create&table=invoicelines\">Add Invoice Line</a>|\n";
}
function setRegex(){
	$regex['nameregex'] = "/[a-zA-Z,'- ]+/";
	$regex['emailregex']="/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(?:[a-zA-Z]{2.4}|museum)$/";
	$regex['boolean'] = '/true|false/';
	$regex['date'] = "/^[0-9-/]+$/";
	return $regex;
}
function setWorkers(){
	$regex=setRegex();
	$nameregex=$regex['nameregex'];
	$emailregex=$regex['emailregex'];
	$booleanregex=$regex['boolean'];

	$workers[0]['view']='workers';
	$workers[0]['name']='workers';
	$workers[0]['index'] = 'id';
//	$workers[0]['filter']= 'workers.active=workers.active';
	$workers[0]['sort']='order by workers.name asc';
	$workers[0]['fields']['id']= array (
		'title'=> 'Id No',
		'edit'=>false);
	$workers[0]['fields']['name']= array (
		'title'=>'Name',
		'edit'=>true,
		'required'=>true,
		'validation'=>$nameregex,
		'type'=>'text',
		 'size'=>25);
	$workers[0]['fields']['companyid']= array (
		'foreignkey'=>true,
		'title'=>'Company',
		'edit'=>true,
		'type'=>'select',
		'foreigntableno'=>1,
		'foreignfield'=>'name',
		'foreignfilter'=>'active=true',
		'foreignindex'=>'id',
		'required'=>true);
	$workers[0]['fields']['manager']= array (
		'title'=>'Manager',
		'edit'=>true,
		'required'=>false,
		 'validation'=>$nameregex,
		 'size'=>25);
	$workers[0]['fields']['email']= array (
		'title'=>'Email',
		'edit'=>true,
		'size'=>'50',
		'validation'=>$emailregex,
		'type'=>'text');
	$workers[0]['fields']['active']= array (
		'title'=>'Active',
		'edit'=>true,
		'validation'=>$booleanregex,
		'required'=>true,
		'type'=>'text');
	$workers[0]['fields']['ts']= array (
		'title'=>'Time Stamp',
		'edit'=>false);


	$workers[1]['name']= 'companies';
	$workers[1]['join']='workers.companyid=companies.id';
	$workers[1]['fields']['name']= array (
		'title'=>'Company Name',
		'edit'=>false);

	return $workers;
}
function setCompanies(){
	$regex=setRegex();
	$nameregex=$regex['nameregex'];
	$emailregex=$regex['emailregex'];

	$companies[0]['name']= 'companies';
	$companies[0]['index'] = 'id';
	$companies[0]['sort']='order by companies.name asc';
	$companies[0]['view']='companies';

	$companies[0]['fields']['id']= array (
		'title'=>'Id No',
		'edit'=>false,
		'type'=>'noedit');
	$companies[0]['fields']['name']= array (
		'title'=>'Name',
		'edit'=>true,
		'required'=>true,
		'validation'=>$nameregex,
		'type'=>'text',
		 'size'=>25);
	$companies[0]['fields']['principal']= array (
		'title'=>'Principal',
		'edit'=>true,
		'required'=>false,
		 'validation'=>$nameregex,
		'type'=>'text',
		 'size'=>25);
	$companies[0]['fields']['email']= array (
		'title'=>'Email',
		'edit'=>true,
		'validation'=>$emailregex,
		'type'=>'text',
		 'size'=>50);
	$companies[0]['fields']['ts']= array (
		'title'=>'Time Stamp',
		'edit'=>false,
		'type'=>'noedit');

	return $companies;
}



function genNavigationBar(){
}

/*************** END TEST CODE **********************/


/*************** FUNCTIONS ***************************/
/********************************* HERE THERE BE DRAGONS ***********************************/
/*********
 *
 *********/
function DBParseForm($PARAMS, $tables){
	//print "DBParseForm(PARAMS, $tables)";

	global $link;
	$tablename=$tables[0]['name'];


	if($PARAMS['id']=="new"){// create a record in the database
		$sql = "insert into $tablename (id) values (0)";
		do_mysql($link,$sql);

		$id = mysql_insert_id();
	} else {
		$id=$PARAMS['id'];
	}
	foreach($PARAMS as $field=>$value){
		if ($field=="table" || $field=="action" || $field=="id") continue;
		$sql = "update $tablename set $field=\"$value\" where id=$id";
		do_mysql($link,$sql,true);
	}
}
/*********
 *
 *********/
function DBGetSelectArray($tablename, $fieldname, $indexname="id", $filter="1=1"){
	global $link;
	if(strlen($filter)==0)$filter="1=1";
	if(strlen($indexname)==0)$indexname="id";


	$sql = "select $indexname,$fieldname from $tablename where $filter order by $fieldname";
	$list = MYSQLGetData($link,$sql);
	foreach($list as $field) {
		$newlist[$field[$indexname]]=$field[$fieldname];
	}
	return $newlist;
}
/*********
 *
 *********/
function DBShowEditForm($tables,$id=0){
	global $link;
//	print "DBShowEditForm(tables,$name,$id)\n";
	$tablename=$tables[0]['name'];
	$viewname = $tables[0]['view'];
	$formname = "parse".$tablename;

	$sql = genListSelect($tables,$tablename,$id);
	print "sql = $sql\n";
	$list = MYSQLGetData($link,$sql);
	$list=$list[0];

	print "<form method=\"post\" name=\"$formname\" action=\"index.php\">\n";
	print "<input type=\"hidden\" name=\"table\" value=\"$tablename\"/>\n";
	print "<input type=\"hidden\" name=\"action\" value=\"parse\"/>\n";
	print "<input type=\"hidden\" name=\"id\" value=\"$id\"/>\n";
	print "<li><input type=\"submit\" value=\"add Record\">\n";
	foreach($tables as $idx=>$table){
		foreach($table['fields'] as $field=>$fielddata){

			if(key_exists($field,$list)) $default=$list[$field];
			else if(key_exists('default',$table))$default=$fielddata['default'];
			else $default = '';

			if(key_exists('title',$fielddata))$title = $fielddata['title'];
			else $title=$field;

			if(key_exists('size',$fielddata))$size = $fielddata['size'];
			else $size=0;

			if(key_exists('type',$fielddata))$type=$fielddata['type'];
			else $type = 'noedit';


			switch($type){
				case "noedit":
					continue;
					break;
				case "text":
					print "<li>$title: <input type=\"$type\" name =\"$field\" value=\"$default\" size=\"$size\">\n";
					break;
				case "select":
					if(key_exists('foreigntableno',$fielddata)) $foreigntable = $tables[$fielddata['foreigntableno']]['name'];
					else $foreigntable = $tables[1]['name']; // by default we get the 2nd (index=1) table

					if(key_exists('foreignfield',$fielddata))$foreignfield=$fielddata['foreignfield'];
					else {print "ERROR: select in form for $tablename (view=$viewname) $foreigntable doesn't have a field name\n"; return false;}

					if(key_exists('foreignfilter',$fielddata))$foreignfilter=$fielddata['foreignfilter'];
					else $foreignfilter="";

					if(key_exists('foreignindex',$fielddata))$foreignindex=$fielddata['foreignindex'];
					else $foreignindex="";

					$valuelist= DBGetSelectArray($foreigntable,$foreignfield,$foreignindex, $foreignfilter);

					print "<li>$title: <select name=\"$field\">\n";
					print "\t<option value=\"i0\" SELECTED>SELECT ONE\n";
					foreach($valuelist as $index=>$value){
						$idx= "i".$index;
						print "\t<option value=\"$idx\">$value\n";
					}
					print "</select>\n";
					break;
				case "checkbox":
					if($default!="checked") $default ="";
					print"<li>$title: <input type=checkbox name=\"$field\" value=\"1\" $default>";
					break;
				default:
					print"type=$type\n";
			}// switch
		}
	}
	print "<li><input type=\"submit\" value=\"add Record\">\n";
	print "</form>\n";

}
/*********
 *
 *********/
 function DBShowCreateForm($tables){
	$tablename=$tables[0]['name'];
	$viewname=$tables[0]['view'];
	$formname = "parse".$tablename;

	print "<form method=\"post\" name=\"$formname\" action=\"index.php\">\n";
	print "<input type=\"hidden\" name=\"table\" value=\"$tablename\"/>\n";
	print "<input type=\"hidden\" name=\"action\" value=\"parse\"/>\n";
	print "<input type=\"hidden\" name=\"id\" value=\"new\"/>\n";
	print "<li><input type=\"submit\" value=\"add Record\">\n";
	foreach($tables as $idx=>$table){
		foreach($table['fields'] as $field=>$fielddata){

			if(key_exists('title',$fielddata))$title = $fielddata['title'];
			else $title=$field;

			if(key_exists('size',$fielddata))$size = $fielddata['size'];
			else $size=0;

			if(key_exists('type',$fielddata))$type=$fielddata['type'];
			else $type = 'noedit';

			if(key_exists('default',$table))$default=$fielddata['default'];
			else $default = '';

			switch($type){
				case "noedit":
					continue;
					break;
				case "text":
					print "<li>$title: <input type=\"$type\" name =\"$field\" value=\"$default\" size=\"$size\">\n";
					break;
				case "select":
					if(key_exists('foreigntableno',$fielddata)) $foreigntable = $tables[$fielddata['foreigntableno']]['name'];
					else $foreigntable = $tables[1]['name']; // by default we get the 2nd (index=1) table

					if(key_exists('foreignfield',$fielddata))$foreignfield=$fielddata['foreignfield'];
					else {print "ERROR: select in form for $tablename (view=$viewname) $foreigntable doesn't have a field name\n"; return false;}

					if(key_exists('foreignfilter',$fielddata))$foreignfilter=$fielddata['foreignfilter'];
					else $foreignfilter="";

					if(key_exists('foreignindex',$fielddata))$foreignindex=$fielddata['foreignindex'];
					else $foreignindex="";

					$valuelist= DBGetSelectArray($foreigntable,$foreignfield,$foreignindex, $foreignfilter);

					print "<li>$title: <select name=\"$field\">\n";
					print "\t<option value=\"i0\" SELECTED>SELECT ONE\n";
					foreach($valuelist as $index=>$value){
						$idx= "i".$index;
						print "\t<option value=\"$idx\">$value\n";
					}
					print "</select>\n";
					break;
				case "checkbox":
					if($default!="checked") $default ="";
					print"<li>$title: <input type=checkbox name=\"$field\" value=\"1\" $default>";
					break;
				default:
					print"type=$type\n";
			}// switch
		}
	}
	print "<li><input type=\"submit\" value=\"add Record\">\n";
	print "</form>\n";

}
/*********
 *
 *********/
function DBShowReadView($tables,$id){
	global $link;
	print "DBShowReadView(tables,$id)\n";
	$tablename=$tables[0]['name'];
	$viewname = $tables[0]['view'];
	$formname = "parse".$tablename;
print "<li>ID=$id".__LINE__."</li>f\n";
	$sql = genListSelect($tables,$id);
	print "<li>sql = $sql\n";
	$list = MYSQLGetData($link,$sql);
	if(count($list)==0) {
		print "No Records Returned</br>\n";
		exit(0);
	}
	print_r($list);
	$list=$list[0];
print_r($list);
	foreach($tables[0]['fields'] as $field=>$fielddata){

		if(key_exists($field,$list)) $value=$list[$field];
		else $value = '';

		if(key_exists('title',$fielddata))$title = $fielddata['title'];
		else $title=$field;

		// for foreign keys...
		if(key_exists('foreignkey',$fielddata) && $fielddata['foreignkey']) {
			if(key_exists('foreigntableno',$fielddata)) $foreigntable = $tables[$fielddata['foreigntableno']]['name'];
			else $foreigntable = $tables[1]['name']; // by default we get the 2nd (index=1) table

			if(key_exists('foreignfield',$fielddata))$foreignfield=$fielddata['foreignfield'];
			else {print "ERROR: in read for $tablename (view=$viewname) $foreigntable doesn't have a field name\n"; return false;}

			if(key_exists('foreignfilter',$fielddata))$foreignfilter=$fielddata['foreignfilter'];
			else $foreignfilter="";

			if(key_exists('foreignindex',$fielddata))$foreignindex=$fielddata['foreignindex'];
			else {print "ERROR:  in read for $tablename (view=$viewname) $foreigntable doesn't have a index\n"; return false;}

			$sql = "select $foreignfield from $foreigntable where $foreignindex='$value'";
			if(strlen($foreignfilter)>0)$sql.=" and $foreignfilter";
			print "SQL=$sql<br/>\n";
			$valuelist = MYSQLGetData($link,$sql);
			print_r($valuelist);
			$value = $valuelist[0][$foreignfield];
		}
		print "<li>$title: $value\n";
	}
 }
/*********
 *
 *********/
function DBShowListView($tables){
//print "DBShowListView(tables,$name)";
	global $link;
	$first = true;

	$tablename=$tables[0]['name'];
	$viewname = $tables[0]['view'];

	$sql=genListSelect($tables);
	//print "\nSQL=$sql<br/>\n";

	$list = MYSQLGetData($link,$sql);
	print "<table border=\"1\">\n";
	print "<tr>".genListHeader($tables[0]);

	print "<th>Actions</th>";
	print "</tr>\n";
	$index = $tables[0]['index'];

	$table = $tables[0];
	foreach($list as $rno=>$record){
		foreach($table['fields'] as $field=>$fielddata){

			if(key_exists('title',$fielddata)) $title=$fielddata['title'];
			else $title=$field;

			if(key_exists('foreignkey',$fielddata)){// this is actually a foreign key into a different table
				$f_tablename=$tables[$fielddata['foreigntableno']]['name']; // get the table name of foreign table
				$f_fieldname = $fielddata['foreignfield']; // get the fieldname of the value we need out of that table
				$field = $f_tablename.$f_fieldname; // magic: the field will be tablename.fieldname in the $list
			}
				print "<td>".$record[$field]."</td>";
		}

		$idx = $record[$index];
		print "<td><a href=\"index.php?action=read&table=$tablename&index=$idx\">Read</a> | ";
		print "<a href=\"index.php?action=update&table=$tablename&index=$idx\">Edit</a> | ";
		print "<a href=\"index.php?action=delete&table=$tablename&index=$idx\">Delete</a></td>";
		print "</tr>\n";
	}

	print "<table>\n";
	print "<a href=\"index.php?action=create&table=$tablename\">Create New Record</a><br/>\n";
}
/*********
 *
 *********/
function genFieldList($tables){
	$viewname=$tables[0]['view'];
	foreach($tables as $table){
		$tablename=$table['name'];

		if(!key_exists('fields',$table)){
			print "ERROR: no fields in $tablename of $viewname\n";
			return false;
		}
		foreach($table['fields'] as $field=>$properties){
			if(key_exists('title',$properties))	$title=$properties['title'];
			else $title = $field;
			$fields[$field]=$title;
		}
	}
	return $fields;
}
/*********
 *
 *********/
function genListHeader($table){
 	$header="";
	$tablename=$table['name'];

	if(!key_exists('fields',$table)){
		print "ERROR: no fields in $tablename\n";
		return false;
	}
	$fieldlist = "";
	foreach($table['fields'] as $field=>$properties){
		if(key_exists('title',$properties))	$title=$properties['title'];
		else $title = $field;
		$header.="<th>$title</th>";
	}
 	return $header;
}


/**************************
 * GENLISTSELECT
 ****************
 *
 ***************************/
function genListSelect($tables, $idx=0){
	print "<li>genListSelect(tables,$idx)".__LINE__."</li>\n";
	$viewname=$tables[0]['view'];
	$tablename=$tables[0]['name'];

// define some initial condition flags
	$firstwhere=true;
	$firstfield=true;
	$firsttable=true;
	$firstand=true;


// create the table list
	$tablelist = "";
	foreach($tables as $table){
		if(key_exists('name',$table)){
			if(!$firsttable)$tablelist.=",";
			else $firsttable = false;
			$tablelist.=$table['name'];
		}
	}
	if (key_exists('index',$tables[0]))  $index=$tables[0]['index'];
	else print "ERROR: table $tablename ($viewname) has no index field\n";

print "<li>idx=$idx ".__LINE__." </li>\n";
// create the field list
	$fieldlist = "";
	foreach($tables as $key=>$table){
		$tablename="";
		if(key_exists('name',$table)) $tablename=$table['name'];
		else print "ERROR: table with name=$name (of view $viewname) & key=$key has no name";
		if(key_exists('fields',$table)){
			foreach($table['fields'] as $field=>$properties){

				if(!$firstfield) $fieldlist.=",";
				else $firstfield=false;
				$fieldlist.=$tablename.".".$field;
				if($key!=0)$fieldlist.=" as $tablename".$field." ";
			}
		} else print "ERROR: no fields in $tablename (of $viewname)\n";
	}

// create the join rules string
	$joinrules = "";
	foreach($tables as $table){
		if(key_exists('join',$table)) {
			if(!$firstand) $joinrules.=" and ";
			else $firstand=false;
			$joinrules.=$table['join'];
		}
	}

// create the filter string
print "<br/>IDX=$idx<br/>\n";
	if($idx==0)	$idfilter=$tablename.".".$index."=".$tablename.".".$index;// trick to simplify coding
	else $idfilter=$tablename.".".$index."=$idx";
print "idfilter=$idfilter<br/>\n";

	if(key_exists('filter',$tables[0]))	$filterrules = $tables[0]['filter']." and ".$idfilter;
	else $filterrules = "".$idfilter;

// create the sort string
	if(key_exists('sort',  $tables[0]))	$sortrules = $tables[0]['sort'];
	else $sortrules = "";

// combine all the components into a select command
	$listsql = "select ".$fieldlist." from ".$tablelist;
	if (strlen($joinrules)+strlen($filterrules)>0) $listsql .=" where ".$joinrules;
	if (strlen($joinrules)>0 && strlen($filterrules)>0) $listsql .=" and ";
	$listsql .= $filterrules;
	$listsql .= ' '.$sortrules;
	return $listsql;
}

/**********************
 *
 ************************/
function CheckArray($tables,$name,$quiet=false){
//	print "Name: $name\n";

	$fields['edit']='if0';
	$fields['foreignfield']='Warning';
	$fields['foreignfilter']='ifforeign';
	$fields['foreignkey']='ifforeign';
	$fields['foreigntableno']='ifforeign';
	$fields['foreignindex']='ifforeign';
	$fields['required']='Warning';
	$fields['size']='ifedit';
	$fields['title']='Fatal';
	$fields['type']='ifedit';
	$fields['validation']='Warning';
	$main['filter']='Warning';
	$main['index']='if0';
	$main['join']='Warning';
	$main['name']='Fatal';
	$main['sort']='Warning';
	$main['fields']='Fatal';
	$main['view']='if0';

	$errorcount = array('Fatal'=>0,'Warning'=>0);
	$hasfields = false;

	if(!key_exists(0,$tables)) $errorcount=GenError($name,'missing table 0','Fatal',$errorcount, $quiet,__LINE__);
	if(!key_exists('view',$tables[0]))$errorcount=GenError($name,'missing view name','Fatal',$errorcount, $quiet,__LINE__);
	else $name = $tables[0]['view'];


	foreach($tables as $tableidx=>$table){
		foreach($main as $key=>$level){
			if($level == 'Fatal' && !key_exists($key,$table)) $errorcount=GenError($name,"($tableidx) missing ".$key,'Fatal',$errorcount, $quiet,__LINE__);
			else if($level == 'if0' && $tableidx==0 && !key_exists($key,$table)) $errorcount=GenError($name,"($tableidx) missing ".$key,'Fatal',$errorcount, $quiet,__LINE__);
		}

		if(key_exists('fields',$table)) {
			$hasfields = true;
			$fieldlist = $table['fields'];

			if(key_exists('foreignfield',$fieldlist)&& $fieldlist['foreignfield']==1)$isforeign=true;
			else $isforeign=false;

			if(key_exists('edit',$fieldlist) && $fieldlist['edit'])$isedit=true;
			else $isedit=false;

			foreach($fieldlist as $fieldname=>$fielddata){
				foreach($fields as $key=>$level){
					if($level == 'Fatal' && !key_exists($key,$fielddata)) $errorcount=GenError($name,"($tableidx) missing from field '$fieldname': ".$key,'Fatal',$errorcount, $quiet,__LINE__);
					else if($level == 'ifforeign' && $isforeign && !key_exists($key,$table)) $errorcount=GenError($name,"($tableidx) missing  from field '$fieldname': ".$key,'Fatal',$errorcount, $quiet,__LINE__);
					else if ($level == 'ifedit' && $isedit && !key_exists($key,$table)) $errorcount=GenError($name,"($tableidx) missing  from field '$fieldname': ".$key,'Fatal',$errorcount, $quiet,__LINE__);
				}
			}
		}

		foreach($table as $key=>$value)	if(!key_exists($key,$main))$errorcount=GenError($name,"($tableidx) unknown key ".$key,'Warning',$errorcount, $quiet,__LINE__);

		if($hasfields) foreach($table['fields'] as $fieldname=>$fielddata)
			foreach($fielddata as $key=>$value)
				if( !key_exists($key,$fields))$errorcount=GenError($name,"($tableidx) unknown key in field '$fieldname': ".$key,'Warning',$errorcount, $quiet,__LINE__);

	}


	if(!$quiet && $errorcount['Warning']+ $errorcount['Fatal']>0) 	print "\nCheck Array function found ".	$errorcount['Warning'].	" warnings and ".	$errorcount['Fatal']. 	" fatal errors.\n";
	if($errorcount['Fatal']==0)return $tables;
	else {
		print "The Check Array function found fatal errors. Processing suspended.<br/>\n";
		exit();
	}
}
function GenError($name,$problem,$level,$errorcount,$quiet, $line){
	if(!$quiet) print "Table $name has an error: $problem which is $level (line=$line). \n";
	$errorcount[$level]++;
	return $errorcount;
}

?>