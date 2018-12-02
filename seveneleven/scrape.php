<?php
// Include the library
include('simple_html_dom.php');

// Create DOM from URL
$html = file_get_html('http://slashdot.org/');

$ret = $html->find(".topic",0);
$topic=$ret->id;
$title = str_replace("topic","title", $topic);

$ret = $html->find("#$title",0);
$url = $ret->a;
print gettype($url)."\n";
if($url)print "true\n";else print "false\n";
print "$topic / $title / $url\n";



exit();
$html = str_get_html('<div id="hello">Hello</div><div id="world">World</div>');
 var_dump($html);
exit();

// Retrieve the DOM from a given URL
$url = "http://www.zerocater.com/sony/";
$url = "http://localhost/testmenu/testmenu.html";
$url = "http://www.google.com/";
$html = file_get_html($url);

$e= $html->find('img');
print_r($e);
exit();
foreach($html->find('overview-tab') as $e){
	print $e->innertext."\n";
	print 1;
}
foreach($html->find('vendor-name') as $e){
	print 2;
	print $e->innertext."\n";
}
foreach($html->find('meal-is-today') as $e)
	print $e->innnertext."\n";
foreach($html->find('vendor-description') as $e)
	print $e->innertext."\n";
exit();
// Find all "A" tags and print their HREFs
foreach($html->find('a') as $e)
    echo $e->href . '<br>';

// Retrieve all images and print their SRCs
foreach($html->find('img') as $e)
    echo $e->src . '<br>';

// Find all images, print their text with the "<>" included
foreach($html->find('img') as $e)
    echo $e->outertext . '<br>';

// Find the DIV tag with an id of "myId"
foreach($html->find('div#myId') as $e)
    echo $e->innertext . '<br>';

// Find all SPAN tags that have a class of "myClass"
foreach($html->find('span.myClass') as $e)
    echo $e->outertext . '<br>';

// Find all TD tags with "align=center"
foreach($html->find('td[align=center]') as $e)
    echo $e->innertext . '<br>';

// Extract all text from a given cell
echo $html->find('td[align="center"]', 1)->plaintext.'<br><hr>';

/*************************************
 * get_url_contents
 * Input: $url -- valid url for the web
 * Return: the HTML from that URL
 * Side effects: none
 ***************************************/
function get_url_contents($url){
        $crl = curl_init();
        $timeout = 5;
        curl_setopt ($crl, CURLOPT_URL,$url);
        curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
        $ret = curl_exec($crl);
        curl_close($crl);
        return $ret;
}
?>