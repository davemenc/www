<?php

// this program displays a web page with the number of days left until the Move
date_default_timezone_set('America/Los_Angeles');
$a_today = getdate();
$today = mktime($a_today['hours'],$a_today['minutes'],$a_today['seconds'],$a_today['mon'],$a_today['mday'],$a_today['year']);

$target = mktime(12,0,0,4,26,2013);
$diff= $target-$today;
$interval = round($diff/86400+.5,0);

?>

<html>
<head>
<style TYPE="text/css">
h1 {font-weight: 1200;
    font-size: 4em;
    font-family:"Verdana";
    }
span  {font-weight: 800;
    font-size: 3em;
    font-family:"Times";
    }
td {font-weight: 800;
    font-size: 3em;
    font-family:"Times";
    }
</style>
</head>
<body>
</br></br></br></br></br></br></br></br>
<center>

<h1> <?php echo $interval ?> DAYS UNTIL </h1>
<h1>The Move To Bridgepointe</h1>

<span>Other Dates</span>
<table>
<tr><td>April 8</td><td>Orientation</strike></td></tr>
<tr><td>April 10</td><td>Trash To Treasure</strike></td></tr>
<tr><td>April 15&nbsp;&nbsp;&nbsp;&nbsp;</td><td>End of Trash To Treasure</strike></td></tr>
<tr><td>April 15</td><td>Crates Delivered (Start Packin')</strike></td></tr>
<tr><td>April 26</td><td>Move Day: Go Home By 3PM</strike></td></tr>
<tr><td>April 29</td><td>First Day in New Building</strike></td></tr></body>
</html>
