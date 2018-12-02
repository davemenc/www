<!-- core of the add actor page, this page sets up the adding form of the actor, include the function files imagegather and db, include the header the content is contained in addForm and then the footer also assign values to required variables -->

<?php
$heading = "Add Talent";
$description = "Please fill out the form to add a new actor, character, or game. If you are adding an actor to an existing game or character, please spell the name correctly to avoid duplicates";
$image ='';
$edit = '';
$title = "<img src='images/ShipLogo.png' />";
include('imageGather.php');
include('DB.php');
include("heading.php");
include("addForm.php");
include('footer.php');
?>
