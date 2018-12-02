<?php
    include_once('config.php');
    include_once('header.php');
    include_once('../library/debug.php');
    include_once('../library/loc_login.php');
    include_once('../library/date.php');
    include_once "../library/mysql.php";
    include_once('../library/list.php');
    include_once('footer.php');
    include_once('../library/filtersql.php');
log_on();
debug_string("--------------------------------- START TODO PROGAM _-------");
$PARAMS = FilterSQL(array_merge($_POST,$_GET));
// Create link to DB
$link = make_mysql_connect($dbhost, $dbuser, $dbpass, $dbname);
//log in user

//debug_on();
debug_string("userno",$userno);
unset($PARAMS['password']);
debug_array("params",$PARAMS);
debug_string ("login succeeded");

    // set the mode
    if (isset($PARAMS['mode'])){$mode=$PARAMS['mode'];}
    else $mode = "display_page";

    debug_string("mode",$mode);

    switch($mode){
        case "showform":
        default:
            debug_string("mode=$mode");
    }
break_mysql_connect($link);
exit();
/***********************************************
 *
 ***********************************************/