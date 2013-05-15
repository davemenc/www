<?php
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

//include_once("mysql.php");

/******************* TEST CODE **********************/
include_once("../library/mysql.php");
include_once("config.php");
$PARAMS = FilterSQL(array_merge($_POST,$_GET));
$link = make_mysql_connect($dbhost, $dbuser, $dbpass, $dbname);
   // set the mode
    if (isset($PARAMS['mode'])){$mode=$PARAMS['mode'];}
    else $mode = "showmenu";

    debug_string("mode",$mode);
    switch($mode){
    	case "showmenu";
    		ShowMenu();
    		break;
        case "listcompany":
        	listcompanies("mode=$mode");
      		ShowCompanyList();
			break;
        case "addcompany":
        	ShowAddCompany();
			break;
		case "showcompanydetail":
			ShowCompanyDetail($PARAMS['companyno']);
			break
 		case "editcompany":
 			EditCompany($PARAMS['companyno']);
 			break;
 		case "parseaddcompany":
 			ParseAddCompany($PARAMS);
      		ShowCompanyList();
			break;
 		case "parseeditcompany":
 			ParseEditCompany($PARAMS);
 			ShowCompanyDetail($PARAMS['companyno']);
			break
     	case "listworkers":
      		ShowWorkerList();
			break;
        case "addworker":
        	ShowAddWorker();
			break;

        default:
            debug_string("mode=$mode");
    }
break_mysql_connect($link);
exit();
/************** TEST FUNCTIONS *************************?
function ShowMenu(){
	print "<li>Company |<a href=\"index.php&mode=listcompanies\">List</a>|<a href=\"index.php&mode=addcompany">Add</a>|\n";
	print "<li>Workers |<a href=\"index.php&mode=listworkers\"List</a>|<a href=\"index.php&mode=addworker">Add</a>|\n";
	print "<li>Invoices |List|Add|\n";
}

/*************** END TEST CODE **********************/


/*************** FUNCTIONS ***************************/
?>