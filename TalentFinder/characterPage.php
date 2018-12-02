<!-- core of the character page, includes all functional files, sets required variables, includes heading, content contained in characterData and the footer -->

<?php
include('imageGather.php');
include('DB.php');
$heading = $_POST["data"];
$title = "<img src='images/ShipLogo.png' />";
$DB = new DB();
$titleId = $DB->query("select titleid from characters where name = '$heading'");
$temp = array_pop($titleId);
$tID = array_pop($temp);
$name = $DB-> query("select name from titles where titleid=$tID");
$temp2 = array_pop($name);
$description = array_pop($temp2);
$edit ="<a href='#' onClick=\"BuildFormEdit();\"><i class='icon-edit'></i>Edit Page</a>";
$pageType = 1;
include('heading.php');
include('characterData.php');
include('footer.php');
?>
