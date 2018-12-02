<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
   <head>
      <title>Main URL list</title>
   </head>
   <body>

	<?php

//
// enable error checking
//
	ini_set ('display_errors', 1);
	error_reporting (E_ALL & ~E_NOTICE);
	
//
// initialize variables
//
	$data = file ('urls.csv');
	$b = 0;
	$n = count($data);

	while ($b != $n)
	{
		$newdata[$b] = explode (',', $data[$b]);
		$b++;
	}
	print '<form action="view.php" method="add">';
	print "\t\t" . '<td><button type="submit" name="submit" value="add">Add New Line</button></td></form>' . "\n";
// print a table
	print '<table>' . "\n\n";

// start table rows
	for ($b = 0; $b != $n; $b++)
	{
		print "\t" . '<tr>' . "\n";
// start table columns
// form with buttons
//print '<form action="view.php?line='.$b.'" method="post">';

		if ($b == 0)
		{
			print "\t\t" . '<td></td>' . "\n"; // spacer for header row
			print "\t\t" . '<td></td>' . "\n"; // spacer for header row
			print "\t\t" . '<td></td>' . "\n"; // spacer for header row
		}
		else
		{
			print "\t\t" . '<td></td>' . "\n";
			print "\t\t" . '<td><form action="view.php?line='.$b.'&submit=view" method="post"><button type="submit" name="submit" value="view">View</button></td></form>' . "\n"; 
			print "\t\t" . '<td><form action="view.php?line='.$b.'&submit=edit" method="post"><button type="submit" name="submit" value="edit">Edit</button></td></form>' . "\n";
		}

		for ($d=0;$d<=8;$d++)
		{

			if ($d != 0)
			{
				print "\t\t" . '<td ';
// handle URL alignment
				if ($d == 1)
					$align = "left";
				else
					$align = "center";
				print 'align="' . $align . '">' . $newdata[$b][$d] . '</td>' . "\n";
			}
		}
		print "\t" . '</tr>' . "\n";	
	}
	print '</table>' . "\n";

	?>

   </body>
</html>
