<?php
    include_once('config.php');
    include_once('../library/debug.php');
    include_once "../library/mysql.php";
 //   include_once('../library/filtersql.php');
log_on();
debug_string("--------------------------------- START GAMECREDIT PROGAM _-------");
$PARAMS = array_merge($_POST,$_GET);
// Create link to DB
$link = make_mysql_connect($dbhost, $dbuser, $dbpass, $dbname);
//log in user

//debug_on();

unset($PARAMS['password']);
debug_array("params",$PARAMS);
debug_string ("login succeeded");


$rulelen = 19;
RateGene();


    // set the mode
    if (isset($PARAMS['m'])){$mode=$PARAMS['m'];}
    else $mode = "";

    debug_string("mode",$mode);
    switch($mode){
        case "rategene":
        	debug_string("rate gene section");
        	debug_string("mode",$mode);
			RateGene();
			break;
        default:
            debug_string("mode=$mode");
    }
break_mysql_connect($link);
exit();
/***************************************************************************/
/*                         FUNCTIONS                                       */
/***************************************************************************/
/***********************************************
 ***********************************************/
function RateGene(){
	global $link,$rulelen;
	$generec = GetGeneToRate($link);
	$id = $generec['id'];
	$gene = $generec['gene'];
	$rulestring = GetRulesFromGene($gene,$rulelen);
	$startstring = GetStartFromGene($gene,$rulelen);
	$jscript = "<script>\n $rulestring \n $startstring </script>\n";


echo <<< EOF
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>DrawCell</title>
  <link rel="stylesheet" href="drawcells.css">

$jscript
<script src="drawcells2.js"></script>
</head>

<body>
	<h1>Computer Generated Movie</h1>
	<h2>Gene # $id</h2>
	<h3>Please Rate Me So I Might Learn</h3>
  <div class="CanvasContainer">
    <canvas id="drawingCanvas" width="781" height="781"></canvas>
  </div>
  <div class="Toolbar">
b>PLEASE RATE THIS:</b><br>
<table>
<tr>
<td>
<form id="ratingUseless" method="post" action="index.php">
<input type="submit"  value="USELESS">
<input type="hidden" name="m" value="rating">
<input type="hidden" name="rate" value="ratingUseless">
</form>
</td><td>
<form id="ratingBad" method="post" action="index.php">
<input type="submit"  value="BAD">
<input type="hidden" name="m" value="rating">
<input type="hidden" name="rate" value="ratingBad">
</form>
</td><td>
<form id="ratingOK" method="post" action="index.php">
<input type="submit"  value="OK">
<input type="hidden" name="m" value="rating">
<input type="hidden" name="rate" value="ratingOK">
</form>
</td><td>
<form id="ratingGood" method="post" action="index.php">
<input type="submit"  value="GOOD">
<input type="hidden" name="m" value="rating">
<input type="hidden" name="rate" value="ratingGood">
</form>
</td><td>
<form id="ratingGreat" method="post" action="index.php">
<input type="submit"  value="GREAT">
<input type="hidden" name="m" value="rating">
<input type="hidden" name="rate" value="ratingGreat">
</form>
</td><td>
<form id="ratingExcellent" method="post" action="index.php">
<input type="submit"  value="EXCELLENT">
<input type="hidden" name="m" value="rating">
<input type="hidden" name="rate" value="ratingExcellent">
</form>
</td>
</tr>
</table>
    </div>
  </div>
</body>
</html>
EOF;

}
/***********************************************
 ***********************************************/
function teststuff(){
$sql = "select * from runs where status='ongoing' order by id limit 1";
$run = MYSQLGetData($link,$sql);
$currentrun = $run[0]['id'];

$sql = "select generation from genes where run=$currentrun order by generation asc limit 1";
$genereclist = MYSQLGetData($link,$sql);
$currentgeneration = $genereclist[0]['generation'];
print "<hr>$currentgeneration<hr>\n";

$sql = "select genes.id,finalscore as fscore, avg(rating) as score,gene from genes, ratings where genes.id=geneid and run=$currentrun and generation=$currentgeneration group by genes.id";
$sql = "select genes.id,finalscore,gene from genes where run=$currentrun and generation=$currentgeneration and finalscore=0";
$genereclist = MYSQLGetData($link,$sql);
//print_r($genes);
print "<hr>$sql<hr>\n";
//exit();

//print "<hr>\n";print_r($generation);print "<hr>\n";
$generec = randomgene($genereclist);

//print "<hr>\n";print_r($generec);print "<hr>\n";
$gene= $generec['gene'];
$genelen = strlen($gene);
$rulelen = 19;
$leftgene = substr($gene,0,$rulelen);
$rightgene = substr($gene,$rulelen);

print "rulelen=$rulelen<br>\n";
print "Gene=$gene<br>\n";
print "lgene=$leftgene (".strlen($leftgene).") <br>\n";
print "rgene=$rightgene (".strlen($rightgene).") <br>\n";

$rule = "";
for($i=0;$i<$rulelen;$i++){
	$rule.=substr($gene,$i,1).",";
}
$l = strlen($rule);
$rule = substr($rule,0,$l-1);//remove the final comma
print "<br>$leftgene = $rule<br>\n";
print "var rules = [$rule];<br>\n";
$datastr = substr($gene,$rulelen);
$xlist = "";
$ylist = "";
for($x=0;$x<4;$x++){
	for($y=0;$y<4;$y++){
		$val = substr($datastr,$y*4+$x,1);
		if ($val=="1"){
			$xlist.="$x,";
			$ylist.="$y,";
		}

	}
}
$xlist = substr($xlist,0,strlen($xlist)-1);//remove the final comma
$ylist = substr($ylist,0,strlen($ylist)-1);//remove the final comma
print "<br>$rightgene=$xlist/$ylist<br>\n";
print "var sourcex=[$xlist];<br>\n";
print "var sourcey=[$ylist];<br>\n";



}
/***********************************************
 ***********************************************/
 function randomgene($genes){
 	shuffle($genes);
	//print_r($genes);
	$result = $genes[0];
	return $result;
 }
 /***********************************************
  ***********************************************/
function GetGeneToRate($link){
	$sql = "select * from genes where generation=1 and run=1  order by ratings,rand() limit 1";
	$genereclist = MYSQLGetData($link,$sql);
	$generec = $genereclist[0];
	return $generec;
}
 /***********************************************
  ***********************************************/
function GetRulesFromGene($gene,$rulelen){
	$leftgene = substr($gene,0,$rulelen);
	$rule = "";
	for($i=0;$i<$rulelen;$i++){
		$rule.=substr($gene,$i,1).",";
	}
	$l = strlen($rule);
	$rule = substr($rule,0,$l-1);//remove the final comma
	$code = "var rules = [$rule];";
	return($code);

}
 /***********************************************
  ***********************************************/
 function GetStartFromGene($gene,$rulelen){
	$rightgene = substr($gene,$rulelen);
	$datastr = substr($gene,$rulelen);
	$xlist = "";
	$ylist = "";
	for($x=0;$x<4;$x++){
		for($y=0;$y<4;$y++){
			$val = substr($datastr,$y*4+$x,1);
			if ($val=="1"){
				$xlist.="$x,";
				$ylist.="$y,";
			}

		}
	}
	$xlist = substr($xlist,0,strlen($xlist)-1);//remove the final comma
	$ylist = substr($ylist,0,strlen($ylist)-1);//remove the final comma
	$code= "var sourcex=[$xlist];\nvar sourcey=[$ylist];\n";
	return $code;
}
?>