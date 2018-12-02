<!-- contains all functions for getting image and audio paths -->

<?php
// return the images for titles as html tags by the actor id
function getTitleImage($id)
{
	echo  "<img src='images/title/$id.jpg' width='100' height='118' />";
}
//return the path for a title image as a string DEPRECATED, DO NOT USE, ORGANZATION HAS CHANGED
function getTitleImagePath($name)
{
	echo "\"images/title/$name/title.png\"";
}
//return the image for a character CHARACTER IMAGES ARE NOT SETUP WILL DISPLAY BLANK
function getCharacterImage($name)
{
	$DB = new DB();
	$tId = $DB->query("select titleid from characters where name = '$name'");
	$temp = array_pop($tId);
	$titleID = array_pop($temp);
	$tname = $DB->query("select name from titles where titleid = $titleID");
	$temp2 = array_pop($tname);
	$titleName = array_pop($temp2);
	// KK - BEGIN
	// echo "<img src='images/title/$titleName/$name.png' width='50' height = '50' />";
	echo "<img src='images/actor/noprofmal.jpg' width='50' height ='50' />";
	// KK - END
}
//returns a larger version of the character image
function getCharacterImageL($name)
{
	$DB = new DB();
	$tId = $DB->query("select titleid from characters where name = '$name'");
	$temp = array_pop($tId);
	$titleID = array_pop($temp);
	$tname = $DB->query("select name from titles where titleid = $titleID");
	$temp2 = array_pop($tname);
	$titleName = array_pop($temp2);
	// KK - BEGIN
	echo "<img src='images/title/$titleName/$name.png' width='150' height = '150' />";
	// KK - END
}
//returns the image of a language as html tags
function getLangImage($name)
{	
	echo "<img src='images/lang/$name.png' width='50' height ='50' />";
}
//returns the image of an actor as an html tags IMAGES FOR ACTORS NOT PROVIDED, RIGHT NOW ONLY RETRIEVES BLANK IMAGE
function getActorImage($name)
{
	echo "<img src='images/actor/noprofmal.jpg' width='150' height ='150' />";
}
//returns the path of the audio clip associated with an actor through the id
function getActorAudio($id)
{
	echo "audio/$id/clip.mp3";
}
// not used at all
function getCharacterAudio($name)
{
	echo "audio/Kalimba.mp3";
}
?>
