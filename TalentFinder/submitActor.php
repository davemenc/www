<!-- handles the submission of a new actor
	
	Checks - 1. if new actor
			2. if new character
			3. if new title
			4. if new language
			
	Once those checks are identified, the appropriate fields are added to the database depending on whether tables need new rows
	
	At the end the appropriate data is inserted into the lookup table -->
<?php

include('DB.php');
//retrieve names from post
$actorName = $_POST["actorName"];
$titleName = $_POST["titleName"];
$charName = $_POST["charName"];
$langName = $_POST["langName"];
$descrip = $_POST["description"];

$DB = new DB();

//setup new booleans
$newAct = false;
$newTitle = false;
$newChar = false;
$newLang = false;

//identifies new actors by using a count on the database to see whether that actor exists
$temp = $DB->query("select count(*) from actors where name='$actorName';");
$temp2 = array_pop($temp);
$checkAct = array_pop($temp2);
if($checkAct == 0)
{
	$newAct = true;
}
//identifies new titles
$temp = $DB->query("select count(*) from titles where name='$titleName';");
$temp2 = array_pop($temp);
$checkTitle = array_pop($temp2);
if($checkTitle == 0)
{
	$newTitle = true;
}
//identifies new characters
$temp = $DB->query("select count(*) from characters where name='$charName';");
$temp2 = array_pop($temp);
$checkChar = array_pop($temp2);
if($checkChar == 0)
{
	$newChar = true;
}
//identifies new language
$temp = $DB->query("select count(*) from languages where name='$langName';");
$temp2 = array_pop($temp);
$checkLang = array_pop($temp2);
if($checkLang == 0)
{
	$newLang = true;
}

$actorID;
$titleID;
$charID;
$langID;

echo $newAct;
echo $newChar;
echo $newTitle;
echo $newLang;

//handles new actors, if new actor is true then set the appropriate id and add to the actors, if not true find the id 
//of that actor in the actor table
if($newAct)
{
	$temp = $DB->query("select count(*) as actorID from actors;");
	$temp2 = array_pop($temp);
	$actorID = array_pop($temp2) + 1;
	$DB->insert("insert into actors values($actorID,'$actorName','$descrip');");
}
else 
{
	$temp = $DB->query("select actorid from actors where name='$actorName';");
	$temp2 = array_pop($temp);
	$actorID = array_pop($temp2);
}
//handles new titles, same as actors
if($newTitle)
{
	$temp = $DB->query("select count(*) as titleID from titles;");
	$temp2 = array_pop($temp);
	$titleID = array_pop($temp2) + 1;
	$DB->insert("insert into titles values($titleID,'$titleName');");
}
else 
{
	$temp = $DB->query("select titleid from titles where name='$titleName';");
	$temp2 = array_pop($temp);
	$titleID = array_pop($temp2);	
}
//handles new characters
if($newChar)
{
	$temp = $DB->query("select count(*) as charID from characters;");
	$temp2 = array_pop($temp);
	$charID = array_pop($temp2) + 1;
	$DB->insert("insert into characters values($charID,$titleID,'$charName');");
}
else 
{
	$temp = $DB->query("select characterid from characters where name='$charName';");
	$temp2 = array_pop($temp);
	$charID = array_pop($temp2);	
}
//handles new languages
if($newLang)
{
	$temp = $DB->query("select count(*) as langID from languages;");
	$temp2 = array_pop($temp);
	$langID = array_pop($temp2) + 1;
	$DB->insert("insert into languages values($langID,'$langName');");
}
else 
{
	$temp = $DB->query("select langid from languages where name='$langName';");
	$temp2 = array_pop($temp);
	$langID = array_pop($temp2);	
}
//now that all the ids are set, insert them into the lookup table
$DB->insert("insert into lookup values($actorID,$titleID,$charID,$langID);");

//setup variables
$heading = "Actor Added";
$description = "you have succesfully added your actor, return home to view titles";
$image ='';
$title = "<img src='images/ShipLogo.png' />";
$edit='';

include('heading.php')

?>


