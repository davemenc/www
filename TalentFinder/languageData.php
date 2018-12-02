<!-- content / data of the language page, creates a list of all the actors belonging to a language -->

<div class="span12"><h3>Actors</h3></div> 
<div class='span12 firstPageListDiv'>

<?php

//database queries to find ids 
    $DB = new DB();
    $lid = $DB->query("select langid from languages where name = '$heading'");
    $stupid = array_pop($lid);
    $langid = array_pop($stupid);
    ?>


<!-- setup table for actors -->
<table class="span12 firstPageListTable">
    
    <?php 
    		//database queries to find the actors in the language
            $aIds = $DB->query("select actorid from lookup where langid = $langid");
            for($j = 0; $j<count($aIds); $j++)
            {
                $actorId = array_pop($aIds[$j]);
                $aname = $DB->query("select name from actors where actorid = $actorId;");
                $dummy = array_pop($aname);
                $actorname = array_pop($dummy);
                ?>
                <!-- insert data into table -->
                <tr class = 'span12' onclick="BuildForm()" onmouseover="this.style.background='lightgrey';this.style.cursor='pointer';ids=this.id;" onmouseleave="this.style.background='white'" id=<?php echo $j; ?> >
                    <td class = 'span2'><?php getActorImage($actorname); ?></td><td class='span2 textAlign'><?php echo $actorname; ?></td> <td class='span8'> </td></tr>
    <?php   }?>
</table>

<!-- form to handle clicking, hidden because when a click occurs the appropriate data is posted to this form and then submitted -->
 <form id='actorForm' action="actorPage.php" method="post">
 <div id="FormHere"></div>
 </form>
 <!-- script to handle populating form on a click -->
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

