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

debug_on();

$sql = "select runs.id,runs.status,genes.generation, count(ratings) as 'Total Rated',avg(ratings) as 'Average Rated',max(ratings) as 'Max Rated' from runs,genes where genes.run=runs.id and ratings>0 group by run,generation";
$runrecs = MYSQLGetData($link,$sql);

$sql = "select count(ratings) as 'Total UnRated' from runs,genes where genes.run=runs.id and ratings=0 group by run,generation";
$unrated = MYSQLGetData($link,$sql);
foreach($unrated as $key=>$value){
	$runrecs[$key]['Total UnRated']=$value['Total UnRated'];
}

if(count($runrecs)>0){
	print "<h1>List of Runs</h1>\n";
	print "<table border=\"1\">\n";
	print "<tr>";
	foreach($runrecs[0] as $key=>$value){
		print "<th>".ucwords($key)."</th>";
	}
	print "<th colspan=\"3\">Actions</th>\n";
	print "</tr>\n";
	foreach($runrecs as $runrec){
		$r = $runrec['id'];
		print "<tr>";
		foreach($runrec as $field){
			print "<td>$field</td>";
		}
		print "<td><a href=\"index.php?m=rategene&r=$r\">Rate A Gene</a></td><td><a href=\"index.php?m=flipstatus&r=$r\">Toggle Status</a></td><td><a href=\"index.php?m=newgen&r=$r\">New Generation</a></td>\n";
		print "</tr>\n";
	}
print "</table>\n";
}else{
	print "<h1>NO RUNS IN DATABASE!</h1>\n";
	exit();
}
$population = 1000;
$currentgeneration=1;
$currentrun=1;
$sql = "select * from genes where run=$currentrun and generation=$currentgeneration and ratings>0 order by avgscore desc";
$generecs = MYSQLGetData($link,$sql);

$total=0;
foreach($generecs as $key=>$gene){
	$total += $gene['avgscore'];
	$generecs[$key]['runtotal'] =  $total;
}
print_r($generecs);
$counts = array();
$stats=array();
print "Total=$total<br>\n";
for($p=0;$p<$population;$p++){
	// mate 2 genes
	$geneno1=FindGene($generecs,$total);
	$geneno2=FindGene($generecs,$total);
//	print "$gene1 : $gene2</br>\n";
	$gene1 = $generecs[$geneno1];
	$gene2 = $generecs[$geneno2];
	$gene3 =MateGenesDummy($gene1,$gene2);
}


break_mysql_connect($link);
exit();

function FindGene($genelist,$total){
	$val=mt_rand(0,$total*1000)/1000;

//	print "val=$val<br>\n";
	for($i=0;$i<count($genelist);$i++){
		if($genelist[$i]['runtotal']>$val) return($i);
	}
	// if we get here there is something wrong
	print "<h1>COULDN'T FIND GENE!</h1>";
	return -1;
}
function MateGenesDummy($gene1,$gene2){
	return $gene1;
}
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
<input type="hidden" name="ID" value="$id">
</form>
</td><td>
<form id="ratingBad" method="post" action="index.php">
<input type="submit"  value="BAD">
<input type="hidden" name="m" value="rating">
<input type="hidden" name="rate" value="ratingBad">
<input type="hidden" name="ID" value="$id">
</form>
</td><td>
<form id="ratingOK" method="post" action="index.php">
<input type="submit"  value="OK">
<input type="hidden" name="m" value="rating">
<input type="hidden" name="rate" value="ratingOK">
<input type="hidden" name="ID" value="$id">
</form>
</td><td>
<form id="ratingGood" method="post" action="index.php">
<input type="submit"  value="GOOD">
<input type="hidden" name="m" value="rating">
<input type="hidden" name="rate" value="ratingGood">
<input type="hidden" name="ID" value="$id">
</form>
</td><td>
<form id="ratingGreat" method="post" action="index.php">
<input type="submit"  value="GREAT">
<input type="hidden" name="m" value="rating">
<input type="hidden" name="rate" value="ratingGreat">
<input type="hidden" name="ID" value="$id">
</form>
</td><td>
<form id="ratingExcellent" method="post" action="index.php">
<input type="submit"  value="EXCELLENT">
<input type="hidden" name="m" value="rating">
<input type="hidden" name="rate" value="ratingExcellent">
<input type="hidden" name="ID" value="$id">
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
 /***********************************************
  ***********************************************/
function SaveGeneRating($link, $userrate, $id){
	print "SaveGeneRating(link,$userrate, $id)<br>\n";

	switch($userrate){
		case "ratingUseless":
			$rate = 1;
			break;
		case "ratingBad":
			$rate = 2;
			break;
		case "ratingOK":
			$rate = 3;
			break;
		case "ratingGood":
			$rate = 4;
			break;
		case "ratingGreat":
			$rate = 5;
			break;
		case "ratingExcellent":
			$rate = 6;
			break;
		default:
			$rate = -1;
			print "PROBLEM IN RATING!\n";
	}



}
 /***********************************************
  ***********************************************/
function SHOWGENES($link,$run,$generation){
	$sql = "select * from genes where run=$run and generation =$generation";
	$genereclist = MYSQLGetData($link,$sql);
	$headings = $genereclist[0];

	print "<h1> Gene List for $run/$generation</h1>";
	print "<table border=\"1\"><tr>";
	foreach($headings as $key=>$value){
		$fieldname = ucwords($key);
		print "<th>$fieldname</th>";
	}
	print "</tr>\n";
	print "<tr>\n";
	foreach($genereclist as $record){
		foreach($record as $key=>$value){
			print "<td>$value</td>";
		}
		print "</tr>\n";
	}
	print "</table>\n";
	exit();
}

 /***********************************************
  ***********************************************/
?>