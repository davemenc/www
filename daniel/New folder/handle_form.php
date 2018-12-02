<html>
<head>
	<title>Form Feedback</title>
</head>
<body>
<?php
ini_set ('display_errors', 1);
error_reporting (E_ALL & ~E_NOTICE);

$flag = FALSE;
//
//

$data = file ('urls.csv');
	$n = count($data);
	for ($f=0;$f!=$n;$f++)
	{
		$data2 = explode(',',$data[$f]);
		if ($data[0] != 'IDX')
			{}		
		else{
			$idx = (string) $n;
			$strand[0] = sprintf('%03d', $idx);			
			$strandstring = implode (',', $strand);			
			$data[] = $strandstring;
		}
	}/*
	$csvfile = implode ("",$data);

	$fp = fopen ('urls.csv','w');

	fwrite ($fp, $csvfile);
	fclose ($fp);
*/
//
//

// Check $name and strip any slashes:
if (strlen($idx) > 0) 
{
	$idx = stripslashes($idx);
} 
else 
{ // If no name was entered...
	$idx = NULL;
	$flag = TRUE;
	echo '<p><b>You forgot to enter your idx</b></p>';
}
if (strlen($url) > 0) 
{
	$url = stripslashes($url);
}
else 
{ // If no name was entered...
	$url = NULL;
	$flag = TRUE;
	echo '<p><b>You forgot to enter your url</b></p>';
}
if (strlen($type) > 0) 
{
	$type = stripslashes($type);
}
else 
{ // If no name was entered...
	$type = NULL;
	$flag = TRUE;
	echo '<p><b>You forgot to enter your type</b></p>';
}
if (strlen($time) > 0) 
{
	$time = stripslashes($time);
} 
else 
{ // If no name was entered...
	$time = NULL;
	$flag = TRUE;
	echo '<p><b>You forgot to enter your time</b></p>';
}
if (strlen($shape) > 0) 
{
	$shape = stripslashes($shape);
} 
else 
{ // If no name was entered...
	$shape = NULL;
	$flag = TRUE;
	echo '<p><b>You forgot to enter your type</b></p>';
}
if (strlen($duration) > 0) 
{
	$duration = stripslashes($duration);
} 
else 
{ // If no name was entered...
	$duration = NULL;
	$flag = TRUE;
	echo '<p><b>You forgot to enter your duration</b></p>';
}
if (strlen($venue) > 0) 
{
	$venue = stripslashes($venue);
} 
else 
{ // If no name was entered...
	$venue = NULL;
	$flag = TRUE;
	echo '<p><b>You forgot to enter your venue</b></p>';
}
if (strlen($active) > 0) 
{
	$active = stripslashes($active);
} 
else 
{ // If no name was entered...
	$active = NULL;
	$flag = TRUE;
	echo '<p><b>You forgot to enter your activity</b></p>';
}
if (strlen($Note) > 0) 
{
	$Note = stripslashes($Note);
	$Note = $Note/* . "\n"*/;
}
//
// If everything was filled out, print the message.
//
if ($flag == FALSE) 
{
	$strand = array ($idx,$url,$type,$time, $shape, $duration, $venue, $active, $Note);
	$strandstring = implode (',', $strand);
	$data = file ('urls.csv');
	$n = count($data);
	while ($c != $n)
	{
		$data2 = explode(',',$data[$c]);
		if ($idx == $data2[0]) //overwrite $idx, tattle	
		{
			$data[$c] = $strandstring . " ";
			print '<p>Overwrote index: ' . $data2[0] . '</p>';
		}
		if ($idx == '000')
		{
			$idx = (string) $n;
			$strand[0] = sprintf('%03d', $idx);			
			$strandstring = implode (',', $strand);			
			$data[] = $strandstring/* . " "*/;
			print '<p>Added new index: ' . $data2[0] . '</p>';
			$n = count($data);			
		}
		$c++;
	}
	$csvfile = implode ("",$data);

	$fp = fopen ('urls.csv','w');

	fwrite ($fp, $csvfile);
	fclose ($fp);
	print '<form action="flub.php" method="POST"><div align="center"><input type="submit" name="submit" value="Return to List" /></div></form>';
}
?>
</body>
</html>
