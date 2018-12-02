<?php
// this is a program to simulate a bouncing ball
//for ($i=1;$i< 1000;$i++) if(isbong($i))print "YES\n";
$dt=.01;
$x=0;
$y=0;
$vx=10*$dt;
$vy=90*$dt;
$ax=0*$dt;
$ay=-32.2*$dt*$dt;
$wall1=200;
$wall2=0;
$floor=0;
$ceiling=500;
$bounce=.8;
print "  T         X         Y           VX        VY           AX          AY \n";
for ($sec=0; $sec<1000; $sec+=$dt){
	// do the physics thing
	$x=$x+$vx;
	$y=$y+$vy;
	$vx=$vx+$ax;
	$vy=$vy+$ay;

	// Bounce?
	if($x>=$wall1){
		$vx=-$vx*$bounce;
		$x=$wall1;
	}
	if($x<=$wall2){
		$vx=-$vx*$bounce;
		$x=$wall2;
	}

	if($y>=$ceiling){
		$vy=-$vy*$bounce;
		$y=$ceiling;
	}
	if($y<=$floor){
		$vy=-$vy*$bounce;
		$y=$floor;
	}

	// change horizontal acceleration (due to wind resistance)

//	$ax = -sgn($vx)*.01*$vx*$vx;

//	if (abs($ax<.1)&& abs($ay<.1)) exit(); // stopped, quit!

	printf( "%6.2f:  %7.2f , %7.2f   ;   %7.3f , %7.3f  ;  %9.4f , %9.4f\n",$sec, $x,$y , $vx,$vy , $ax,$ay);
}
exit();
$minplayers = 2;
$maxplayers = 20;
$firstplayer=1;
$numplays = 101;
for($numplayers=$minplayers;$numplayers<=$maxplayers; $numplayers++){
	$lastplayer = $numplayers;
	print "====== PLAYERS: $numplayers ======";
	$plays = array_fill(1,$numplayers,0);
	$bongs = array_fill(1,$numplayers,0);
	$direction = 1;
	$player = $firstplayer;
	for($count=1;$count<$numplays; $count++){
			//print "\nCount $count= player $player\n";
		$plays[$player]++;
		if(isbong($count)) {
			$direction = -$direction;
			$bongs[$player]++;
		}
		$player = $player+$direction;
		if($player>$lastplayer)$player=$firstplayer;
		if($player<$firstplayer)$player= $lastplayer;
	}
	print "\nPLAYS\n";
	foreach($plays as $player=>$count){
		print "$player: $count\n";
	}
	print "\nBONGS\n";
	foreach($bongs as $player=>$count){
		print "$player: $count\n";
	}
}

function isbong($num){
	//print "function isbong($num)\n";
	if($num%7==0) return true;
	if($num%11==0) return true;
	$snum = sprintf("%03d",$num);
	for($i=0;$i<3;$i++){
		if(substr($snum,$i,1)=="7") return true;
		if(substr($snum,$i,2)=="11") return true;
	}
}
function sgn($num){
	if($num<0)return (-1);
	else return(1);
}
?>