<!-- heading for the application, used in every page, sets up the grey area at the top, loads twitter bootstrap css, sets up header for all the html -->

<!DOCTYPE html>

<html>

<head>

<!-- bootstrap setup -->
<meta charset="utf-8">
    <title>Talent Finder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

	<script src="jwplayer/jwplayer.js" type="text/javascript"></script>

    <!-- Le styles -->
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
	<link href="css.css" rel="stylesheet">
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <!-- jquery load -->
    <script src="http://code.jquery.com/jquery-1.7.1.js" type="text/javascript"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>
 
 </head>

<body>

<!-- start content -->
<div class="containter-fluid span12">
	
	<!-- setup grey area on top -->
	<div class='row-fluid rowStyle'>
		<div class='span2'>
			<?php
		echo "<h1>$title</h1>";
		?>
		</div>
		<div class= 'span10'>
		 <?php
		echo "<h1>$heading</h1>";
		echo "<p>$description</p>"; ?>
		<!-- setup link area -->
		<h6><a href='index.php'><i class='icon-home'></i>Home</a>   <a href='mailto:khwaab.dave@playstation.sony.com'><i class='icon-envelope'></i>Email Question</a> <a href='addActor.php'><i class='icon-plus'></i>Add Actor</a><?php echo $edit; ?></h6>
		</div>
	</div>
	
	<!-- form for handling the click of the edit button -->
	<form id='editForm' name='editForm' action="edit.php" method="post">
 <div id="editHere"></div>
 </form>
 
 <!-- script for populating form on click -->
 <script language="javascript" type="text/javascript"> 
      
      function AddElementEdit(xValue,formTitle,name)
      {  
        var theNewElem = document.createElement('input');  
        theNewElem.setAttribute('type','text');
        theNewElem.setAttribute('name',name);
        theNewElem.setAttribute('type','hidden'); 
        theNewElem.setAttribute('value',xValue);  
        document.getElementById(formTitle).appendChild(theNewElem);   
      }  
      function BuildFormEdit()
      {
        AddElementEdit('<?php echo $heading; ?>','editHere','pageName');
        AddElementEdit('<?php echo $pageType; ?>','editHere','pageType');
        document.getElementById('editForm').submit();  
      }
 </script>
 
 

