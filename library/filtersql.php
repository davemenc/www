<?php
/******************************************************************
 * FilterSQL
   ******************************************************************/
function FilterSQL($s){
        debug_string("FilterSQL(s)");
        debug_array("s",$s);
        $result = array();
        foreach($s as $key=>$value){
                debug_string("key",$key);
                debug_string("value",$value);
                $filtered = $value;
                $filtered = str_replace("\\","\\\\",$filtered);
                $filtered = str_replace("\"","\\\"",$filtered);
                $filtered = str_replace("'","\'",$filtered);
                $filtered = str_replace("\n","\\\n",$filtered);
                $filtered = str_replace("\r","\\\r",$filtered);
                $filtered = str_replace("\x1a","\\\x1a",$filtered);
                $filtered = str_replace("\x00","\\\x00",$filtered);
                $result[$key] = $filtered;
                debug_string("filtered value",$result[$key]);
        }
        //$result = str_replace("'","\'",$s);
        //$result = mysql_real_escape_string ($s);
        debug_array("result",$result);
    return $result;
}
?>
