1. Variables

$heading - the title of the page shows up in the grey bar, this is also passed around as actor names, title names, language names, character names to identify what page is what
$description - the description field, shows up under the title, just adds a little explanation to the page
$edit - the edit link, some pages have it some pages don't every page is handled differently
$title - the image on the left top corner of the app, right now its just the ship header on all pages
$pagetype - the type of page

			0 -- actor page
			1 -- character page
			2 -- language page
			3 -- title page

2. Page organization

Most pages are organized in a structure that is the core of the page and then the data

for example -- Actor page: Core - actorPage.php
			   data - actorData.php

The editing pages have an edit form page and a submit handler that goes along with it


3. Database Structure

Titles table

| titleid | name | genre |


Actors table

| actorid | name | description |


Characters table

| characterid | titleid | name |


Language table

|langid | name |


Lookup table

| actorid | titleid | charid | langid |