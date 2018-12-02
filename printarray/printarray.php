<?php

//initialize variables
$a=1;
$b=2;
$c=3;
$d=4;
$e=5;
$f="one";
$g="two";
$h="three";
$i="four";
$j="five";

$AR_A=array($a,$b,$c,$d,$e);
$AR_B=array($f,$g,$h,$i,$j);
$AR_C=array($f=>$a,$g=>$b,$h=>$c,$i=>$d,$j=>$e);
$AR_D=array($AR_A,$AR_B,$AR_C);
$AR_E=array($f=>$AR_A,$g=>$AR_B,$h=>$AR_C,$i=>$AR_D);
$AR_F=array($f=>$AR_A,$g=>$AR_B,$h=>$AR_C,$i=>$AR_D,$j=>$AR_E);

print "AR_A\n";print_r($AR_A);
//print "AR_B\n";print_r($AR_B);
//print "AR_C\n";print_r($AR_C);
//print "AR_D\n";print_r($AR_D);
//print "AR_E\n";print_r($AR_E);
//print "AR_F\n";print_r($AR_F);

//print_var_value("A",$a);
//print_var_value("F",$f);
//print_var_value("G",$g);
//print_var_value("J",$j);
print_var_value("AR_A",$AR_A);
//print_var_value("AR_B",$AR_B);
//print_var_value("AR_C",$AR_C);

exit();
/*********************************************************************/
function print_var_value ($description,$var){
print "$description:\n";
print_element($var);
}
/**********************************************
 *
 **********************************************/
function print_element($element,$key=""){
	print "************* print_element($element,$key)\n";
	if (is_array($element)){
		foreach($element as $newkey=>$newel) {
			print "\t";
			print_element($newel,$newkey);
		}
	} else	if($key!="") print "[$key] => $element \n";
			else print"\t$element\n";
}

?>
