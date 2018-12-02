<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
   <head>
      <title>Trying to display ONLY a strand</title>
   </head>
   <body>

<?php


ini_set ('display_errors', 1);
error_reporting (E_ALL & ~E_NOTICE);

$strand = '140,http://www.wow.com,F,A,2,3:23,3,Y,';

$y = explode (',', $strand);

print '<form action="flub.php" method="POST"> <fieldset><legend>Enter your URL strand:</legend> IDX: <input type="text" name="idx" value="' . $y[0] . '" /><br />';

print 'URL: <input type="text" name="url" value="' . $y[1] . '" size="100"/><br />';

if ($y[2] == 'F')
	print 'Type: <select name="type"><option value="F">File</option></select>';
else
	print 'Type: <select name="type"><option value="S">Stream</option></select>';

if ($y[3] == 'A')
	print'Time: <select name="time"><option value="A">AM</option></select>';
elseif ($y[3] == 'P')
	print'Time: <select name="time"><option value="P">PM</option></select>';
else
	print'Time: <select name="time"><option value="B">both</option></select>';

if ($y[4] == '4')
	print'Shape: <select name="shape"><option value="4">4</option></select>';
elseif ($y[4] == '3')
	print'Shape: <select name="shape"><option value="3">3</option></select>';
elseif ($y[4] == '2')
	print'Shape: <select name="shape"><option value="2">2</option></select>';
elseif ($y[4] == '1')
	print'Shape: <select name="shape"><option value="1">1</option></select>';
else
	print'Shape: <select name="shape"><option value="0">0</option></select>';

print 'Duration: <input type="text" name="duration" value="' . $y[5] . '" /><br />';

if ($y[6] == '4')
	print'Venue: <select name="venue"><option value="4">4</option></select>';
elseif ($y[6] == '3')
	print'Venue: <select name="venue"><option value="3">3</option></select>';
elseif ($y[6] == '2')
	print'Venue: <select name="venue"><option value="2">2</option></select>';
elseif ($y[6] == '1')
	print'Venue: <select name="venue"><option value="1">1</option></select>';
else
	print'Venue: <select name="venue"><option value="0">0</option></select>';

if ($y[7] == 'F')
	print 'Active: <select name="active"><option value="Y">Yes</option></select>';
else
	print 'Active: <select name="active"><option value="N">No</option></select>';

print 'Note: <input type="text" name="Note" value="' . $y[8] . '" size="100"/><br /></fieldset><div align="center"><input type="submit" name="submit" value="Return to list" /></div></form>';
?>

   </body>
</html>
