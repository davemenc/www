<?php
$xml = GET_PLAN_XML();
//$simplexml = simplexml_load_string($xml);
//print_r($simplexml);
//$result = $simplexml->xpath('//name');
//print_r($result);

$depth = array();


$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startElement", "endElement");
$depth[$xml_parser]=0;
//if (!($fp = fopen($file, "r"))) {
//    die("could not open XML input");
//}

//while ($data = fread($fp, 4096)) {
//    if (!xml_parse($xml_parser, $data, feof($fp))) {
			if(!xml_parse($xml_parser, $xml)){
        die(sprintf("XML error: %s at line %d",
                    xml_error_string(xml_get_error_code($xml_parser)),
                    xml_get_current_line_number($xml_parser)));
    }

xml_parser_free($xml_parser);

/**********************************************************************************************************************/
function startElement($parser, $name, $attrs)
{
    global $depth;
    for ($i = 0; $i < $depth[$parser]; $i++) {
        echo "  ";
    }
    echo "$name\n";
    $depth[$parser]++;
}

function endElement($parser, $name)
{
    global $depth;
    $depth[$parser]--;
}
function GET_TEST_XML(){
$xml  = <<<XML
<?xml version="1.0" encoding="ISO-8859-1"?>

<bookstore>

<book>
  <title lang="eng">Harry Potter</title>
  <price>29.99</price>
</book>

<book>
  <title lang="eng">Learning XML</title>
  <price>39.95</price>
</book>

</bookstore>
XML;
return $xml;

}
function GET_PLAN_XML(){
$xml  = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<plans expand="plans">
	<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan"/>
	<plans expand="plan" size="410" max-result="25" start-index="0">
		<plan enabled="true" name="Audio - Sound Stream EX" key="AUDIO-SOUNDSTREAMEX">
				<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/AUDIO-SOUNDSTREAMEX"/>
		</plan>
		<plan enabled="true" name="AVCAP - DEVNET-RC" key="AVCAP-DEVNETRC">
				<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/AVCAP-DEVNETRC"/>
		</plan>
		<plan enabled="true" name="AVCAP - RC" key="AVCAP-RC">
				<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/AVCAP-RC"/>
		</plan>
		<plan enabled="false" name="AVCAP - UI" key="AVCAP-UI">
				<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/AVCAP-UI"/>
		</plan>
		<plan enabled="true" name="AVCAP - WIP" key="AVCAP-WIP">
				<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/AVCAP-WIP"/>
		</plan>
		<plan enabled="true" name="CATWALK - loopback_agent" key="CATWALK-LOOPBACKAGENT">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/CATWALK-LOOPBACKAGENT"/>
		</plan>
		<plan enabled="true" name="CATWALK - WIP" key="CATWALK-WIP">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/CATWALK-WIP"/>
		</plan>
		<plan enabled="true" name="Crash Recorder - SCEI" key="CRASHRECORDER-SCEI">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/CRASHRECORDER-SCEI"/>
		</plan>
		<plan enabled="false" name="Crash Recorder - WIP" key="CRASHRECORDER-WIP">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/CRASHRECORDER-WIP"/>
		</plan>
		<plan enabled="true" name="DART - RC SysTest Win2008 64bit" key="DART-RCSYSWIN200864">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/DART-RCSYSWIN200864"/>
		</plan>
		<plan enabled="false" name="DART - RC SysTest Win7 64bit" key="DART-RCSYSWIN764">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/DART-RCSYSWIN764"/>
		</plan>
		<plan enabled="false" name="DART - RC SysTest WinXP 32bit" key="DART-RCSYSWINXP32">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/DART-RCSYSWINXP32"/>
		</plan>
		<plan enabled="true" name="DART - RCSYS COMPILE" key="DART-RCSYS">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/DART-RCSYS"/>
		</plan>
		<plan enabled="true" name="DART - RCSYS WIN PREP" key="DART-RCSYSWINPREP">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/DART-RCSYSWINPREP"/>
		</plan>
		<plan enabled="false" name="DART - RCSYS WIN2008 32" key="DART-RCSYSWIN200832">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/DART-RCSYSWIN200832"/>
		</plan>
		<plan enabled="false" name="DART - RCSYS WIN7 32" key="DART-RCSYSWIN732">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/DART-RCSYSWIN732"/>
		</plan>
		<plan enabled="false" name="DART - RCSYS WIN7 64 JA" key="DART-RCSYSWIN764JA">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/DART-RCSYSWIN764JA"/>
		</plan>
		<plan enabled="false" name="DART - RCSYS WINXP 64" key="DART-RCSYSWINXP64">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/DART-RCSYSWINXP64"/>
		</plan>
		<plan enabled="false" name="DART - RCSYS WINXP 64 JA" key="DART-RCSYSWINXP64JA">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/DART-RCSYSWINXP64JA"/>
		</plan>
		<plan enabled="true" name="DART - RELEASE" key="DART-RELEASE">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/DART-RELEASE"/>
		</plan>
		<plan enabled="true" name="DART - RR2" key="DART-RR2">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/DART-RR2"/>
		</plan>
		<plan enabled="false" name="DART - Sonar-Release" key="DART-SONARRELEASE">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/DART-SONARRELEASE"/>
		</plan>
		<plan enabled="true" name="DART - WIP" key="DART-WIP">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/DART-WIP"/>
		</plan>
		<plan enabled="true" name="Diagnostic - Diagnostic" key="DIAG-DIAG">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/DIAG-DIAG"/>
		</plan>
		<plan enabled="true" name="libcrashreport.net - WIP libcrashreport.net" key="LIBCRASHREPORTNET-WIPLIBCRASHREPORTNET">
			<link rel="self" href="http://bamboo1.ship.scea.com/bamboo/rest/api/latest/plan/LIBCRASHREPORTNET-WIPLIBCRASHREPORTNET"/>
		</plan>
	</plans>
</plans>
XML;
return $xml;
}
?>
