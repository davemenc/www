<?php
$check=0;
for($i=0; $i<1000; $i++){
	$x = mt_rand( 0 , 99);
	$y = mt_rand( 0 , 99);
	$v = mt_rand(1,200)+mt_rand(1,200)+mt_rand(1,200)+mt_rand(1,200)+mt_rand(1,200);
	$city[$x][$y]['v'] = $v;
	$city[$x][$y]['vs'] = 0;
	$city[$x][$y]['x'] = $x;
	$city[$x][$y]['y'] = $y;
	$check++;
}
$city = process_city1($city);
$rec = current($city);
$maxvs = -999;
$minvs = 999;
$maxx = 0;
$maxy = 0;
$minx = 0;
$miny = 0;

while($row=next($city)){
	foreach($row as $block){
	if($block['vs']<$minvs){
			$minvs = $block['vs'];
			$minx =  $block['x'];
			$miny =  $block['y'];
		}
		if($block['vs']>$maxvs){
			$maxvs = $block['vs'];
			$maxx =  $block['x'];
			$maxy =  $block['y'];
		}
	}
}
foreach($city as $x=>$row){
	foreach($row as $y=>$block){
		$scale = round(map($block['vs'],$minvs,$maxvs,1,9));
//		print "$x,$y: $scale</br>\n";
		$city[$block['x']][$block['y']]['char']=$scale;
	}
}
print "$minx,$miny: $minvs</br>\n";
print "$maxx,$maxy: $maxvs</br>\n";

for($x=0;$x<100;$x++){
	printf("%3d ",$x);
	for($y=0;$y<100;$y++){
		if(array_key_exists($x,$city) &&array_key_exists($y,$city[$x])){
			print $city[$x][$y]['char'];
		} else {
			print ".";
		}
	}
	print "\n";
}
exit();
function distance($x1,$y1,$x2,$y2){
	return(max(1,pow($x2-$x1,2)+pow($y2-$y1,2)));
}
function process_city1($city){
	$check=0;
	foreach($city as $x=>$rec){
		foreach($rec as $y=>$val){
			$firstval=$val['v'];
			foreach($city as $x2=>$rec2){
				foreach($rec2 as $y2=>$val2){
					$city[$x][$y]['vs'] += round($val2['v'] / max( 1, pow($x2-$x,2) + pow($y2-$y,2) ),2);
					$check++;
				}
			}
		}
	}
	return $city;
}

function  map( $x, $in_min,  $in_max,  $out_min,  $out_max)
{
  return ($x - $in_min) * ($out_max - $out_min) / ($in_max - $in_min) + $out_min;
}
?>