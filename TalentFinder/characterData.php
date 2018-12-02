<!-- data / content of the character page -->

<div class='row'>
    <div class='span8'><h3>Language</h3></div>
    <div class='span4'><h3>Actor</h3></div>
</div>
<div class='span12 firstPageListDiv'>

<!-- database query to aquire id's from the heading/name of the character-->
<?php 
    $DB = new DB();
    $cid = $DB->query("select characterid from characters where name = '$heading'");
    $stupid = array_pop($cid);
    $charid = array_pop($stupid);
    ?>

<!-- table to contain the content of the characters, lists the actor and the language performed in-->
<table class="span12 firstPageListTable">
    <?php 
        $lIds = $DB->query("select langid from lookup where charid = $charid");
        for($i = 0; $i<count($lIds);$i++)
        {
        	//Database queries to acquire actors and languages that a character is done in
            $lid = array_pop($lIds[$i]);
            $ln = $DB->query("select name from languages where langid = $lid");
            $dumb = array_pop($ln);
            $lname = array_pop($dumb);
            $aId = $DB->query("select actorid from lookup where langid = $lid and charid = $charid");
            $dum = array_pop($aId);
            $actorId = array_pop($dum);
            $an = $DB->query("select name from actors where actorid = $actorId");
            $du = array_pop($an);
            $actorName = array_pop($du);
          ?>
          <!-- place values into table-->
            <tr onclick="BuildForm()" onmouseover="this.style.background='lightgrey';this.style.cursor='pointer';ids=this.id;" onmouseleave="this.style.background='white'" id=<?php echo $i; ?>>
                <td class='span4'><?php getLangImage($lname); ?></td>
                <td class='span4'><?php echo $lname; ?></td>
                <td class='span4'><?php echo $actorName; ?></td>
            </tr>
  <?php }?>
</table>

<!-- forms to handle clicking, these forms are hidden, when a table row is clicked the form is populated with the appropriate information and submitted to continue to the appropriate next page -->
 <form id='actorForm' action="actorPage.php" method="post">
 <div id="FormHere"></div>
 </form>
 
 <!-- script to handle the population of the hidden forms on a table click -->
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
        AddElement(document.getElementById(ids).cells.item(2).innerHTML,'FormHere');
        document.getElementById('actorForm').submit();  
      }
 </script>

</div>


