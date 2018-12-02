
<html>
<head>

	<title>Form Feedback</title>
</head>
<body>
<?php
ini_set ('display_errors', 1);
error_reporting (E_ALL & ~E_NOTICE);
$data = file ('urls.csv');
$n = count($data);
// print $n . '<br />' . "\n";
for ($a=0; $a!=$n; $a++)
{
	$data2[$a] = explode(',',$data[$a]);	
}
//print_r ($data2);
print '<table>' . "\n\n";
// start table rows
for ($b = 0; $b != $n; $b++)
{
	print "\t" . '<tr>' . "\n";
// start table column
	for ($d=0;$d<=8;$d++)
	{
		print "\t\t" . '<td>' . $data2[$b][$d] . '</td>' . "\n";
	}
	print "\t" . '</tr>' . "\n";
}
print '</table>' . "\n";

$idx = (string) $n;
$idx = sprintf('%03d', $idx);

//$idx = str_pad($idx, 3, '0', STR_PAD_LEFT);
print'<p> this is a formatting test: ' . $idx . ' </p>';

//
// Changes multidimensional into single array
//
/*
for ($c = 0; $c != $n; $c++)
{	
	$stringdata[] = implode (',',$data2[$c]);	
}
print_r ($stringdata);
*/
/*
for ($c = 0; $c != $n; $c++)
{	
	$stringdata[] = implode (',',$data2[$c]);	
}
$csvfile = implode ("\r\n",$stringdata);
print $csvfile;

$fp = fopen ('test.csv','w');

fwrite ($fp, $csvfile);
fclose ($fp);
*/
?>
</body>
</html>
