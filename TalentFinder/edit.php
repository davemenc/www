<!-- This is the core and the content of the edit page, This is when the page type variable is actually used, depending on the page type a different edit form will be shown
	pagetype = 0 --- actor page
	pagetype = 1 --- character page
	pagetype = 2 --- language page
	pagetype = 3 --- title page
	
	Handling of the submission of the edit is done in submitEdit.php
	
	This page also sets up the heading and footer of the the edit page. 
	All editing requires the entry of the key, contained in key.php -->

<?php

//set up core of the page, assign variable values 
$description = "Fill out the form to edit the page";
$edit = "";
$title = "<img src='images/ShipLogo.png' />";
$pageType = $_POST["pageType"];
$pageName = $_POST["pageName"];
$heading = "Edit Page: $pageName";
include('imageGather.php');
include('DB.php');
include('heading.php');

// set up actor editing form
if($pageType == 0)
{ ?>
	<div class='row-fluid paddingTop span12'>
	
	<?php 
	// database queries for ID's from actor name
    $DB = new DB();
    $actID = $DB->query("select actorid from actors where name ='$pageName';");
    $temp = array_pop($actID);
    $aId = array_pop($temp);
    $charIds = $DB->query("select charid from lookup where actorid=$aId;");
	$descrip = $DB->query("select description from actors where actorid=$aId;");
	$descrip = array_pop($descrip);
	$descrip = array_pop($descrip);
    ?>
	
	<!-- set up form for actor editing -->
	<form action='submitEdit.php' method='post' enctype="multipart/form-data">
		<div class = 'row'><label>Actor Name: </label><input type='text' class= 'span6' name='actorName' value='<?php echo $pageName; ?>'></div>
		<div class = 'row'><label>Actor Description: </label><input type='text' class= 'span6' name='description' value='<?php echo $descrip; ?>'></div>
		
		<div class = 'row'><label>Remove Characters (Check and hit update to remove) </label>
			<table class='table'>
				<?php
				//Set up table to remove characters from an actor
				for($i=0;$i<count($charIds);$i++)
    			{
			        $charId = array_pop($charIds[$i]);
			        $charN = $DB->query("select name from characters where characterid = $charId;");
			        $temp2 = array_pop($charN);
			        $cName = array_pop($temp2); ?>
			      	
			      	<tr>
			      		<td class='span6'><input type="checkbox" name='<?php echo $charId; ?>' value='remove' /><?php echo $cName; ?> </td>
			      	</tr>
			      <?php } ?>
			</table>
		</div>
		<!-- rest of form the hidden elements are necessary for processing the submission of the form -->
		<div class = 'row'><label>Upload Audio: </label><input type='file' name ='audio'></div>
		<div class = 'row'><label>Key (enter to authorize edit): </label><input type='text' class= 'span6' name='key'></div>
		<input type='hidden' value='<?php echo $pageName; ?>' name='name' />
		<input type='hidden' value=<?php echo $pageType; ?> name='type' />
		<button class='btn btn-primary' type='submit'>Update Actor!</button>
	</form>
	
</div>
<?php } ?>

<?php 
// setup character editing form, simple form you can only edit the name
if($pageType == 1)
{ ?>
	<div class='row-fluid paddingTop span12'>
	
	<form action='submitEdit.php' method='post'>
		<div class = 'row'><label>Character Name: </label><input type='text' class= 'span6' name='characterName' value='<?php echo $pageName; ?>'></div>
		<div class = 'row'><label>Key (enter to authorize edit): </label><input type='text' class= 'span6' name='key'></div>
		<input type='hidden' value='<?php echo $pageName; ?>' name='name' />
		<input type='hidden' value=<?php echo $pageType; ?> name='type' />
		<button class='btn btn-primary' type='submit'>Update Character!</button>
	</form>
	
</div>
<?php } ?>
<?php 
// setup language editing form, again simple can only edit name
if($pageType == 2)
{ ?>
	<div class='row-fluid paddingTop span12'>
	
	<form action='submitEdit.php' method='post'>
		<div class = 'row'><label>Language Name: </label><input type='text' class= 'span6' name='langName' value='<?php echo $pageName; ?>'></div>
		<div class = 'row'><label>Key (enter to authorize edit): </label><input type='text' class= 'span6' name='key'></div>
		<input type='hidden' value='<?php echo $pageName; ?>' name='name' />
		<input type='hidden' value=<?php echo $pageType; ?> name='type' />
		<button class='btn btn-primary' type='submit'>Update Language!</button>
	</form>
	
</div>
<?php } ?>
<?php 
// setup title editing form, can edit name, genre, and upload a picture for the title
if($pageType == 3)
{ ?>
	<div class='row-fluid paddingTop span12'>
	
	<form action='submitEdit.php' method='post' enctype="multipart/form-data">
		<div class = 'row'><label>Title Name: </label><input type='text' class= 'span6' name='titleName' value='<?php echo $pageName; ?>'></div>
		<div class = 'row'><label>Genre: </label><input type='text' class= 'span6' name='genre'></div>
		<div class = 'row'><label>Upload Picture (please use .jpg): </label><input type='file' name ='pic'></div>
		<div class = 'row'><label>Key (enter to authorize edit): </label><input type='text' class= 'span6' name='key'></div>
		<input type='hidden' value='<?php echo $pageName; ?>' name='name' />
		<input type='hidden' value=<?php echo $pageType; ?> name='type' />
		<button class='btn btn-primary' type='submit'>Update Title!</button>
	</form>
	
</div>
<?php } 

include('footer.php');

?>



