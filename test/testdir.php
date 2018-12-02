<?php
$rootdir = "c:/wamp/www/";
$htmlroot = "http://localhost/";
$handle = opendir($rootdir);
while($name = readdir($handle)) {
	if ($name=="." || $name=="..") continue;
	if(is_dir($rootdir.$name)) $dirs[]=$htmlroot.$name;
	if(is_file($rootdir.$name)) $files[]=$htmlroot.$name;
}
closedir($handle);
print "<h2>Directories In $rootdir</h2>";
foreach ($dirs as $dir){
	print "<a href=\"$dir\">$dir</a></br>";
}
print "<h2>Files in $rootdir</h2>";
foreach ($files as $file){
	print "<a href=\"$file\">$file</a></br>";
}
?>