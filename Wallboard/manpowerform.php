<?php
/*
        Copyright 2005 Dave Menconi
   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at
       http://www.apache.org/licenses/LICENSE-2.0
   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/
$PARAMS = array_merge($_POST,$_GET);
// set the mode
if (isset($PARAMS['mode'])) $mode=$PARAMS['mode'];
else $mode = "displayform";
switch($mode){
	case "parseform":
		parseform($PARAMS);
		break;
	case "displayform":
	default:
		displayform();
}
exit();
//************ FUNCTIONS ***********************/
function parseform($parms){
//	print "parseform($parms)<br/><hr>\n";
	$Months = array ('M0'=>"NA",'M1'=>'Jan', 'M2'=>'Feb', 'M3'=>'Mar', 'M4'=>'Apr', 'M5'=>'May', 'M6'=>'Jun', 'M7'=>'Jul', 'M8'=>'Aug', 'M9'=>'Sep', 'M10'=>'Oct', 'M11'=>'Nov', 'M12'=>'Dec');
	$Durations=array('D0'=>"NA","D1"=>'1',  "D2"=>'2',  "D3"=>'3',  "D4"=>'4',  "D5"=>'5',  "D6"=>'6',  "D7"=>'7',  "D8"=>'8',  "D9"=>'9',  "D10"=>'10',  "D11"=>'11',  "D12"=>'12',  "D13"=>'13',  "D14"=>'14',  "D15"=>'15',  "D16"=>'16',  "D17"=>'17',  "D18"=>'18',  "D19"=>'19',  "D20"=>'20');
	$Projects = array('P0'=>"NA","P1"=>'3rd Party T&M', "P2"=>'ARB/Standards', "P3"=>'ATF2', "P4"=>'ATF3', "P5"=>'Audio', "P6"=>'Audio NG', "P7"=>'Audio PS3/PSP', "P8"=>'Audio Support', "P9"=>'Docs', "P10"=>'DTE', "P11"=>'DTE AVCAP', "P12"=>'DTE BinXML', "P13"=>'DTE DART', "P14"=>'DTE libs', "P15"=>'DTE Recap', "P16"=>'DTE Rec-Playback', "P17"=>'DTE Support', "P18"=>'DTE SyncLaunch', "P19"=>'Dtrace', "P20"=>'Dtrace Tools (Reswatch)', "P21"=>'FIOS', "P22"=>'FIOS2', "P23"=>'G&A / HR', "P24"=>'GameTech', "P25"=>'GameTech Support', "P26"=>'GTC', "P27"=>'Imogen', "P28"=>'Japanese Translation', "P29"=>'Level Editor', "P30"=>'Live Edit/Hub', "P31"=>'LLVM', "P32"=>'Metrics', "P33"=>'NGH', "P34"=>'SCEA Dev HW', "P35"=>'SCEA Intranet', "P36"=>'SHIP', "P37"=>'SHIP Alfresco', "P38"=>'SHIP Bamboo/CI', "P39"=>'SHIP Confluence', "P40"=>'SHIP EAGL', "P41"=>'SHIP Jira/Greenhopper', "P42"=>'SHIP Portal', "P43"=>'SHIP SCMs', "P44"=>'SHIP Search/Fisheye', "P45"=>'SHIP SourceForge', "P46"=>'SHIP SSO', "P47"=>'SHIP Support', "P48"=>'SHIP SysEng', "P49"=>'SHIP User Directory', "P50"=>'SHIP WSR', "P51"=>'SLED/LUA', "P52"=>'StateMachine', "P53"=>'SystemTech', "P54"=>'SystemTech Support', "P55"=>'THC', "P56"=>'Tradeshows / Edu', "P57"=>'VITA', "P58"=>'WWS Framework', "P59"=>'Zeus');
	$Jobs = array( 'J0'=>"NA","J1"=>'Docs', "J2"=>'G&A', "J3"=>'HR', "J4"=>'Mix', "J5"=>'PTO', "J6"=>'SCEI', "J7"=>'Software', "J8"=>'Support', "J9"=>'Sys Eng', "J10"=>'Test');
	$Clients = array('C0'=>"NA", "C1"=>'Audio',  "C2"=>'Bend',  "C3"=>'DTE',  "C4"=>'EatSleepPlay',  "C5"=>'EyeToy',  "C6"=>'FPQA',  "C7"=>'GFPQA',  "C8"=>'GTG',  "C9"=>'Guerrilla',  "C10"=>'Insomniac',  "C11"=>'Liverpool',  "C12"=>'London Studio',  "C13"=>'PD Sound',  "C14"=>'Play',  "C15"=>'Rika Toyoda',  "C16"=>'Zindagi',  "C17"=>'San Diego',  "C18"=>'Santa Monica Studio',  "C19"=>'SCE',  "C20"=>'SCEA',  "C21"=>'SCEE',  "C22"=>'SCEI',  "C23"=>'WWS_SDK',  "C24"=>'SOE',  "C25"=>'Telemetry',  "C26"=>'That Game Company',  "C27"=>'TNT',  "C28"=>'VASG',  "C29"=>'ModNation',  "C30"=>'WWS',  "C31"=>'Zindagi',  "C32"=>'Zipper',  "C33"=>'Other');

	$skip = array (1,1,1,1,1);

	foreach($parms as $key=>$value){
		switch($key){
			case "mode":
				break;
			case "name":
				$name = $value;
				break;
			case "month":
				$month = $Months[$value];
				break;
			case "days0":
				if ($value!="D0"){
					$skip[0]=0;
					$duration[0] = $Durations[$value];
				}
				break;
			case "days1":
				if ($value!="D0"){
					$skip[1]=0;
					$duration[1] = $Durations[$value];
				}
				break;
			case "days2":
				if ($value!="D0"){
					$skip[2]=0;
					$duration[2] = $Durations[$value];
				}
				break;
			case "days3":
				if ($value!="D0"){
					$skip[3]=0;
					$duration[3] = $Durations[$value];
				}
				break;
			case "days4":
				if ($value!="D0"){
					$skip[4]=0;
					$duration[4] = $Durations[$value];
				}
				break;
			case "Job0":
				$job[0]=$Jobs[$value];
				break;
			case "Job1":
				$job[1]=$Jobs[$value];
				break;
			case "Job2":
				$job[2]=$Jobs[$value];
				break;
			case "Job3":
				$job[3]=$Jobs[$value];
				break;
			case "Job4":
				$job[4]=$Jobs[$value];
				break;
			case "pclient0":
				$client[0] = $Clients[$value];
				break;
			case "pclient1":
				$client[1] = $Clients[$value];
				break;
			case "pclient2":
				$client[2] = $Clients[$value];
			case "pclient3":
				$client[3] = $Clients[$value];
				break;
			case "pclient4":
				$client[4] = $Clients[$value];
				break;
			case "project0":
				$project[0] = $Projects[$value];
				break;
			case "project1":
				$project[1] = $Projects[$value];
				break;
			case "project2":
				$project[2] = $Projects[$value];
				break;
			case "project3":
				$project[3] = $Projects[$value];
				break;
			case "project4":
				$project[4] = $Projects[$value];
				break;
			default:
				print "$key not found in switch!<br/>\n";
		}// switch
	}
	$fname = "tempout.txt";
	$fout = fopen($fname, "at");
	if($fout===false)
	{
		print "File $fname could not be opened for appending.";
		exit(-1);
	}
	print "<table border=1>\n";
	for ($i=0;$i<5;$i++){
		if (!$skip[$i]){
			//print "<tr><td>".$name."</td><td>".$month."</td><td>".$duration[$i]."</td><td>".$project[$i]."</td><td>".$job[$i]."</td><td>".$client[$i]."</tr>";
			$line = $name."\t".$month."\t".$duration[$i]."\t".$project[$i]."\t".$job[$i]."\t".$client[$i]."\n";
			fwrite ($fout, $line);
		}
	}
	print "</table>";
	fclose($fout);
	displaythankyou();

}
function displaythankyou(){
	echo <<< ENDTHANKYOU
	<html>
	<head>
	   <meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1">
	   <meta name="description" content="Menconi's Todo List">
	   <meta name="DISTRIBUTION" content="IU">
	   <meta name="ROBOTS" content="noindex,nofollow">
	   <meta name="revisit-after" content="30 days">
	   <meta name="author" content="Dave Menconi">
	   <meta name="rating" content="PG-13">
	   <Title>Manpower Data Entry Page</title>
	</head>

	<body bgcolor="#dFdFfF" >
	<h2> <center>Manpower data successfully entered.</h2>
	<br><br><b> <center>Thank You!</b>
	</body>
	</html>

ENDTHANKYOU;
}
function displayform(){
echo <<< ENDFORM
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1">
   <meta name="description" content="Menconi's Todo List">
   <meta name="DISTRIBUTION" content="IU">
   <meta name="ROBOTS" content="noindex,nofollow">
   <meta name="revisit-after" content="30 days">
   <meta name="author" content="Dave Menconi">
   <meta name="rating" content="PG-13">
   <Title>Manpower Data Entry Page</title>
</head>

<body bgcolor="#dFdFfF" >
<h1> Enter Manpower Data</h1>
<form action = "manpowerform.php" method=post>
<input type=hidden name="mode" value="parseform">
<b>Name:&nbsp; </b> <input type=text name="name" size=25 value="Dave Menconi"><br>
<b>Month: </b>
<select  name="month">
<option value="M1"selected>Jan
<option value="M2">Feb
<option value="M3">Mar
<option value="M4">Apr
<option value="M5">May
<option value="M6">Jun
<option value="M7">Jul
<option value="M8">Aug
<option value="M9">Sep
<option value="M10">Oct
<option value="M11">Nov
<option value="M12">Dec
</select><br>


<table>
<tr><td><b>Days </b></td><td><b>Project</b></td><td><b>Job</b></td><td><b>Primary Client</b></td></tr>
<tr> <td><select  name="days0">
<option value="D1">1
<option value="D2">2
<option value="D3">3
<option value="D4">4
<option value="D5">5
<option value="D6">6
<option value="D7">7
<option value="D8">8
<option value="D9">9
<option value="D10">10
<option value="D11">11
<option value="D12">12
<option value="D13">13
<option value="D14">14
<option value="D15">15
<option value="D16">16
<option value="D17">17
<option value="D18">18
<option value="D19">19
<option value="D20" selected>20
</select></td>

<td><select  name="project0">
<option value="P0" selected>Select Project
<option value="P1">3rd Party T&M
<option value="P2">ARB/Standards
<option value="P3">ATF2
<option value="P4">ATF3
<option value="P5">Audio
<option value="P6">Audio NG
<option value="P7">Audio PS3/PSP
<option value="P8">Audio Support
<option value="P9">Docs
<option value="P10">DTE
<option value="P11">DTE AVCAP
<option value="P12">DTE BinXML
<option value="P13">DTE DART
<option value="P14">DTE libs
<option value="P15">DTE Recap
<option value="P16">DTE Rec-Playback
<option value="P17">DTE Support
<option value="P18">DTE SyncLaunch
<option value="P19">Dtrace
<option value="P20">Dtrace Tools (Reswatch)
<option value="P21">FIOS
<option value="P22">FIOS2
<option value="P23">G&A / HR
<option value="P24">GameTech
<option value="P25">GameTech Support
<option value="P26">GTC
<option value="P27">Imogen
<option value="P28">Japanese Translation
<option value="P29">Level Editor
<option value="P30">Live Edit/Hub
<option value="P31">LLVM
<option value="P32">Metrics
<option value="P33">NGH
<option value="P34">SCEA Dev HW
<option value="P35">SCEA Intranet
<option value="P36">SHIP
<option value="P37">SHIP Alfresco
<option value="P38">SHIP Bamboo/CI
<option value="P39">SHIP Confluence
<option value="P40">SHIP EAGL
<option value="P41">SHIP Jira/Greenhopper
<option value="P42">SHIP Portal
<option value="P43">SHIP SCMs
<option value="P44">SHIP Search/Fisheye
<option value="P45">SHIP SourceForge
<option value="P46">SHIP SSO
<option value="P47">SHIP Support
<option value="P48">SHIP SysEng
<option value="P49">SHIP User Directory
<option value="P50">SHIP WSR
<option value="P51">SLED/LUA
<option value="P52">StateMachine
<option value="P53">SystemTech
<option value="P54">SystemTech Support
<option value="P55">THC
<option value="P56">Tradeshows / Edu
<option value="P57">VITA
<option value="P58">WWS Framework
<option value="P59">Zeus
</select></td>

<td><select  name="Job0">
<option value="J0" selected>Select Job
<option value="J1" >Docs
<option value="J2" >G&A
<option value="J3" >HR
<option value="J4" >Mix
<option value="J5" >PTO
<option value="J6" >SCEI
<option value="J7" >Software
<option value="J8" >Support
<option value="J9" >Sys Eng
<option value="J10" >Test
</select></td>


<td><select  name="pclient0">
<option value="C0" selected>Select Primary Client
<option value="C1">Audio
<option value="C2">Bend
<option value="C3">DTE
<option value="C4">EatSleepPlay
<option value="C5">EyeToy
<option value="C6">FPQA
<option value="C7">GFPQA
<option value="C8">GTG
<option value="C9">Guerrilla
<option value="C10">Insomniac
<option value="C11">Liverpool
<option value="C12">London Studio
<option value="C13">PD Sound
<option value="C14">Play
<option value="C15">Rika Toyoda
<option value="C16">Zindagi
<option value="C17">San Diego
<option value="C18">Santa Monica Studio
<option value="C19">SCE
<option value="C20">SCEA
<option value="C21">SCEE
<option value="C22">SCEI
<option value="C23">WWS_SDK
<option value="C24">SOE
<option value="C25">Telemetry
<option value="C26">That Game Company
<option value="C27">TNT
<option value="C28">VASG
<option value="C29">ModNation
<option value="C30">WWS
<option value="C31">Zindagi
<option value="C32">Zipper
<option value="C33">Other
</select></td>

</tr>
<tr> <td><select  name="days1">
<option value="D0" selected>Select Days
<option value="D1">1
<option value="D2">2
<option value="D3">3
<option value="D4">4
<option value="D5">5
<option value="D6">6
<option value="D7">7
<option value="D8">8
<option value="D9">9
<option value="D10">10
<option value="D11">11
<option value="D12">12
<option value="D13">13
<option value="D14">14
<option value="D15">15
<option value="D16">16
<option value="D17">17
<option value="D18">18
<option value="D19">19
<option value="D20" >20
</select></td>

<td><select  name="project1">
<option value="P0" selected>Select Project
<option value="P1">3rd Party T&M
<option value="P2">ARB/Standards
<option value="P3">ATF2
<option value="P4">ATF3
<option value="P5">Audio
<option value="P6">Audio NG
<option value="P7">Audio PS3/PSP
<option value="P8">Audio Support
<option value="P9">Docs
<option value="P10">DTE
<option value="P11">DTE AVCAP
<option value="P12">DTE BinXML
<option value="P13">DTE DART
<option value="P14">DTE libs
<option value="P15">DTE Recap
<option value="P16">DTE Rec-Playback
<option value="P17">DTE Support
<option value="P18">DTE SyncLaunch
<option value="P19">Dtrace
<option value="P20">Dtrace Tools (Reswatch)
<option value="P21">FIOS
<option value="P22">FIOS2
<option value="P234">G&A / HR
<option value="P24">GameTech
<option value="P25">GameTech Support
<option value="P26">GTC
<option value="P27">Imogen
<option value="P28">Japanese Translation
<option value="P29">Level Editor
<option value="P30">Live Edit/Hub
<option value="P31">LLVM
<option value="P32">Metrics
<option value="P33">NGH
<option value="P34">SCEA Dev HW
<option value="P35">SCEA Intranet
<option value="P36">SHIP
<option value="P37">SHIP Alfresco
<option value="P38">SHIP Bamboo/CI
<option value="P39">SHIP Confluence
<option value="P40">SHIP EAGL
<option value="P41">SHIP Jira/Greenhopper
<option value="P42">SHIP Portal
<option value="P43">SHIP SCMs
<option value="P44">SHIP Search/Fisheye
<option value="P45">SHIP SourceForge
<option value="P46">SHIP SSO
<option value="P47">SHIP Support
<option value="P48">SHIP SysEng
<option value="P49">SHIP User Directory
<option value="P50">SHIP WSR
<option value="P51">SLED/LUA
<option value="P52">StateMachine
<option value="P53">SystemTech
<option value="P54">SystemTech Support
<option value="P55">THC
<option value="P56">Tradeshows / Edu
<option value="P57">VITA
<option value="P58">WWS Framework
<option value="P59">Zeus
</select></td>

<td><select  name="Job1">
<option value="J0" selected>Select Job
<option value="J1" >Docs
<option value="J2" >G&A
<option value="J3" >HR
<option value="J4" >Mix
<option value="J5" >PTO
<option value="J6" >SCEI
<option value="J7" >Software
<option value="J8" >Support
<option value="J9" >Sys Eng
<option value="J10" >Test
</select></td>

<td><select  name="pclient1">
<option value="C0" selected>Select Primary Client
<option value="C1">Audio
<option value="C2">Bend
<option value="C3">DTE
<option value="C4">EatSleepPlay
<option value="C5">EyeToy
<option value="C6">FPQA
<option value="C7">GFPQA
<option value="C8">GTG
<option value="C9">Guerrilla
<option value="C10">Insomniac
<option value="C11">Liverpool
<option value="C12">London Studio
<option value="C13">PD Sound
<option value="C14">Play
<option value="C15">Rika Toyoda
<option value="C16">Zindagi
<option value="C17">San Diego
<option value="C18">Santa Monica Studio
<option value="C19">SCE
<option value="C20">SCEA
<option value="C21">SCEE
<option value="C22">SCEI
<option value="C23">WWS_SDK
<option value="C24">SOE
<option value="C25">Telemetry
<option value="C26">That Game Company
<option value="C27">TNT
<option value="C28">VASG
<option value="C29">ModNation
<option value="C30">WWS
<option value="C31">Zindagi
<option value="C32">Zipper
<option value="C33">Other
</select></td>

</tr>
<tr> <td><select  name="days2">
<option value="D0" selected>Select Days
<option value="D1">1
<option value="D2">2
<option value="D3">3
<option value="D4">4
<option value="D5">5
<option value="D6">6
<option value="D7">7
<option value="D8">8
<option value="D9">9
<option value="D10">10
<option value="D11">11
<option value="D12">12
<option value="D13">13
<option value="D14">14
<option value="D15">15
<option value="D16">16
<option value="D17">17
<option value="D18">18
<option value="D19">19
<option value="D20" >20
</select></td>

<td><select  name="project2">
<option value="P0" selected>Select Project
<option value="P1">3rd Party T&M
<option value="P2">ARB/Standards
<option value="P3">ATF2
<option value="P4">ATF3
<option value="P5">Audio
<option value="P6">Audio NG
<option value="P7">Audio PS3/PSP
<option value="P8">Audio Support
<option value="P9">Docs
<option value="P10">DTE
<option value="P11">DTE AVCAP
<option value="P12">DTE BinXML
<option value="P13">DTE DART
<option value="P14">DTE libs
<option value="P15">DTE Recap
<option value="P16">DTE Rec-Playback
<option value="P17">DTE Support
<option value="P18">DTE SyncLaunch
<option value="P19">Dtrace
<option value="P20">Dtrace Tools (Reswatch)
<option value="P21">FIOS
<option value="P22">FIOS2
<option value="P234">G&A / HR
<option value="P24">GameTech
<option value="P25">GameTech Support
<option value="P26">GTC
<option value="P27">Imogen
<option value="P28">Japanese Translation
<option value="P29">Level Editor
<option value="P30">Live Edit/Hub
<option value="P31">LLVM
<option value="P32">Metrics
<option value="P33">NGH
<option value="P34">SCEA Dev HW
<option value="P35">SCEA Intranet
<option value="P36">SHIP
<option value="P37">SHIP Alfresco
<option value="P38">SHIP Bamboo/CI
<option value="P39">SHIP Confluence
<option value="P40">SHIP EAGL
<option value="P41">SHIP Jira/Greenhopper
<option value="P42">SHIP Portal
<option value="P43">SHIP SCMs
<option value="P44">SHIP Search/Fisheye
<option value="P45">SHIP SourceForge
<option value="P46">SHIP SSO
<option value="P47">SHIP Support
<option value="P48">SHIP SysEng
<option value="P49">SHIP User Directory
<option value="P50">SHIP WSR
<option value="P51">SLED/LUA
<option value="P52">StateMachine
<option value="P53">SystemTech
<option value="P54">SystemTech Support
<option value="P55">THC
<option value="P56">Tradeshows / Edu
<option value="P57">VITA
<option value="P58">WWS Framework
<option value="P59">Zeus
</select></td>

<td><select  name="Job2">
<option value="J0" selected>Select Job
<option value="J1" >Docs
<option value="J2" >G&A
<option value="J3" >HR
<option value="J4" >Mix
<option value="J5" >PTO
<option value="J6" >SCEI
<option value="J7" >Software
<option value="J8" >Support
<option value="J9" >Sys Eng
<option value="J10" >Test
</select></td>

<td><select  name="pclient2">
<option value="C0" selected>Select Primary Client
<option value="C1">Audio
<option value="C2">Bend
<option value="C3">DTE
<option value="C4">EatSleepPlay
<option value="C5">EyeToy
<option value="C6">FPQA
<option value="C7">GFPQA
<option value="C8">GTG
<option value="C9">Guerrilla
<option value="C10">Insomniac
<option value="C11">Liverpool
<option value="C12">London Studio
<option value="C13">PD Sound
<option value="C14">Play
<option value="C15">Rika Toyoda
<option value="C16">Zindagi
<option value="C17">San Diego
<option value="C18">Santa Monica Studio
<option value="C19">SCE
<option value="C20">SCEA
<option value="C21">SCEE
<option value="C22">SCEI
<option value="C23">WWS_SDK
<option value="C24">SOE
<option value="C25">Telemetry
<option value="C26">That Game Company
<option value="C27">TNT
<option value="C28">VASG
<option value="C29">ModNation
<option value="C30">WWS
<option value="C31">Zindagi
<option value="C32">Zipper
<option value="C33">Other
</select></td>

</tr>
<tr> <td><select  name="days3">
<option value="D0" selected>Select Days
<option value="D1">1
<option value="D2">2
<option value="D3">3
<option value="D4">4
<option value="D5">5
<option value="D6">6
<option value="D7">7
<option value="D8">8
<option value="D9">9
<option value="D10">10
<option value="D11">11
<option value="D12">12
<option value="D13">13
<option value="D14">14
<option value="D15">15
<option value="D16">16
<option value="D17">17
<option value="D18">18
<option value="D19">19
<option value="D20">20
</select></td>

<td><select  name="project3">
<option value="P0" selected>Select Project
<option value="P1">3rd Party T&M
<option value="P2">ARB/Standards
<option value="P3">ATF2
<option value="P4">ATF3
<option value="P5">Audio
<option value="P6">Audio NG
<option value="P7">Audio PS3/PSP
<option value="P8">Audio Support
<option value="P9">Docs
<option value="P10">DTE
<option value="P11">DTE AVCAP
<option value="P12">DTE BinXML
<option value="P13">DTE DART
<option value="P14">DTE libs
<option value="P15">DTE Recap
<option value="P16">DTE Rec-Playback
<option value="P17">DTE Support
<option value="P18">DTE SyncLaunch
<option value="P19">Dtrace
<option value="P20">Dtrace Tools (Reswatch)
<option value="P21">FIOS
<option value="P22">FIOS2
<option value="P234">G&A / HR
<option value="P24">GameTech
<option value="P25">GameTech Support
<option value="P26">GTC
<option value="P27">Imogen
<option value="P28">Japanese Translation
<option value="P29">Level Editor
<option value="P30">Live Edit/Hub
<option value="P31">LLVM
<option value="P32">Metrics
<option value="P33">NGH
<option value="P34">SCEA Dev HW
<option value="P35">SCEA Intranet
<option value="P36">SHIP
<option value="P37">SHIP Alfresco
<option value="P38">SHIP Bamboo/CI
<option value="P39">SHIP Confluence
<option value="P40">SHIP EAGL
<option value="P41">SHIP Jira/Greenhopper
<option value="P42">SHIP Portal
<option value="P43">SHIP SCMs
<option value="P44">SHIP Search/Fisheye
<option value="P45">SHIP SourceForge
<option value="P46">SHIP SSO
<option value="P47">SHIP Support
<option value="P48">SHIP SysEng
<option value="P49">SHIP User Directory
<option value="P50">SHIP WSR
<option value="P51">SLED/LUA
<option value="P52">StateMachine
<option value="P53">SystemTech
<option value="P54">SystemTech Support
<option value="P55">THC
<option value="P56">Tradeshows / Edu
<option value="P57">VITA
<option value="P58">WWS Framework
<option value="P59">Zeus
</select></td>

<td><select  name="Job3">
<option value="J0" selected>Select Job
<option value="J1" >Docs
<option value="J2" >G&A
<option value="J3" >HR
<option value="J4" >Mix
<option value="J5" >PTO
<option value="J6" >SCEI
<option value="J7" >Software
<option value="J8" >Support
<option value="J9" >Sys Eng
<option value="J10" >Test
</select></td>

<td><select  name="pclient3">
<option value="C0" selected>Select Primary Client
<option value="C1">Audio
<option value="C2">Bend
<option value="C3">DTE
<option value="C4">EatSleepPlay
<option value="C5">EyeToy
<option value="C6">FPQA
<option value="C7">GFPQA
<option value="C8">GTG
<option value="C9">Guerrilla
<option value="C10">Insomniac
<option value="C11">Liverpool
<option value="C12">London Studio
<option value="C13">PD Sound
<option value="C14">Play
<option value="C15">Rika Toyoda
<option value="C16">Zindagi
<option value="C17">San Diego
<option value="C18">Santa Monica Studio
<option value="C19">SCE
<option value="C20">SCEA
<option value="C21">SCEE
<option value="C22">SCEI
<option value="C23">WWS_SDK
<option value="C24">SOE
<option value="C25">Telemetry
<option value="C26">That Game Company
<option value="C27">TNT
<option value="C28">VASG
<option value="C29">ModNation
<option value="C30">WWS
<option value="C31">Zindagi
<option value="C32">Zipper
<option value="C33">Other
</select></td>

</tr>
<tr> <td><select  name="days4">
<option value="D0" selected>Select Days
<option value="D1" >1
<option value="D2">2
<option value="D3">3
<option value="D4">4
<option value="D5">5
<option value="D6">6
<option value="D7">7
<option value="D8">8
<option value="D9">9
<option value="D10">10
<option value="D11">11
<option value="D12">12
<option value="D13">13
<option value="D14">14
<option value="D15">15
<option value="D16">16
<option value="D17">17
<option value="D18">18
<option value="D19">19
<option value="D20" >20
</select></td>

<td><select  name="project4">
<option value="P0" selected>Select Project
<option value="P1">3rd Party T&M
<option value="P2">ARB/Standards
<option value="P3">ATF2
<option value="P4">ATF3
<option value="P5">Audio
<option value="P6">Audio NG
<option value="P7">Audio PS3/PSP
<option value="P8">Audio Support
<option value="P9">Docs
<option value="P10">DTE
<option value="P11">DTE AVCAP
<option value="P12">DTE BinXML
<option value="P13">DTE DART
<option value="P14">DTE libs
<option value="P15">DTE Recap
<option value="P16">DTE Rec-Playback
<option value="P17">DTE Support
<option value="P18">DTE SyncLaunch
<option value="P19">Dtrace
<option value="P20">Dtrace Tools (Reswatch)
<option value="P21">FIOS
<option value="P22">FIOS2
<option value="P234">G&A / HR
<option value="P24">GameTech
<option value="P25">GameTech Support
<option value="P26">GTC
<option value="P27">Imogen
<option value="P28">Japanese Translation
<option value="P29">Level Editor
<option value="P30">Live Edit/Hub
<option value="P31">LLVM
<option value="P32">Metrics
<option value="P33">NGH
<option value="P34">SCEA Dev HW
<option value="P35">SCEA Intranet
<option value="P36">SHIP
<option value="P37">SHIP Alfresco
<option value="P38">SHIP Bamboo/CI
<option value="P39">SHIP Confluence
<option value="P40">SHIP EAGL
<option value="P41">SHIP Jira/Greenhopper
<option value="P42">SHIP Portal
<option value="P43">SHIP SCMs
<option value="P44">SHIP Search/Fisheye
<option value="P45">SHIP SourceForge
<option value="P46">SHIP SSO
<option value="P47">SHIP Support
<option value="P48">SHIP SysEng
<option value="P49">SHIP User Directory
<option value="P50">SHIP WSR
<option value="P51">SLED/LUA
<option value="P52">StateMachine
<option value="P53">SystemTech
<option value="P54">SystemTech Support
<option value="P55">THC
<option value="P56">Tradeshows / Edu
<option value="P57">VITA
<option value="P58">WWS Framework
<option value="P59">Zeus
</select></td>

<td><select  name="Job4">
<option value="J0" selected>Select Job
<option value="J1" >Docs
<option value="J2" >G&A
<option value="J3" >HR
<option value="J4" >Mix
<option value="J5" >PTO
<option value="J6" >SCEI
<option value="J7" >Software
<option value="J8" >Support
<option value="J9" >Sys Eng
<option value="J10" >Test
</select></td>


<td><select  name="pclient4">
<option value="C0" selected>Select Primary Client
<option value="C1">Audio
<option value="C2">Bend
<option value="C3">DTE
<option value="C4">EatSleepPlay
<option value="C5">EyeToy
<option value="C6">FPQA
<option value="C7">GFPQA
<option value="C8">GTG
<option value="C9">Guerrilla
<option value="C10">Insomniac
<option value="C11">Liverpool
<option value="C12">London Studio
<option value="C13">PD Sound
<option value="C14">Play
<option value="C15">Rika Toyoda
<option value="C16">Zindagi
<option value="C17">San Diego
<option value="C18">Santa Monica Studio
<option value="C19">SCE
<option value="C20">SCEA
<option value="C21">SCEE
<option value="C22">SCEI
<option value="C23">WWS_SDK
<option value="C24">SOE
<option value="C25">Telemetry
<option value="C26">That Game Company
<option value="C27">TNT
<option value="C28">VASG
<option value="C29">ModNation
<option value="C30">WWS
<option value="C31">Zindagi
<option value="C32">Zipper
<option value="C33">Other

</tr>
</table>
<input type="submit" value="insert record">
</form>
</body>
</html>
ENDFORM;
}
?>