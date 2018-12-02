<!-- core of the title page, includes functional files (imagegather, DB) sets up variables, include header content and footer -->
<?php
include('imageGather.php');
$heading = $_POST["data"];
$description = 'Click a an actor to view their profile';
$title = "<img src='images/ShipLogo.png' />";
$edit ="<a href=\"javascript:BuildFormEdit();\"><i class='icon-edit'></i>Edit Page</a>";
$pageType = 3;
include('DB.php');
include('heading.php');
include('titleData.php');
include('footer.php');
?>
