<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
   <head>
      <title>Display/edit page for URL Line</title>
   </head>
   <body>

	<?php
	ini_set ('display_errors', 1);
	error_reporting (E_ALL & ~E_NOTICE);

	$line2view = $_GET[line];
	$submit = $_GET[submit];

	$CSVArray = file ('urls.csv');

	$lineAsString = $CSVArray[$line2view];
	settype($lineAsString, "string");

	function viewForm($lineAsString)
	{
		 $funcStrand = explode(',', $lineAsString);
		
		print '<fieldset><legend>Enter your URL strand:</legend> IDX: <input type="text" name="idx" value="' . $funcStrand[0] . '" /><br />';

		print 'URL: <input type="text" name="url" value="' . $funcStrand[1] . '" size="100"/><br />';

		if ($funcStrand[2] == 'F')
			print 'Type: <select name="type"><option value="F" selected="selected">File</option><option value="S">Stream</option></select>';
		else
			print 'Type: <select name="type"><option value="F">File</option><option value="S" selected="selected">Stream</option></select>';

		if ($funcStrand[3] == 'A')
			print'Time: <select name="time"><option value="A" selected="selected">AM</option><option value="P">PM</option><option value="B">both</option></select>';
		elseif ($funcStrand[3] == 'P')
			print'Time: <select name="time"><option value="A">AM</option><option value="P" selected="selected">PM</option><option value="B">both</option></select>';
		else
			print'Time: <select name="time"><option value="A">AM</option><option value="P">PM</option><option value="B" selected="selected">both</option></select>';

		if ($funcStrand[4] == '4')
			print'Shape: <select name="shape"><option value="4" selected="selected">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option><option value="0">0</option></select>';
		elseif ($funcStrand[4] == '3')
			print'Shape: <select name="shape"><option value="4">4</option><option value="3" selected="selected">3</option><option value="2">2</option><option value="1">1</option><option value="0">0</option></select>';
		elseif ($funcStrand[4] == '2')
			print'Shape: <select name="shape"><option value="4">4</option><option value="3">3</option><option value="2" selected="selected">2</option><option value="1">1</option><option value="0">0</option></select>';
		elseif ($funcStrand[4] == '1')
			print'Shape: <select name="shape"><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1" selected="selected">1</option><option value="0">0</option></select>';
		else
			print'Shape: <select name="shape"><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option><option value="0" selected="selected">0</option></select>';

		print 'Duration: <input type="text" name="duration" value="' . $funcStrand[5] . '" /><br />';

		if ($funcStrand[6] == '4')
			print'Venue: <select name="venue"><option value="4" selected="selected">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option><option value="0">0</option></select>';
		elseif ($funcStrand[6] == '3')
			print'Venue: <select name="venue"><option value="4">4</option><option value="3" selected="selected">3</option><option value="2">2</option><option value="1">1</option><option value="0">0</option></select>';
		elseif ($funcStrand[6] == '2')
			print'Venue: <select name="venue"><option value="4">4</option><option value="3">3</option><option value="2" selected="selected">2</option><option value="1">1</option><option value="0">0</option></select>';
		elseif ($funcStrand[6] == '1')
			print'Venue: <select name="venue"><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1" selected="selected">1</option><option value="0">0</option></select>';
		else
			print'Venue: <select name="venue"><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option><option value="0" selected="selected">0</option></select>';

		if ($funcStrand[7] == 'Y')
			print 'Active: <select name="active"><option value="Y" selected="selected">Yes</option><option value="N">No</option></select>';
		else
			print 'Active: <select name="active"><option value="Y">Yes</option><option value="N" selected="selected">No</option></select>';

		print 'Note: <input type="text" name="note" value="' . $funcStrand[8] . '" size="100"/><br /></fieldset>';

	}
	
	if ($submit == 'view')
	{
		print '<form action="mainForm.php" method="POST">';
		
		viewForm($lineAsString);

		print '<div align="center"><input type="submit" name="submit" value="Return to list" /></div></form>';
	}
	
	elseif ($submit == 'edit')
	{
		print '<form action="handle_form.php" method="POST">';
		
		viewForm($lineAsString);

		print '<div align="center"><input type="submit" name="submit" value="Save Changes" /></div></form>';
		print '<form action="mainForm.php" method="POST"><div align="center"><input type="submit" name="submit" value="Cancel" /></div></form>';
	}
	
	else
	{
		print '<form action="handle_form.php" method="POST">';
		
		$arrayAsString = array
		(
			"idx" => "000", "url" => "", "type" => "", "time" => "", "shape" => "", "duration" => "", "venue" => "", "active" => "", "note" => "",
		);
		$lineAsString = implode (',',$arrayAsString);
		viewForm($lineAsString);

		print '<div align="center"><input type="submit" name="submit" value="Save Changes" /></div></form>';
		print '<form action="mainForm.php" method="POST"><div align="center"><input type="submit" name="submit" value="Cancel" /></div></form>';
	}

	?>

   </body>
</html>
