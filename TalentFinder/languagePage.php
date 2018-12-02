<!-- core of the language page, sets up variables, loads function files (imagegather, db) loads heading, content and footer -->
<?php
include('imageGather.php');
$heading = $_POST["data"];
$description = 'Click an actor to view their profile';
$title = "<img src='images/ShipLogo.png' />";
$edit = "<a href='#' onClick=\"BuildFormEdit();\"><i class='icon-edit'></i>Edit Page</a>";
$pageType = 2;
include('DB.php');
include('heading.php');
include('languageData.php');
include('footer.php');
?>
