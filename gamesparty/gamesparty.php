<?php
include_once("config.php");
include_once("/home/dmenconi/public_html/library/debug.php");
include_once("/home/dmenconi/public_html/library/mysql.php");
//debug_on();
print "hi\n";
$link = make_mysql_connect($dbhost,$dbuser,$dbpass,$dbname);
$sql ="select * from players where email!='' and notify=1";
//debug_string("sql",$sql);
$players = MYSQLGetData($link,$sql);
//debug_array("players",$players);
break_mysql_connect($link);

$partydate = date("n/d/Y",nextgamesparty(time()));
//if ($partydate== '2/8/2010') $partydate= "No Party in February!";
$message = sabrinamessage($partydate);
$subject = "The Nelson Games Party";
$from = " \"Sabrina Nelson\" sabrina007@mac.com";

//print $subject."\n";
//print $message."\n";

foreach($players as $player){
    $to=$player['email'];
    print $to."\n";
//  $to="davemenc@gmail.com";
    $pname=$player['playername'];
    $salutation = "Dear $pname,\n";
    $fname=$player['fullname'];
//print $to."  ".$subject." ".$salutation.$fname."\n";
    //mail($to,$subject,$salutation.$message);
}
exit();

function nextgamesparty($timestamp){
    $dateary = getdate($timestamp);//convert timestamp to array
    $mon = $dateary['mon'];//get the month #
    $year = $dateary['year'];//get the year
    $mday = $dateary['mday'];// get the day of month
    //print_r($dateary);
    $thismonthgpday = gameday($mon,$year); // when is the games party this month?
    //print "                      check... $mon/$mday/$year = $thismonthgpday\n";
    if($thismonthgpday<=$mday){ // on or past the gamesparty; go to next month
        $mon+=1;//next month
        $mday=1;// first day
    }
    if($mon>12){ // past the end of the year
        $mon=1; // first month
        $year+=1;// next year
        $mday=1; // first day
    }
    $thismonthgpday = gameday($mon,$year); // when is the games party this month?
    $new_ts = mktime(1,1,1,$mon,$thismonthgpday,$year);//get the new timestamp
    return $new_ts;
}

function gameday($month,$year){
    $timestamp = mktime(1,1,1,$month,1,$year);//time stamp for the first day
    $dateary = getdate($timestamp);
    $wday = $dateary['wday'];//weekday number
    return 14-$wday; // which day is the next saturday
}
function kurtmessage($partydate){
$message=<<<EOF
IMPORTANT: The next Menconi Games Party is at KURT SCHALLITZ's house NOT the usual place. Hence I guess it's the Menconi/Schallitz Games Party...

Dave & Dorita Menconi and Kurt Schallitz would like to invite you to join us for a party at Kurt's home. As always, we'll socialize, play games, share food and generally have a good time. Feel free (but not
obligated) to bring food and drinks to share.

WHEN
Our parties are always on the 2nd Saturday of the month (one to two weeks from when this message is sent out). They officially start at 1 PM and end at 8 PM (please don't come early or stay late).

The next games party will be on $partydate.

WHERE
Kurt's address is
Kurt Schallitz
6546 village drive
Livermore, ca 94551
Google Maps: http://tinyurl.com/4dgkgxj

EOF;
return  $message;
}
function mymessage($partydate){
$message=<<<EOF
NOTE: DO NOT REPLY TO THIS EMAIL OR USE THIS EMAIL ADDRESS IN ANY WAY!

NOTE: Please note the new end time: 8PM.

Dave & Dorita Menconi would like to invite you to join us for a party at our home. As always, we'll socialize, play games, share food and generally have a good time. Feel free to bring food and drinks to sha
re. Dinner is usually served and we ask that people chip in on the cost (usually something like $5).

For more details see http://www.menconi.com/houseparty

WHEN
Our parties are always on the 2nd Saturday of the month (one to two weeks from when this message is sent out). They officially start at 1 PM and end at 8 PM (please don't come early or stay late).

The next games party will be on $partydate.


WHERE
The party is at 357 Spring Valley in Milpitas; this is within a mile of the
intersection of 237 (Calaveras Blvd) and 680 in the South Bay.
Google Map Link: http://tinyurl.com/6esuuo

Please join us at the next party!

The Menconis

PS If you would rather not receive future notices, please let me know.

DIRECTIONS
From the East Bay
South on 880 or 680 to 237
East (Left) on 237 (aka Calaveras Blvd)
Left on Temple Ave
Right on Fair Hill
Right on Spring Valley Ln
We're 357 Spring Valley Ln on the right (4th house from the corner)

From SF
South on 101 to 237
East (Left) on 237 (aka Calaveras Blvd)
Left on Temple Ave
Right on Fair Hill
Right on Spring Valley Ln
We're 357 Spring Valley Ln on the right (4th house from the corner)

From the South
North on 880 (or 17) or 680 (or North on 101 to 680 N and then...) to 237
East (Right) on 237 (aka Calaveras Blvd)
Left on Temple Ave
Right on Fair Hill
Right on Spring Valley Ln
We're 357 Spring Valley Ln on the right (4th house from the corner)

EOF;
return  $message;
}

?>

