<?php
/*
Copyright (c) 2009 Dave Menconi

*/
//initialize variables
$datafilename = "spiritexercises.tab";

// set some more variables based on the arguments
$logfilename = $datafilename.".log";
$fieldnames = array("webpage","todoitem","importance");

//read the file in
$row = 0;
if (($handle = fopen($datafilename, "rt")) !== FALSE) {
    while (($data = fgets($handle, 1000)) !== FALSE) {
        $exercises[]=trim($data);
        $row++;
    }
    fclose($handle);
}
shuffle($exercises);
print "<h1>".$exercises[0]."</h1>\n";
exit();

?>
