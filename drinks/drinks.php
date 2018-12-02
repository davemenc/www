<?php
$drinks = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
$start=time();
for ($i=0;$i<100000;$i++)
	print time()."\n";
exit();

//print "TIME: ".time()."\n";
for ($i=0;$i<count($drinks);$i++)
for ($j=0;$j<count($drinks);$j++)
for ($k=0;$k<count($drinks);$k++)
for ($l=0;$l<count($drinks);$l++)
for ($m=0;$m<count($drinks);$m++)
for ($n=0;$n<count($drinks);$n++)
for ($o=0;$o<count($drinks);$o++)
for ($p=0;$p<count($drinks);$p++){
	$list = array($i,$j,$k,$l,$m,$n,$o,$p);
	if(checkdup($list))continue;
	foreach($list as $item) print  $drinks[$item].";";
	print "\n";
	//print $drinks[$i].'; '. $drinks[$j] .'; '. $drinks[$k] .'; '. $drinks[$l].'; '.  $drinks[$m].'; '.  $drinks[$n].'; '.  $drinks[$o].'; '.  $drinks[$p]." \n";
	$now=time()-$start+1;
//	if($now%1000==0) print "\n----------------- ".time(). "------------\n";
}
print "END TIME: ".time()."\n";

function checkdup($list){
	$counts = array();
	foreach($list as $item) {
		if(key_exists($item,$counts)) return true;
		else $counts[$item]=0;
	}
	print_r($counts);
	return false;
}
?>
