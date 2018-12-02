
<html>
<head>

	<title>HTML Form</title>
</head>
<body>


<form action="handle_form.php" method="POST">

<fieldset><legend>Enter your URL strand:</legend>

IDX: <input type="text" name="idx" value="" /><br />
URL: <input type="text" name="url" value="" size="100"/><br />
Type: <select name="type">
  <option value="F">File</option>
  <option value="S">Stream</option>
</select>
Time: <select name="time">
  <option value="A">AM</option>
  <option value="P">PM</option>
  <option value="B">both</option>
</select>
Shape: <select name="shape">
  <option value="0">0</option>
  <option value="1">1</option>
  <option value="2">2</option>
  <option value="3">3</option>
  <option value="4">4</option>
</select>
Duration: <input type="text" name="duration" value="" /><br />
Venue: <select name="venue">
  <option value="0">0</option>
  <option value="1">1</option>
  <option value="2">2</option>
  <option value="3">3</option>
  <option value="4">4</option>
</select> 
Active: <select name="active">
  <option value="Y">Y</option>
  <option value="N">N</option>
</select><br />
Note: <input type="text" name="Note" value="" size="100"/><br />

</fieldset>

<div align="center"><input type="submit" name="submit" value="Submit Information" /></div>

</form><!-- End of Form -->

</body>
</html>
