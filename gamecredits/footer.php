<?php
function display_footer($versionmsg="",$copyright=""){
$footer=<<<EOF
   <hr >$versionmsg<hr >$copyright
   </body>
</html>
EOF;
return $footer;
}
?>
