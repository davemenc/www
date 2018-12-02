<!-- contains the data / content of the actor page -->

<div class='span12 firstPageListDiv'>

<!-- database queries to aquire ID's from the name of the actor passed through the post (stored in heading)-->
<?php 
    $DB = new DB();
    $actID = $DB->query("select actorid from actors where name ='$heading';");
    $temp = array_pop($actID);
    $aId = array_pop($temp);
    $charIds = $DB->query("select charid from lookup where actorid=$aId;");
    ?>
<!-- start HTML content -->
<div class='row-fluid paddingTop'>
	<div class = 'span2'>
		<?php getActorImage($heading); ?>
	</div>
	<div class='span10'>
		<!-- table for the description and audio bits -->
		<table>
			<tr>
				<td class='span3'>Description - </td><td class='span9'><?php $description = $DB->query("select description from actors where actorid=$aId"); $temp = array_pop($description); $desc = array_pop($temp); echo $desc; ?></td>
			</tr>
			<tr>
				<td class='span3'> Audio Sample - </td>
				<td class='span9'> 
					<script type='text/javascript' src='jwplayer/jwplayer.js'></script>
 					<div id='mediaspace'>This text will be replaced</div>
 					<!-- audio files -->
					<script type='text/javascript'>
						  jwplayer('mediaspace').setup({
						    'flashplayer': 'jwplayer/player.swf',
						    'duration': '33',
						    'file': '<?php getActorAudio($aId); ?>',
						    'controlbar': 'bottom',
						    'width': '470',
						    'height': '24'
						  });
					</script>
				</td>
			</tr>
		</table>
	</div>
</div>
<!-- table for titles the actor has done with sony -->
<h2> Work with Sony </h2>

<div class='row-fluid'>
    <div class='span4'><h3>Title</h3></div>
    <div class='span4'><h3>Character</h3></div>
    <div class='span4'><h3>Language</h3></div>
</div>
<!-- the actual table for the content -->
<table class = 'table table-striped'>
    <?php
    for($i=0;$i<count($charIds);$i++)
    {
    	//Database queries to find appropriate information about the actor, characters, titles, languages, and other information to populate the table
        $charId = array_pop($charIds[$i]);
        $charN = $DB->query("select name from characters where characterid = $charId;");
        $temp2 = array_pop($charN);
        $cName = array_pop($temp2);
        $titleId = $DB->query("select titleid from characters where characterid = $charId;");
        $temp3 = array_pop($titleId);
        $tId = array_pop($temp3);
        $titleName = $DB->query("select name from titles where titleid = $tId;");
        $temp4 = array_pop($titleName);
        $tName = array_pop($temp4);
        $langId = $DB->query("select langid from lookup where charid = $charId and actorid = $aId;");
        $temp5 = array_pop($langId);
        $lId = array_pop($temp5);
        $langName = $DB->query("select name from languages where langid = $lId;");
        $temp6 = array_pop($langName);
        $lName = array_pop($temp6);
        ?>
    <!-- place data into table-->
    <tr class='textAlign'>
        <td class='span2'><?php getTitleImage($tId); ?> </td><td class = 'span2'>    <?php echo  $tName; ?>    </td>
        <td class='span1'><?php getCharacterImage($cName); ?> </td><td class = 'span3'>    <?php echo  $cName; ?>    </td>
        <td class='span1'><?php getLangImage($lName); ?> </td><td class = 'span3'>    <?php echo  $lName; ?>    </td>
    </tr>
<?php } ?>
</table>






</div>

