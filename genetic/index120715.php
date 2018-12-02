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

// CONSTANTS
$genelen = 44;
$rulelen = 19;
$generationsize = 100;

//set the run number
$currentrun=-1; // invalid value
if(isset($PARAMS['r'])){
	$currentrun=$PARAMS['r'];
	$sql = "select * from runs where id=$currentrun ";
	$runreclist = MYSQLGetData($link,$sql);
	if(count($runreclist)==0) $currentrun=-1;
}
if ($currentrun==-1){// either never set or set in PARAMS to a run that doesnt exist
	$sql = "select * from runs where status='ongoing' order by id limit 1";
	$runreclist = MYSQLGetData($link,$sql);
	if(count($runreclist)==0){
		$currentrun = CreateNewRun($genelen,$generationsize);
	}else{
	$currentrun = $runreclist[0]['id'];
	}
}
// get the current generation

$sql = "select generation from genes where run=$currentrun order by generation desc limit 1";
$genereclist = MYSQLGetData($link,$sql);
if (count($genereclist)==0){
	print "<h1>CAN'T FIND GENES!</h1>\nRun=$currentrun</br>\n";
	exit();
}
$currentgeneration = $genereclist[0]['generation'];
//print "<br>$sql<br>\n";
//print_r($currentgeneration);

//print "<hr>$currentrun / $currentgeneration<hr>\n";

// set the mode
if (isset($PARAMS['m'])){
	$mode=$PARAMS['m'];
}
else $mode = "";

//$sql = "select * from genes where run=$currentrun and generation=$currentgeneration and ratings>0 order by generation asc limit 1";
//$genereclist = MYSQLGetData($link,$sql);

//debug_string("mode",$mode);
switch($mode){
	case "rating":
//		print "rating<br>";
		//ratingArray ( [m] => rating [rate] => ratingUseless [ID] => 9 )
		if(isset($PARAMS['rate']) && isset($PARAMS['ID'])){
			$userrate= $PARAMS['rate'];
			$id = $PARAMS['ID'];
			SaveGeneRating($link, $userrate, $id);
		}
		$sql = "select * from genes where generation=$currentgeneration and run=$currentrun and ratings=0";
		$genelist = MYSQLGetData($link,$sql);
		if(count($genelist)==0)	NewGeneration($link,$currentrun,$currentgeneration,$generationsize );
		RateGene($link,$currentrun,$currentgeneration);
		break;
	case "rategene":
//		debug_string("rate gene section");
//		debug_string("mode",$mode);
		RateGene($link,$currentrun,$currentgeneration);
		break;
	case "newrun":
		CreateNewRun($genelen,$generationsize);
		SHOWGENES($link,$currentrun,$currentgeneration);
		break;
	case "flipstatus":
		print "FLIPSTATUS<br>\n";
		if (isset($PARAMS['r'])){
			$r=$PARAMS['r'];
			FlipRunStatus($link, $r);
		}else {
			print "<H1>NO RUN NUMBER!</h1>\n";
			exit();
		}
		SHOWGENES($link,$currentrun,$currentgeneration);
		break;
	case "newgen":
		print "NEWGEN<br>\n";

// begin test code -- this always
		$sql = "select * from genes where generation=$currentgeneration and run=$currentrun and ratings>0";
		$genelist = MYSQLGetData($link,$sql);
		if (count($genelist)>0){
			$sql = "select * from genes where generation=$currentgeneration and run=$currentrun";
			$genelist = MYSQLGetData($link,$sql);
			$id1=$genelist[0]['id'];
			$id2=$genelist[1]['id'];
			$sql="update genes set ratings=1, avgscore=3 where id=$id1;";
			do_mysql($link,$sql,true);
			$sql="update genes set ratings=1, avgscore=3 where id=$id2;";
			do_mysql($link,$sql,true);
		}
//end test code

		NewGeneration($link,$currentrun,$currentgeneration,$generationsize );
		SHOWGENES($link,$currentrun,$currentgeneration+1);
		break;
	default:
//		debug_string("mode=$mode");
//		debug_string("default");
		SHOWGENES($link,$currentrun,$currentgeneration);
		exit();
}
break_mysql_connect($link);
exit();
/***************************************************************************/
/*                         FUNCTIONS                                       */
/***************************************************************************/
/***********************************************
 ***********************************************/
function FlipRunStatus($link, $run){
	print "FlipRunStatus(link, $run)<br>\n";
	$sql="select * from runs where id=$run";
	print "sql = $sql<br>\n";
	$runlist = MYSQLGetData($link,$sql);
	if(count($runlist)==0) {
		print "<H1>BAD RUN NUMBER!</h1>\n";
		print "<a href=\"index.php\">Return to front page</a><br>\n";
		exit();
	}
	$status = $runlist[0]['status'];
	print "status=$status<br>\n";

	if($status=='Ongoing') $newstatus='Paused';
	else if($status=='Paused') $newstatus='OnGoing';
	else {
		print "<H1>RUN IS NOT ACTIVE; STATUS CAN'T BE CHANGED!</h1>\n";
		exit();
	}
	$sql = "update runs set status='$newstatus' where id=$run";
	print "sql update = $sql<br>\n";
	do_mysql($link,$sql,true);
}
/***********************************************
 ***********************************************/
 function GetPercentDone($link,$run,$generation){

 	$sql= "select *  from genes where ratings>0 and generation=$generation and run=$run";
	$ratedgenes= MYSQLGetData($link,$sql);
	$ratedgenecount = count($ratedgenes);

	$sql= "select * from genes where  generation=$generation and run=$run";
	$allgenes= MYSQLGetData($link,$sql);
	$totalgenecount = count($allgenes);

	return(100*$ratedgenecount/$totalgenecount);
 }
/***********************************************
 ***********************************************/
function RateGene($link,$run,$generation){
	global $rulelen;
	$generec = GetGeneToRate($link,$run,$generation);
	$id = $generec['id'];
	$gene = substr($generec['gene'],1);// remove leading letter
	$rulestring = GetRulesFromGene($gene,$rulelen);
	$startstring = GetStartFromGene($gene,$rulelen);
	$jscript = "<script>\n $rulestring \n $startstring </script>\n";
	$percentdone = GetPercentDone($link,$run,$generation);

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
	<h1>Computer Generated Animations</h1>
	<b>Run # $run | Generation # $generation | Gene # $id | Done: $percentdone%</b>  <br>
	<a href="index.php">QUIT</a>
  <div class="CanvasContainer">
    <canvas id="drawingCanvas" width="706" height="706"></canvas>
  </div>
  <div class="Toolbar">
<b>PLEASE RATE THIS</b> (Try to use all the buttons; some ARE not as bad as others!):<br>
<table>
<tr>
<td>
<form id="ratingUseless" method="post" action="index.php">
<input type="submit"  value="WORST">
<input type="hidden" name="m" value="rating">
<input type="hidden" name="rate" value="ratingUseless">
<input type="hidden" name="ID" value="$id">
</form>
</td><td>
<form id="ratingBad" method="post" action="index.php">
<input type="submit"  value="WORSE">
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
</td>
<td> &nbsp;&nbsp;&nbsp;</td>
<td>
<form id="ratingGood" method="post" action="index.php">
<input type="submit"  value="GOOD">
<input type="hidden" name="m" value="rating">
<input type="hidden" name="rate" value="ratingGood">
<input type="hidden" name="ID" value="$id">
</form>
</td><td>
<form id="ratingGreat" method="post" action="index.php">
<input type="submit"  value="BETTER">
<input type="hidden" name="m" value="rating">
<input type="hidden" name="rate" value="ratingGreat">
<input type="hidden" name="ID" value="$id">
</form>
</td><td>
<form id="ratingExcellent" method="post" action="index.php">
<input type="submit"  value="BEST">
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
/***********************************************
 ***********************************************/
function NewGeneration($link,$run,$generation,$gensize){
//	print"NewGeneration(link,$run,$generation,$gensize)<br>\n";

	$sql = "select * from genes where run=$run and generation=$generation and ratings>0 order by avgscore desc";
	$generecs = MYSQLGetData($link,$sql);
//	print_r($generecs);
	$total=0;
	foreach($generecs as $key=>$gene){
		$total += $gene['avgscore'];
		$generecs[$key]['runtotal'] =  $total;
	}

	$newgeneration = $generation+1;
	$p=0;
	while($p<$gensize){
		// mate 2 genes
		$geneno1=FindGene($generecs,$total);
		$geneno2=FindGene($generecs,$total);
		if($geneno1==$geneno2) continue; // if the two are the same, it dont count
		$gene1 = $generecs[$geneno1]['gene'];
		$gene2 = $generecs[$geneno2]['gene'];
		$gene3 = Mate2Genes($gene1,$gene2);
		if($gene3==$gene2 || $gene3==$gene1) continue; // if the child  is the same as one of the parents it dont count

		$sql ="insert into genes (run,generation, gene) values ('$run','$newgeneration','$gene3')";
		do_mysql($link,$sql,true);
		$p++;
	}
//	print "<hr><h1>NEED TO SAVE MATED GENES FOR NEW GENERATION</h1><b>LINE 263</b><hr>\n";
}
/***********************************************
 ***********************************************/

/***********************************************
 ***********************************************/
 /*function randomgene($genes){
 	shuffle($genes);
	$result = $genes[0];
	return $result;
 }*/
 /***********************************************
  ***********************************************/
function GetGeneToRate($link,$run,$generation){
	$sql = "select * from genes where generation=$generation and run=$run  order by ratings,rand() limit 1";
	$genereclist = MYSQLGetData($link,$sql);
	$generec = $genereclist[0];
	return $generec;
}
 /***********************************************
  ***********************************************/
function GetRulesFromGene($gene,$rulelen){
	$leftgene = substr($gene,1,$rulelen);
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
	$datastr = substr($gene,$rulelen+1);// the +1 is to skip the leading non numeric character
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
	//print "SaveGeneRating(link,$userrate, $id)<br>\n";

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
//rint "<br>\n$id: $userrate = $rate<br>\n";
	$sql = "insert into ratings (geneid,rating) values ( $id,$rate)";
	do_mysql($link,$sql,true);

	$sql = "select avg(rating) as rateavg,count(rating) as ratecount from ratings where geneid=$id group by geneid";
	$rating = MYSQLGetData($link,$sql);
	$rateavg = $rating[0]['rateavg'];
	$ratecount = $rating[0]['ratecount'];

	$sql = "update genes set avgscore = $rateavg,ratings=$ratecount where id=$id";
	do_mysql($link,$sql,true);

}
 /***********************************************
  ***********************************************/
function SHOWGENES($link,$run,$generation){

$sql = "select * from runs";
$runs = MYSQLGetData($link,$sql);

$runrecs = array();
$headers = array();
foreach ($runs[0] as $key=>$values) $headers[]=ucwords($key);
$headers[]='Total Rated';
$headers[]='Total UnRated';
$headers[]='Average Rated';
$headers[]='Max Rated';
foreach ($runs as $runrec){
	$id=$runrec['id'];
	$runrecs[$id]=$runrec;
	$runrecs[$id]['Total Rated']=0;
	$runrecs[$id]['Total UnRated']=0;
	$runrecs[$id]['Average Rated']=0;
	$runrecs[$id]['Max Rated']=0;
}


$sql = "select runs.id,runs.status,genes.generation, count(ratings) as 'Total Rated',avg(ratings) as 'Average Rated',max(ratings) as 'Max Rated' from runs,genes where genes.run=runs.id and ratings>0 and generation = $generation group by run";
$rated = MYSQLGetData($link,$sql);
foreach($rated as $key=>$value){
	$id = $value['id'];
	$runrecs[$id]['Total Rated']=$value['Total Rated'];
	$runrecs[$id]['Max Rated']=$value['Max Rated'];
	$runrecs[$id]['Average Rated']=$value['Average Rated'];
}


$sql = "select runs.id, count(ratings) as 'Total UnRated' from runs,genes where genes.run=runs.id and ratings=0 and generation = $generation  group by run,generation";
$unrated = MYSQLGetData($link,$sql);
foreach($unrated as $key=>$value){
	$id = $value['id'];
	$runrecs[$id]['Total UnRated']=$value['Total UnRated'];
}
if(count($runrecs)>0){
	print "<h1>List of Runs</h1>\n";
	print "<table border=\"1\">\n";
	print "<tr>";
	foreach($headers as $head){
		print "<th>".$head."</th>";
	}
	print "<th colspan=\"3\">Actions</th>\n";
	print "</tr>\n";
	foreach($runrecs as $runrec){
		if(isset($runrec['id'])){
			$r = $runrec['id'];
			print "<tr>";
			foreach($runrec as $field){
				print "<td>$field</td>";
			}
			print "<td><a href=\"index.php?m=rategene&r=$r\">Rate A Gene</a></td><td><a href=\"index.php?m=flipstatus&r=$r\">Toggle Status</a></td><td><a href=\"index.php?m=newgen&r=$r\">New Generation</a></td>\n";
			print "</tr>\n";
		}
	}
	print "</table>\n";
	print "<a href=\"index.php?m=newrun\">Create A New Run</a><br>\n";
}else{
	print "<h1>NO RUNS IN DATABASE!</h1>\n";
	exit();
}


	$sql = "select * from genes where run=$run and generation=$generation order by ratings,avgscore";
	$genereclist = MYSQLGetData($link,$sql);
	$headings = $genereclist[0];
//	print "<br>\n<a href=\"index.php?m=rategene\">RateGene</a><br>\n";

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

/*
	$sql = "select * from ratings order by geneid";
	$genereclist = MYSQLGetData($link,$sql);
	if(count($genereclist)>0){
		$headings = $genereclist[0];


		print "<h1>Ratings List </h1>";
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
	} else {
		print "<H1>No Ratings</h1>\n";
	}
*/
$sql = "select Id,run,generation,gene,avgscore,ratings from genes where run=1  order by gene,run,generation";
$lastgene = "";
$genes = MYSQLGetData($link,$sql);
	print "<h1> All Gene List</h1>";
	print "<table border=\"1\"><tr>";
	foreach($genes[0] as $key=>$value){
		$fieldname = ucwords($key);
		print "<th>$fieldname</th>";
	}
	print"<th>Dup</th>";
	print "</tr>\n";
	print "<tr>\n";
	foreach($genes as $record){
		foreach($record as $key=>$value){
			if($key=="gene"){
				if($value==$lastgene) $mark = "*";
				else $mark = " ";
				$lastgene = $value;
			}
			print "<td>$value</td>";
		}
		print "<td>$mark</td>";
		print "</tr>\n";
	}
	print "</table>\n";

}

 /***********************************************
  ***********************************************/
function CreateNewRun($genelen,$numgenes){
//	print "CreateNewRun($genelen,$numgenes)<br>\n";
	global $link;
	$sql = "insert into runs (status) values ('Ongoing')";
	do_mysql($link,$sql,true);
	$run = mysql_insert_id($link);

	for($i=0;$i<$numgenes;$i++){
		$s = "a";
		for($j=0;$j<$genelen;$j++){
			$val = mt_rand(0,1);
			$s .= $val;
		}
		$sql = "insert into genes (run,generation,gene) values ($run,0,'$s')";
		do_mysql($link,$sql,true);
	}
	return $run;
}

 /***********************************************
  ***********************************************/
function Mate2Genes($gene1,$gene2){
//	print "Mate2Genes($gene1,$gene2)<br>\n";
	$len = strlen($gene1);
	$crossover = mt_rand(2,$len-1);//the 2 should be a 1
	$part1 = substr($gene1,0,$crossover);
	$part2 = substr($gene2,$crossover,$len-$crossover);
	$newgene = $part1 . $part2;

/*	if(mt_rand(1,10)==10){ // 1 in 10 chance for mutation
		$bitnum = mt_rand(1,strlen($newgene));
		$oldbit = substr($newgene,$bitnum,1);
		if($oldbit==0)$newbit='1';else $newbit='0';
		substr_replace($newgene,$newbit,$bitnum,1);
	}
*/
	return $newgene;
}
?>