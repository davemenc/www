<?php
function display_header($title, $css){
$header = <<<EOF
<!DOCTYPE html>
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <meta name="description" content="Game Credits">
   <meta name="ROBOTS" content="noindex,nofollow">
   <meta name="revisit-after" content="30 days">

   <meta name="author" content="Dave Menconi">

   <meta name="rating" content="PG-13">
    <Title>Game Credit Form</title>
    <link rel="stylesheet" href="form.css" type="text/css">
</head>
<body  >
EOF;
return $header;
}
?>
