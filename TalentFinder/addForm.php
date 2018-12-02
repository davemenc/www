<!-- form for adding an actor, contains all required fields to add an actor -->

<div class='row-fluid paddingTop span12'>
	
	<form action='submitActor.php' method='post'>
		<div class = 'row'><label>Actor Name: </label><input type='text' class= 'span6' name='actorName'></div>
		<div class = 'row'><label>Title Name: </label><input type='text' class= 'span6' name='titleName'></div>
		<div class = 'row'><label>Character Name: </label><input type='text' class= 'span6' name='charName'></div>
		<div class = 'row'><label>Language: </label><input type='text' class= 'span6' name='langName'></div>
		<div class = 'row'><label>Actor Description: </label><input type='text' class= 'span6' name='description'></div>
		<button class='btn btn-primary' type='submit'>Add Actor!</button>
	</form>
	
</div>
