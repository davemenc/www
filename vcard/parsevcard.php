<?php

//read in the URLs
$filename = "publisher.vcf";
$fieldnames = array();
$publishers = readindata($filename,$fieldnames);
//print_r($fieldnames);
print "\n\n++++++++++++++++++++++++++++++\n\n";
foreach($fieldnames as $fieldname=>$count){
	print "$fieldname\t";
	$fieldlist[]=$fieldname;
}
print "\n";
foreach($publishers as $publisher){
	foreach($fieldlist as $field){
		if(isset($publisher[$field]))print $publisher[$field]."\t";
		else print "\t";
	}
	print "\n";
}
print "\n\n++++++++++++++++++++++++++++++\n\n";
print_r($publishers);
exit();
/*************************************************************************/
/**********************************************
 *
 **********************************************/
function readindata($filename,&$fieldnames){
	$item = 0;
	if (($handle = fopen($filename, "rt")) !== FALSE) {
	    while (($data = fgets($handle, 4000)) !== FALSE) {
	    	$i = strpos($data,":");
	    	$key = substr($data,0,$i);
	    	$value = trim(substr($data,$i+1,strlen($data)));
	    	$records[$item][$key]=$value;
	    	if($key=="END" || $key=="END:") $item++;
	    	if(isset($fieldnames[$key]))$fieldnames[$key]++;
	    	else $fieldnames[$key]=1;
	    }
	    fclose($handle);
	}
	return($records);
}
?>