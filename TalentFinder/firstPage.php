<!-- The data that holds the main page of the application -->

<html>
<script language="javascript" type="text/javascript">
// ids is a variable that stores the id of what element is hovered over, for the process of a click in a table
var ids = 0;
</script>

<!-- JQUery to Filter table for search results -->

<script type="text/javascript">
	$(window).load(function () {
		var $rows = $('#titles tr');
		$('#searchT').keyup(function () {
			var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
			$rows.show().filter(function () {
				var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
				return !~text.indexOf(val);
			}).hide();
		});
	});

	$(window).load(function () {
		var $rows = $('#chars tr');
		$('#searchC').keyup(function () {
			var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
			$rows.show().filter(function () {
				var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
				return !~text.indexOf(val);
			}).hide();
		});
	});

	$(window).load(function () {
		var $rows = $('#lang tr');
		$('#searchL').keyup(function () {
			var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
			$rows.show().filter(function () {
				var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
				return !~text.indexOf(val);
			}).hide();
		});
		$('.tableLeave').mouseout(function (event) {
			$(this).css('background-color', 'white');
		});

		$('.tableLeave').mouseover(function (event) {
			$(this).css('background-color', 'lightgray');
		});
	});

</script>

 <div class="row paddingTop" >
 	<!-- Titles column -->
      	<div class="span4" name = 'titlesRow'>
			<input type="text" class='span4' placeholder="Search Titles" id="searchT">
		    <div class = "firstPageListDiv">
      			<table id = 'titles' class="firstPageListTable">
      			   <?php
      			   		//database queries to find all titles and populate the list
                        $DB = new DB();
                        $recordst = $DB->query("select name from titles;");
                        for($i = 0; $i < count($recordst); $i++)
                        {
                            $element = $recordst[$i];
                            $id = "title"+$i;
                            $titleName = array_pop($element);

							$titleID = $DB->query("select titleid from titles where name = '$titleName';");
							$titleID = array_pop($titleID);
							$titleID = array_pop($titleID);
                   ?>
                               <!-- place data into table -->
      			           <tr class='tableLeave' onclick='BuildFormTitle()'  id=<?php echo $id; ?>>
      						        <td class='span6'>
      							          <?php getTitleImage($titleID); ?>
      						        </td>
      						        <td class='span6'><?php echo $titleName; ?></td>
  					       </tr>
                  <?php } ?>
      			</table>
   			</div>
	    </div>
	   <!-- Characters Column -->
      	<div class="span4" name ='charactersRow'>
      			<input type="text" class='span4' placeholder="Search Characters" id="searchC">
  			<div class = "firstPageListDiv">
      			<table id='chars' class="firstPageListTable">
      			   <?php
      			   		//database queries to find all characters and populate the list
                        $recordsc = $DB->query("select name from characters;");
                        for($i = 0; $i < count($recordsc); $i++)
                        {
                            $element = $recordsc[$i];
                            $charName = array_pop($element);
                            $idc = $i+count($recordst);
                            ?>
                            <!-- place data into table -->
      			               <tr class='tableLeave' onclick="BuildFormCharacter()" onmouseover="this.style.background='lightgrey';this.style.cursor='pointer';ids=this.id;" onmouseleave="this.style.background='white'" id=<?php echo $idc; ?>>
      					         <td class='span4'>
      						            <?php getCharacterImage($charName); ?>
      					         </td>
      					         <td class='span8'><?php echo $charName; ?></td>
      				          </tr>
                  <?php } ?>
      			</table>
  			</div>
	    </div>
	    <!-- language column -->
	    <div class="span4">
      			<input type="text" class='span4' placeholder="Search Languages" id="searchL">
  			<div class = "firstPageListDiv">
      			<table id='lang' class="firstPageListTable">
   			      <?php
   			      		//database queries to find all languages and populate the list
                        $recordsl = $DB->query("select name from languages;");
                        for($i = 0; $i < count($recordsl); $i++)
                        {
                            $element = $recordsl[$i];
                            $langName = array_pop($element);
                            $idl = +$i+count($recordst)+count($recordsc);?>
                            <!-- place into table -->
      			               <tr class='tableLeave' onclick="BuildFormLanguage()" onmouseover="this.style.background='lightgrey';this.style.cursor='pointer';ids=this.id;" onmouseleave="this.style.background='white'" id=<?php echo $idl; ?>>
      					         <td class='span6'>
      						            <?php getLangImage($langName); ?>
      					         </td>
      					         <td class='span6'><?php echo $langName; ?></td>
      				          </tr>
                  <?php } ?>
      			</table>
  			</div>
        </div>
 <!-- Forms to handle clicks of a table row, these forms are hidden because whenever a table is clicked the information from the table row populates this form and then submits to handle which page to go to next -->
 </div>
 <form id='titleForm' action="titlePage.php" method="post">
 <div id="FormHere"></div>
 </form>
 <form id='characterForm' action="characterPage.php" method="post">
 <div id="FormHere1"></div>
 </form>
 <form id='languageForm' action="languagePage.php" method="post">
 <div id="FormHere2"></div>
 </form>
 </div>
 <!-- javascript to handle the table row clicks uses the ids variable to determine what has been clicked and then adds to the appropriate form to be submitted-->
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
      function BuildFormTitle()
      {
        AddElement(document.getElementById(ids).cells.item(1).innerHTML,'FormHere');
        document.getElementById('titleForm').submit();
      }
      function BuildFormCharacter()
      {
        AddElement(document.getElementById(ids).cells.item(1).innerHTML,'FormHere1');
        document.getElementById('characterForm').submit();
      }
      function BuildFormLanguage()
      {
        AddElement(document.getElementById(ids).cells.item(1).innerHTML,'FormHere2');
        document.getElementById('languageForm').submit();
      }

</script>

