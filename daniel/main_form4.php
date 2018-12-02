<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
   <head>
      <title>Wallboard URL list</title>
   </head>
   <body>

	<?php
	
	ini_set ('display_errors', 1);
	error_reporting (E_ALL & ~E_NOTICE);
	
//
// Form Handling
//
	
	$url = $_POST["url"];
	$type = $_POST["type"];
	$time = $_POST["time"];
	$shape = $_POST["shape"];
	$duration = $_POST["duration"];
	$venue = $_POST["venue"];
	$active = $_POST["active"];
	$note = $_POST["note"];
	
//
	
	$chosenline = $_POST['action'];
	
	if (is_numeric($chosenline))
	{
		$chosenline--;
	}
	else 
	{
		switch ($chosenline)
		{
			case "Save":
				print '<p>Save button was clicked... </p>';
				break;
			case "Add":
				print '<p>Add button was clicked...   </p>';
//array_push ($detail, $dataline);
				break;
			default:
//echo "default";
				break;
		}
	}
	
//
// create one dimensional table from CSV
// count table rows
// sort elements low - high
//
	
	$datatable = file ('urlsmini.csv');
	$n = count($datatable); // rows in the table
	asort($datatable);
	
// Test line to see if it's doing it's job
// print_r($datatable);
	
	$datamulti = array();
	
// adds the second dimension to the table
	
	for ($a=0;$a<$n;$a++)
	{
		$dataline = explode(',',$datatable[$a]);
		array_push ($datamulti, $dataline);
	} 
	
// Test array to see if it's doing it's job
// print_r($datamulti);
//
// $datamulti[row][column]
//

//
// Display 2D table in HTML
//
	
	?>
	<table border="1" cellpadding="3" cellspacing="3">
		<tr>
			<td></td>
			<td>URL</td>
			<td>Type</td>
			<td>Time</td>
			<td>Shape</td>
			<td>Duration</td>
			<td>Venue</td>
			<td>Active</td>
			<td>Note</td>
		</tr>
	<?php
	
	$b = 0;
	while($b != $n)
	{
		print '<tr>';
		for($c=0;$c<9;$c++)
		{
			if($c==0)
			{
				$b++;
				print '<td><form action="main_form4.php" method="post"><input type="submit" name="action" value="'.$b.'"/></td>';
				$b--;
			}
			else
				print '<td> '.$datamulti[$b][$c].' </td>';
		}
		print '</tr>';
		$b++;
	}
	print '<tr><p />'; 
	print '</tr><p><input type="submit" name="action" value="Save"/><input type="submit" name="action" value="Add"/></p></form>';
	
	?>
	
	</table>
	
	<?php
//
// Display one line from 2D table in Details Box
//	
	
	$detail = explode (',', $datatable[$chosenline]);
	
// Test line to see if it's doing it's job
//	 print_r($detail);
	
	print '<form><br />';
	
	print 'URL: <input type="text" name="url" value='.$detail[1].' size="100"/><br />';
	
	if ($detail[2] == 'F')
		print 'Type: <select name="type"><option value="F" selected="selected">File</option><option value="S">Stream</option></select>';
	else
		print 'Type: <select name="type"><option value="F">File</option><option value="S" selected="selected">Stream</option></select>';
	
	if ($detail[3] == 'A')
		print'Time: <select name="time"><option value="A" selected="selected">AM</option><option value="P">PM</option><option value="B">both</option></select>';
	elseif ($detail[3] == 'P')
		print'Time: <select name="time"><option value="A">AM</option><option value="P" selected="selected">PM</option><option value="B">both</option></select>';
	else
		print'Time: <select name="time"><option value="A">AM</option><option value="P">PM</option><option value="B" selected="selected">both</option></select>';
	
	if ($detail[4] == '4')
		print'Shape: <select name="shape"><option value="4" selected="selected">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option><option value="0">0</option></select>';
	elseif ($detail[4] == '3')
		print'Shape: <select name="shape"><option value="4">4</option><option value="3" selected="selected">3</option><option value="2">2</option><option value="1">1</option><option value="0">0</option></select>';
	elseif ($detail[4] == '2')
		print'Shape: <select name="shape"><option value="4">4</option><option value="3">3</option><option value="2" selected="selected">2</option><option value="1">1</option><option value="0">0</option></select>';
	elseif ($detail[4] == '1')
		print'Shape: <select name="shape"><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1" selected="selected">1</option><option value="0">0</option></select>';
	else
		print'Shape: <select name="shape"><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option><option value="0" selected="selected">0</option></select>';
	
	print 'Duration: <input type="text" name="duration" value="' . $detail[5] . '" /><br />';
	
	if ($detail[6] == '4')
		print'Venue: <select name="venue"><option value="4" selected="selected">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option><option value="0">0</option></select>';
	elseif ($detail[6] == '3')
		print'Venue: <select name="venue"><option value="4">4</option><option value="3" selected="selected">3</option><option value="2">2</option><option value="1">1</option><option value="0">0</option></select>';
	elseif ($detail[6] == '2')
		print'Venue: <select name="venue"><option value="4">4</option><option value="3">3</option><option value="2" selected="selected">2</option><option value="1">1</option><option value="0">0</option></select>';
	elseif ($detail[6] == '1')
		print'Venue: <select name="venue"><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1" selected="selected">1</option><option value="0">0</option></select>';
	else
		print'Venue: <select name="venue"><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option><option value="0" selected="selected">0</option></select>';
	
	if ($detail[7] == 'Y')
		print 'Active: <select name="active"><option value="Y" selected="selected">Yes</option><option value="N">No</option></select>';
	else
		print 'Active: <select name="active"><option value="Y">Yes</option><option value="N" selected="selected">No</option></select>';
	
	print 'Note: <input type="text" name="note" value="' . $detail[8] . '" size="100"/><br /></form>';
	
//
// SAVE one line from Details Box to 2D table
// update CSV file
//	
	
	
	
//
// ADD one line to 2D table from Details Box
// update CSV file
//
	
	
	?>
   </body>
</html>
