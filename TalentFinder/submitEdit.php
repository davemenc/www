<!-- handles the submission of all the edit forms does different things based on the page type
	pagetype = 0 -- actor edit
	pagetype = 1 -- character edit
	pagetype = 2 -- language edit
	pagetype = 3 -- title edit
	
-->
	
<?php
include('key.php');
include('DB.php');
$enteredKey = $_POST["key"];

error_reporting(E_ERROR | E_WARNING | E_PARSE);

//CHECK KEY, IF KEY NOT THE SAME AS HARD KEY THEN FAIL
if(strcmp($key, $enteredKey)==0)
{
	//setup variables for header
	$heading = "Edit Success";
	$description = "Your edit was successful please click home to return to the application";
	$edit = "";
	$title = "<img src='images/ShipLogo.png' />";
	
	$pageType = $_POST["type"];
	$name = $_POST["name"];
	
	
	
	$DB = new DB();
	//handle actor edit
	if($pageType == 0)
	{
		//handle name change
		$newName = $_POST["actorName"];
		$newDescrip = $_POST["description"];
		$DB->insert("update actors set name='$newName', description='$newDescrip' where name='$name';");
		
		//lookup characters to remove
	    $actID = $DB->query("select actorid from actors where name ='$newName';");
	    $temp = array_pop($actID);
	    $aId = array_pop($temp);
	    $charIds = $DB->query("select charid from lookup where actorid=$aId;");

		//identify which characters were checked and remove them from lookup
		for($i=0;$i<count($charIds);$i++)
		{
	        $charId = array_pop($charIds[$i]);
			$checkbox = $_POST[$charId];
			if(strcmp($checkbox,'remove')==0)
			{
				//remove checked character from lookup
				$DB->insert("delete from lookup where actorid=$aId and charid=$charId;");
				
				//check if lookup still has any references to that character if not remove from character table also
				$bool = $DB->query("select count(*) as condition from lookup where charid=$charId ;");
				$bool = array_pop($bool);
				$bool = array_pop($bool);
				if($bool == 0)
				{
					$DB->insert("delete from characters where characterid=$charId;");
				}
			}
		}
		//copy in uploaded audio for audio clips
		$audio = $_FILES['audio']['name'];
		if($audio)
		{
			$audio_name = "clip.mp3";
			$path = "audio/$aId/$audio_name";
			mkdir("audio/$aId");
			$copied = move_uploaded_file($_FILES['audio']['tmp_name'],$path);
		}
		
	}
	//handle character edit, only name
	if($pageType == 1)
	{
		$newName = $_POST["characterName"];
		$DB->insert("update characters set name='$newName' where name='$name';");
	}
	//handle language edit, only name
	if($pageType == 2)
	{
		$newName = $_POST["langName"];
		$DB->insert("update languages set name='$newName' where name='$name';");
	}
	//handle title edit, name , genre, picture
	if($pageType == 3)
	{
		//update name and genre
		$titleID = $DB->query("select titleid from titles where name = '$name';");
								$titleID = array_pop($titleID);
								$titleID = array_pop($titleID);
		$newName = $_POST["titleName"];
		$genre = $_POST["genre"];
		$DB->insert("update titles set name='$newName', genre ='$genre' where name='$name';");
		
		//update image from upload
		$image = $_FILES['pic']['name'];
		if($image)
		{
			$image_name = "$titleID.jpg";
			$path = "images/title/$image_name";
			$copied = copy($_FILES['pic']['tmp_name'],$path);
		}
			
	}
	
}
//if key fails display this
else 
{
	$heading = "Edit Failed";
	$description = "Your key didn't match the master key, you do not have authorization to edit, please check your key and try again";
	$edit = "";
	$title = "<img src='images/ShipLogo.png' />";
}

//setup header, include files for function
include('heading.php');

include('imageGather.php');


include('footer.php');
?>
