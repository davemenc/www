<?php

/**
 * This PestXML usage test pulls data from the bamboo servers here in TNT
 *
 **/
$user = "tnttvplayer";
$logindomain = "bamboo1.ship.scea.com";
$password = "tv1234";

require_once 'PestXML.php';

$pest = new PestXML('http://bamboo1.ship.scea.com/bamboo/rest/api/latest');
$pest->setupAuth($user , $password);

// Retrieve plans
$plans = $pest->get('/plan');

print_r($plans);
?>
