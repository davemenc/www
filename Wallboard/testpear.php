<?php
echo "<link rel='stylesheet' type='text/css' href='sample.css'>";

// Include Highlighter class
require_once("Text/Highlighter.php");
//require_once("C:/wamp/bin/php/php5.3.5/PEAR/Text/Highlighter.php");

// This is the code we want to display
$code = "<?php
// This is a test page for PEAR Text_Highlighter package
\$message = \"Hello, world!\";

echo \$message;
?>";

// What to display - PHP code
$what = "php";

// Define the class
$highlighter =& Text_Highlighter::factory($what);

// Call highlight method to display the code to web browser
echo $highlighter->highlight($code);
?>