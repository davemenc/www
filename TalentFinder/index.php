<!-- first page of the application, core, sets up some variables, include functional files (imagegather and db) and header footer etc.. 
	
	PLEASE READ THE DEV README FOR EXPENATION OF BASIS VARIABLES AND THE DATABASE ORGANIZATION-->

<?php
$heading = "Talent Finder";
$description = "Welcome to the talent finder, please find your actors below through our search mechanism";
$edit = "";
$title = "<img src='images/ShipLogo.png' />";
$pageType = '';
include('imageGather.php');
include('DB.php');
include("heading.php");
include("firstPage.php");
include('footer.php');
?>
