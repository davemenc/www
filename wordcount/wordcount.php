<?PHP
/*
    Copyright (c) 2007 Dave Menconi

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/


/**************** INCLUDES ****************/
//include_once("../library/mysql.php");
//include_once("config.php");

/**************** REGEX ****************/
$whitespace="/[ 	\n]+/";
$special1="/[ ~!@#$%^&*()+=-]+/";
$special2="/[ 0987654321`[{}|;:.,<>?\\\"]+/";//"
$special3="/]/";
$special4="/\[/";
$special5="|/|";
/**************** Special CASES ****************/
/**************** PROGRAM ****************/

$PARAMS = array_merge($_POST,$_GET);
//print_r($PARAMS);
$data = $PARAMS['data'];

/*$oneliners=explode("\n",$data);
foreach($oneliners as $line){
	if (strlen($line)<140) print "$line</br>\n";
}
exit;
*/

$data = preg_replace($whitespace," ",$data);// replace all whitespace with spaces
$data = preg_replace($special1," ",$data);// replace all whitespace with spaces
$data = preg_replace($special2," ",$data);// replace all whitespace with spaces
$data = preg_replace($special3," ",$data);// replace all whitespace with spaces
$data = preg_replace($special4," ",$data);// replace all whitespace with spaces
$data = preg_replace($special5," ",$data);// replace all whitespace with spaces
$data = preg_replace($whitespace," ",$data);// replace all whitespace with spaces
$words = explode ( " ",$data);
//print_r($words);
$uniquewords = array();
foreach($words as $word){
	$w=trim(strtolower($word));
	if(!IsCountableWord($w))continue;
	if(array_key_exists($w,$uniquewords))$uniquewords[$w]++;
	else $uniquewords[$w]=1;
}
arsort($uniquewords);
$total=0;
foreach($uniquewords as $word=>$count){
	print "<li>$word	$count\n";
	$total++;
	if($total>40)break;
}

exit();
/**************** FUNCTIONS ****************/
function IsCountableWord($word){
	$common = array("not","what","don't","can","just","know","things","two","ca","inc","by","which","more","than","–","_","year","•","preferred","understanding","activities","a","all","this","needs", "work","deliver","requirements","strong","skills","years", "ensure", "company", "including","meet","needs","employees","ability","high","an","and","are","as","with","have","will","at","be","but","do","few","for","form","go","i","if","in","into","is","it","of","on","or","our","over","that","the","there","to","we","who","why","www","you","your");
	if(strlen($word)<1)return false;
	if(array_search($word,$common)!== false)return false;

	return true;

}
?>