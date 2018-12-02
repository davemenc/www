<!-- content / data for the titles page, displays the characters with a list of all actors in all languages -->
<html>

<?php 
    //queries for ids
    $DB = new DB();
    $tid = $DB->query("select titleid from titles where name = '$heading';");
    $stupid = array_pop($tid);
    $titleId = array_pop($stupid);
	
	$genre = $DB->query("select genre from titles where titleid=$titleId");
	$genre = array_pop($genre);
	$genre = array_pop($genre);
?>

<script language="javascript" type="text/javascript">
//ids keeps track of which table element is hovered over to handle clicking
var ids = 0;
</script>

<div class='row'>
    <div class='span6'><h3>Character</h3></div>
    <div class='span3'><h3>Language</h3></div>
    <div class='span3'><h3>Actor</h3></div>
</div>
<div class='titlePageDivStyle'>

			<!-- setup table -->
      			<table class="span12 titlePageListStyle">
      			   <?php
      			   		//first loop through characters and create a row for each character
	                    $tabId = 0;
			            $characters = $DB->query("select name from characters where titleid=$titleId;");
						
                        for($i = 0; $i < count($characters); $i++)
                        {
                        $charname = array_pop($characters[$i]);
                        ?>                        
      				      <tr>
      				      	<!-- populate primary table -->
      					     <td class="span3 borders" ><?php getCharacterImageL($charname); ?></td>
      					     <td class="span3 borders" ><?php echo $charname ?></td>
      					     <td class="span6 borders" >
      					             <?php
      					             	 //while in the character row create another table where we loop through the actors belonging to that character and the languages they perform in, here are database queries for the actors and languages
                                         $characterId = $DB->query("select characterid from characters where name = '$charname';");
                                         $charId = array_pop($characterId);
                                         $dummy = array_pop($charId);
			                             $actids = $DB->query("select actorid from lookup where titleid=$titleId and charid=$dummy;");
										 //populate table with the information
                                         for($j = 0; $j<count($actids); $j++)
                                         {
                                         	//more queries for actors and languages
                                            $actorid = array_pop($actids[$j]);
											$actorname = $DB->query("select name from actors where actorid=$actorid;");
											                                            
                                            $langid = $DB->query("select langid from lookup where actorid=$actorid and charid=$dummy;");
											$temp = array_pop($langid);
											$langID = array_pop($temp);
											
											$langname = $DB->query("select name from languages where langid = $langID;");
                                            
                                            $lname = array_pop($langname);
                                            $aname = array_pop($actorname);
                                            $lname2 = array_pop($lname);
                                            $aname2 = array_pop($aname);
                                            ?>
                                            <!-- populate secondary table -->
                                            <table>
                                                <tr onclick="BuildForm()"  onmouseover="this.style.background='lightgrey';this.style.cursor='pointer';ids=this.id;" onmouseleave="this.style.background='white'" id=<?php echo $tabId; ?>> 
                                                    <td class = 'span6'><?php getLangImage($lname2); echo " $lname2"; ?></td><td class='span6'><?php echo "$aname2" ?></td>
                                                </tr>
                                            </table>
                                   <?php 
                                        $tabId++;
                                        }?>
                                            
      					     </td>
				     <?php } ?>
      				      </tr>
			      </table>
<!-- hidden form to handle clicking, when a row is clicked the data gets populated here and submitted -->
 <form id='actorForm' action="actorPage.php" method="post">
 <div id="FormHere"></div>
 </form>
 <!-- script to handle populating forms on click -->
 <script language="javascript" type="text/javascript"> 
      
      function AddElement(xValue,formTitle)
      {  
        var theNewElem = document.createElement('input');  
        theNewElem.setAttribute('type','text');
        theNewElem.setAttribute('name','data');
         theNewElem.setAttribute('type','hidden'); 
        theNewElem.setAttribute('value',xValue);  
        document.getElementById(formTitle).appendChild(theNewElem);   
      }  
      function BuildForm()
      {
        AddElement(document.getElementById(ids).cells.item(1).innerHTML,'FormHere');
        document.getElementById('actorForm').submit();  
      }
 </script>

</div>

