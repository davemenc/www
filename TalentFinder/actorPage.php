<!-- The core of the actor page, includes all functional files (imagegather, Db) a heading and the actor data page then the footer, also assigns values for different required variables, the actor page has an edit value -->
<?php
include('imageGather.php');
include('DB.php');
$heading = $_POST["data"];
$description = 'Actor Profile shows titles, characters and languages for an actor';
$title = "<img src='images/ShipLogo.png' />";
$edit = "<a href='#' onClick=\"BuildFormEdit();\"><i class='icon-edit'></i>Edit Page</a>";
$pageType = 0;
include('heading.php');
include('actorData.php');
include('footer.php');
?>
