<html>
<head>
	<title>Handling form data, reporting change in URL list</title>
</head>
<body>
	<?php
	ini_set ('display_errors', 1);
	error_reporting (E_ALL & ~E_NOTICE);

//
// Retrieve Variables!
//
	$idx = $_POST[idx];
	$url = $_POST[url];
	$type = $_POST[type];
	$time = $_POST[time];
	$shape = $_POST[shape];
	$duration = $_POST[duration];
	$venue = $_POST[venue];
	$active = $_POST[active];
	$note = $_POST[note];

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
		}
//
//
	$paramArray = array($idx,$url,$type,$time,$shape,$duration,$venue,$active);
	
	foreach($paramArray as $value)
	{
		if (strlen($value) > 0) 
		{
			$value = stripslashes($value);
		} 
		else 
		{ 
			$value = NULL;
			$flag = TRUE;
			echo "<p><b>You seem to have left out an important field. </b></p>";
		}
	}
	unset($value);

	if (strlen($note) > 0) 
	{
		$note = stripslashes($note);
	}
//
// If everything was filled out, print the message.
//
	if ($flag == FALSE) 
	{
		$strand = array ($idx,$url,$type,$time, $shape, $duration, $venue, $active, $note);
		$strandstring = implode (',', $strand);
		$data = file ('urls.csv');
		$n = count($data);
		while ($c != $n)
		{
			$data2 = explode(',',$data[$c]);
			if ($idx == $data2[0]) //overwrite $idx, tattle	
			{
				$data[$c] = $strandstring . "\r\n";
				print '<p>Overwrote index: ' . $data2[0] . '</p>';
			}
			if ($idx == '000')
			{
				$idx = (string) $n;
				$strand[0] = sprintf('%03d', $idx);			
				$strandstring = implode (',', $strand);			
				$data[] = $strandstring . "\r\n";
				print '<p>Added new index: ' . $data2[0] . '</p>';
				$n = count($data);			
			}
			$c++;
		}
		$csvfile = implode ("",$data);

		$fp = fopen ('urls.csv','w');

		fwrite ($fp, $csvfile);
		fclose ($fp);
		print '<form action="mainForm.php" method="POST"><div align="center"><input type="submit" name="submit" value="Return to List" /></div></form>';
	}
	?>
</body>
</html>
