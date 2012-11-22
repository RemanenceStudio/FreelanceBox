<?

header ("Content-type: application/vnd.ms-excel;name=export.xls");
header("Content-Disposition: attachment;filename=export.xls\n");


///////////////////////////////////////////////////////////////////////////////////

///																				///

///							Configuration du compte								///

///																				///

///////////////////////////////////////////////////////////////////////////////////







			require_once 'Zend/Loader.php';

			Zend_Loader::loadClass('Zend_Gdata');

			Zend_Loader::loadClass('Zend_Gdata_AuthSub');

			Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

			Zend_Loader::loadClass('Zend_Gdata_Calendar');

			

			$user = 'jsaudax@gmail.com';

			$pass = 'tgp73p76';

			

			if( isset($_POST['ps']) ) {

				$user = $_POST['ps'];

				$pass = $_POST['pw'];

			}

			

			$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;

			 

			try

			{

				$client = Zend_Gdata_ClientLogin::getHttpClient($user,$pass,$service);			

			}

			catch(Exception $e)

			{

				// prevent Google username and password from being displayed

				// if a problem occurs

				echo "Could not connect to calendar.";

				die();

			}









///////////////////////////////////////////////////////////////////////////////////

///																				///

///							Récupération des events								///

///																				///

///////////////////////////////////////////////////////////////////////////////////







// https://www.google.com/calendar/feeds/jsaudax%40gmail.com/private-95cf30d1b845c74f9f5ea847894cdb25/basic

		// parameters

		$calendar_user = 'jsaudax%40gmail.com';

		$calendar_visibility = 'private-95cf30d1b845c74f9f5ea847894cdb25';

		 

		$start_date = '2012-07-25';

		$end_date = '2012-08-26';

		

		if( isset($_POST['ps']) ) {

			$calendar_user = $_POST['cu'];

			$calendar_visibility = $_POST['cv'];

			$start_date = $_POST['dd'];

			$end_date = $_POST['df'];

		}

		

		// If necessary add timezone

		//$start_date = $start_date  . 'T00:00:00.000-07:00';

		//$end_date = $end_date  . 'T00:00:00.000-07:00';

		 

		// build query

		$gdataCal = new Zend_Gdata_Calendar($client);

		 

		$query = $gdataCal->newEventQuery();

		 

		$query->setUser($calendar_user);

		$query->setVisibility($calendar_visibility);			

		 

		$query->setSingleEvents(true);

		$query->setProjection('full');

		$query->setOrderby('starttime');

		$query->setSortOrder('ascending');

		$query->setMaxResults(100);

		 

		 

		$query->setStartMin($start_date);

		$query->setStartMax($end_date);

		 

		// execute and get results

		$event_list = $gdataCal->getCalendarEventFeed($query);





// Day of week

$tabDay = array(1 => 'Monday', 2 => "Tuesday", 3 => "Wednesday", 4 => "Thursday", 5 => "Friday", 6 => "Saturday", 0 => "Sunday");



// Range date

$dateIncrement = $start_date;

$i = 0;



while( $dateIncrement <= $end_date ) {

	

	$tabDateIncrement[$i] =  $dateIncrement;

	

	$dateIncrement = date('Y-m-d', strtotime('+1day', strtotime($dateIncrement)));

	$i++;

	

}

foreach ($event_list as $event) {
			
	$dateStart[0] = substr($event->when[0]->startTime, 0, 10);
	
	$total = count($tabTest) - 1;
	$index = count($tabTest);
	
	if ($dateStart[0] != $tabTest[$total]['date']) {
		echo 'ok<br>';
		$tabTest[$index]['title'] = $event->title;
		$tabTest[$index]['date'] = $dateStart[0];
	}
	else {
		$tabTest[$total]['title'] .= ' / ' . $event->title;
	}


}

?>
<html xmlns:v="urn:schemas-microsoft-com:vml"

xmlns:o="urn:schemas-microsoft-com:office:office"

xmlns:x="urn:schemas-microsoft-com:office:excel"

xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv=Content-Type content="text/html; charset=UTF-8">
<meta name=ProgId content=Excel.Sheet>
<meta name=Generator content="Microsoft Excel 12">
<style>
<!--
table  tr {
	mso-height-source: auto;
}
col {
	mso-width-source: auto;
}
br {
	mso-data-placement: same-cell;
}
.style0 {
	mso-number-format: General;
	text-align: general;
	vertical-align: middle;
	white-space: nowrap;
	mso-rotate: 0;
	mso-background-source: auto;
	mso-pattern: auto;
	color: windowtext;
	font-size: 10.0pt;
	font-weight: 400;
	font-style: normal;
	text-decoration: none;
	font-family: Arial, sans-serif;
	mso-font-charset: 0;
	border: none;
	mso-protection: locked visible;
	mso-style-name: Normal;
	mso-style-id: 0;
}
td {
	mso-style-parent: style0;
	padding-top: 1px;
	padding-right: 1px;
	padding-left: 1px;
	mso-ignore: padding;
	color: windowtext;
	font-size: 10.0pt;
	font-weight: 400;
	font-style: normal;
	text-decoration: none;
	font-family: Arial, sans-serif;
	mso-font-charset: 0;
	mso-number-format: General;
	text-align: general;
	vertical-align: middle;
	border: none;
	mso-background-source: auto;
	mso-pattern: auto;
	mso-protection: locked visible;
	white-space: nowrap;
	mso-rotate: 0;
}
.xl65 {
	mso-style-parent: style0;
	color: gray;
	font-size: 11.0pt;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	white-space: normal;
}
.xl66 {
	mso-style-parent: style0;
	vertical-align: bottom;
	border-top: none;
	border-right: none;
	border-bottom: .5pt solid windowtext;
	border-left: none;
	white-space: normal;
}
.xl67 {
	mso-style-parent: style0;
	color: black;
	font-size: 9.0pt;
	font-weight: 700;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	text-align: center;
	border: .5pt solid windowtext;
	background: #BFBFBF;
	mso-pattern: black none;
}
.xl68 {
	mso-style-parent: style0;
	vertical-align: bottom;
	border-top: .5pt solid windowtext;
	border-right: none;
	border-bottom: .5pt solid windowtext;
	border-left: none;
	white-space: normal;
}
.xl69 {
	mso-style-parent: style0;
	vertical-align: bottom;
	border-top: none;
	border-right: none;
	border-bottom: none;
	border-left: .5pt solid windowtext;
	white-space: normal;
}
.xl70 {
	mso-style-parent: style0;
	color: black;
	font-size: 8.0pt;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	text-align: center;
	border: .5pt solid windowtext;
}
.xl71 {
	mso-style-parent: style0;
	color: black;
	font-size: 8.0pt;
	mso-number-format: "m\/d\/yyyy\\ \;\@";
	text-align: center;
	border: .5pt solid windowtext;
}
.xl72 {
	mso-style-parent: style0;
	color: red;
	font-size: 9.0pt;
	font-weight: 700;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	text-align: center;
	border: .5pt solid windowtext;
}
.xl73 {
	mso-style-parent: style0;
	color: black;
	font-size: 8.0pt;
	font-weight: 700;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	text-align: center;
	border: .5pt solid windowtext;
	white-space: normal;
}
.xl74 {
	mso-style-parent: style0;
	color: black;
	font-size: 9.0pt;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	text-align: center;
	border: .5pt solid windowtext;
}
.xl75 {
	mso-style-parent: style0;
	color: black;
	font-size: 11.0pt;
	font-weight: 700;
	text-align: right;
	border: .5pt solid windowtext;
	background: #BFBFBF;
	mso-pattern: black none;
}
.xl76 {
	mso-style-parent: style0;
	vertical-align: bottom;
	border-top: .5pt solid windowtext;
	border-right: none;
	border-bottom: none;
	border-left: none;
	white-space: normal;
}
.xl77 {
	mso-style-parent: style0;
	color: black;
	font-size: 24.0pt;
	font-weight: 700;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	text-align: left;
	vertical-align: top;
}
.xl78 {
	mso-style-parent: style0;
	color: black;
	font-size: 9.0pt;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	white-space: normal;
}
.xl79 {
	mso-style-parent: style0;
	vertical-align: bottom;
	border-top: .5pt solid windowtext;
	border-right: .5pt solid windowtext;
	border-bottom: .5pt solid windowtext;
	border-left: none;
	white-space: normal;
}
.xl80 {
	mso-style-parent: style0;
	color: black;
	font-size: 8.0pt;
	text-align: center;
	border: .5pt solid windowtext;
	white-space: normal;
}
.xl81 {
	mso-style-parent: style0;
	color: black;
	font-size: 14.0pt;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	text-align: center;
	border: .5pt solid windowtext;
	white-space: normal;
}
.xl82 {
	mso-style-parent: style0;
	color: black;
	font-size: 11.0pt;
	mso-number-format: "0\\ \[$€-40C\]\;";
	text-align: center;
	border: .5pt solid windowtext;
}
.xl83 {
	mso-style-parent: style0;
	color: black;
	font-size: 11.0pt;
	font-weight: 700;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	text-align: right;
	border: .5pt solid windowtext;
	background: #BFBFBF;
	mso-pattern: black none;
}
.xl84 {
	mso-style-parent: style0;
	color: black;
	font-size: 11.0pt;
	font-weight: 700;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	text-align: left;
	border-top: .5pt solid windowtext;
	border-right: none;
	border-bottom: none;
	border-left: none;
}
.xl85 {
	mso-style-parent: style0;
	font-weight: 700;
	vertical-align: bottom;
	border-top: .5pt solid windowtext;
	border-right: none;
	border-bottom: none;
	border-left: none;
	white-space: normal;
}
.xl86 {
	mso-style-parent: style0;
	color: black;
	font-size: 11.0pt;
	font-weight: 700;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	text-align: right;
	border-top: .5pt solid windowtext;
	border-right: none;
	border-bottom: none;
	border-left: none;
}
.xl87 {
	mso-style-parent: style0;
	color: black;
	font-size: 9.0pt;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	text-align: left;
	white-space: normal;
}
.xl88 {
	mso-style-parent: style0;
	text-align: left;
}
.xl781 {
	mso-style-parent: style0;
	color: black;
	font-size: 9.0pt;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	white-space: normal;
}
.xl782 {
	mso-style-parent: style0;
	color: black;
	font-size: 9.0pt;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	white-space: normal;
}
.xl783 {
	mso-style-parent: style0;
	color: black;
	font-size: 9.0pt;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	white-space: normal;
}
.xl784 {
	mso-style-parent: style0;
	color: black;
	font-size: 9.0pt;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	white-space: normal;
}
.xl785 {
	mso-style-parent: style0;
	color: black;
	font-size: 9.0pt;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	white-space: normal;
}
.xl786 {
	mso-style-parent: style0;
	color: black;
	font-size: 9.0pt;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	white-space: normal;
}
.xl787 {
	mso-style-parent: style0;
	color: black;
	font-size: 9.0pt;
	font-family: Calibri, sans-serif;
	mso-font-charset: 0;
	white-space: normal;
}
-->
</style>
</head>

<body link=blue vlink=purple>
<table border=0 cellpadding=0 cellspacing=0 width=700 style='border-collapse:

 collapse;table-layout:fixed;width:527pt'>
    <col width=29 style='mso-width-source:userset;mso-width-alt:1060;width:22pt'>
    <col width=120 style='mso-width-source:userset;mso-width-alt:4388;width:90pt'>
    <col width=47 style='mso-width-source:userset;mso-width-alt:1718;width:35pt'>
    <col width=90 style='mso-width-source:userset;mso-width-alt:3291;width:68pt'>
    <col width=86 style='mso-width-source:userset;mso-width-alt:3145;width:65pt'>
    <col width=46 style='mso-width-source:userset;mso-width-alt:1682;width:35pt'>
    <col width=33 style='mso-width-source:userset;mso-width-alt:1206;width:25pt'>
    <col width=184 style='mso-width-source:userset;mso-width-alt:6729;width:138pt'>
    <col width=65 style='mso-width-source:userset;mso-width-alt:2377;width:49pt'>
    <tr height=42 style='height:31.5pt'>
        <td height=42 width=29 style='height:31.5pt;width:22pt'></td>
        <td colspan=6 class=xl77 width=422 style='width:318pt'>TIMESHEET</td>
        <td width=184 style='width:138pt'></td>
        <td width=65 style='width:49pt'></td>
    </tr>
    <tr height=17 style='height:12.75pt'>
        <td height=17 style='height:12.75pt'></td>
        <td colspan=2 class=xl65 width=167 style='width:125pt'>Name</td>
        <td colspan=6 class=xl78 width=504 style='width:380pt'>Variable &agrave; r&eacute;cup&eacute;rer</td>
    </tr>
    <tr height=17 style='height:12.75pt'>
        <td height=17 style='height:12.75pt'></td>
        <td colspan=2 class=xl65 width=167 style='width:125pt'>Adress</td>
        <td colspan=6 class=xl78 width=504 style='width:380pt'><span class="xl781" style="width:380pt">Variable &agrave; r&eacute;cup&eacute;rer</span></td>
    </tr>
    <tr height=23 style='mso-height-source:userset;height:17.25pt'>
        <td height=23 style='height:17.25pt'></td>
        <td class=xl65 width=120 style='width:90pt'>Postcode</td>
        <td></td>
        <td colspan=6 class=xl87 width=504 style='width:380pt'><span class="xl782" style="width:380pt">Variable &agrave; r&eacute;cup&eacute;rer</span></td>
    </tr>
    <tr height=20 style='height:15.0pt'>
        <td height=20 style='height:15.0pt'></td>
        <td class=xl65 width=120 style='width:90pt'>City</td>
        <td></td>
        <td colspan=6 class=xl78 width=504 style='width:380pt'><span class="xl783" style="width:380pt">Variable &agrave; r&eacute;cup&eacute;rer</span></td>
    </tr>
    <tr height=23 style='mso-height-source:userset;height:17.25pt'>
        <td height=23 style='height:17.25pt'></td>
        <td class=xl65 width=120 style='width:90pt'>Country</td>
        <td></td>
        <td colspan=6 class=xl78 width=504 style='width:380pt'><span class="xl784" style="width:380pt">Variable &agrave; r&eacute;cup&eacute;rer</span></td>
    </tr>
    <tr height=23 style='mso-height-source:userset;height:17.25pt'>
        <td height=23 class=xl66 width=29 style='height:17.25pt;width:22pt'>&nbsp;</td>
        <td class=xl66 width=120 style='width:90pt'>&nbsp;</td>
        <td class=xl66 width=47 style='width:35pt'>&nbsp;</td>
        <td class=xl66 width=90 style='width:68pt'>&nbsp;</td>
        <td class=xl66 width=86 style='width:65pt'>&nbsp;</td>
        <td class=xl66 width=46 style='width:35pt'>&nbsp;</td>
        <td class=xl66 width=33 style='width:25pt'>&nbsp;</td>
        <td class=xl66 width=184 style='width:138pt'>&nbsp;</td>
        <td></td>
    </tr>
    <tr height=23 style='mso-height-source:userset;height:17.25pt'>
        <td colspan=2 height=23 class=xl67 style='height:17.25pt'>Time Sheet</td>
        <td colspan=2 class=xl67 style='border-left:none'>Social security number</td>
        <td colspan=3 class=xl67 style='border-left:none'>Company</td>
        <td class=xl67 style='border-top:none;border-left:none'>Date</td>
        <td class=xl69 width=65 style='border-left:none;width:49pt'>&nbsp;</td>
    </tr>
    <tr height=24 style='mso-height-source:userset;height:18.0pt'>
        <td colspan=2 height=24 class=xl70 style='height:18.0pt'><span class="xl786" style="width:380pt">Variable &agrave; r&eacute;cup&eacute;rer</span></td>
        <td colspan=2 class=xl80 width=137 style='border-left:none;width:103pt'><span class="xl785" style="width:380pt">Variable &agrave; r&eacute;cup&eacute;rer</span></td>
        <td colspan=3 class=xl81 width=165 style='border-left:none;width:125pt'><span class="xl787" style="width:380pt">Variable &agrave; r&eacute;cup&eacute;rer</span></td>
        <td class=xl71 style='border-top:none;border-left:none'><? echo date("m/d/Y"); ?></td>
        <td class=xl69 width=65 style='border-left:none;width:49pt'>&nbsp;</td>
    </tr>
    <tr height=23 style='mso-height-source:userset;height:17.25pt'>
        <td height=23 class=xl68 width=29 style='height:17.25pt;border-top:none;

  width:22pt'>&nbsp;</td>
        <td class=xl68 width=120 style='border-top:none;width:90pt'>&nbsp;</td>
        <td class=xl68 width=47 style='border-top:none;width:35pt'>&nbsp;</td>
        <td class=xl68 width=90 style='border-top:none;width:68pt'>&nbsp;</td>
        <td class=xl68 width=86 style='border-top:none;width:65pt'>&nbsp;</td>
        <td class=xl68 width=46 style='border-top:none;width:35pt'>&nbsp;</td>
        <td class=xl68 width=33 style='border-top:none;width:25pt'>&nbsp;</td>
        <td class=xl68 width=184 style='border-top:none;width:138pt'>&nbsp;</td>
        <td></td>
    </tr>
    <tr height=23 style='mso-height-source:userset;height:17.25pt'>
        <td colspan=7 height=23 class=xl67 style='height:17.25pt'>Monthly Wages - See
            
            Contract</td>
        <td class=xl67 style='border-top:none;border-left:none'>LOA</td>
        <td class=xl69 width=65 style='border-left:none;width:49pt'>&nbsp;</td>
    </tr>
    <tr height=23 style='mso-height-source:userset;height:17.25pt'>
        <td colspan=7 height=23 class=xl82 style='height:17.25pt'>&nbsp;</td>
        <td class=xl72 style='border-top:none;border-left:none'>0</td>
        <td class=xl69 width=65 style='border-left:none;width:49pt'>&nbsp;</td>
    </tr>
    <tr height=17 style='height:12.75pt'>
        <td height=17 class=xl73 width=29 style='height:12.75pt;border-top:none;

  width:22pt'>Day</td>
        <td class=xl73 width=120 style='border-top:none;border-left:none;width:90pt'>Worked
            
            day</td>
        <td colspan=6 class=xl73 width=486 style='border-left:none;width:366pt'>Project
            
            Description</td>
        <td class=xl69 width=65 style='border-left:none;width:49pt'>&nbsp;</td>
    </tr>
    
    <!-- LIGNE A GENERER -->
    
    <?

 $j = 0;

 for ($i = 0; $i < count($tabDateIncrement); $i++) {

 //foreach ($event_list as $event) {

	 

	$dayNumber = date("d",strtotime($tabDateIncrement[$i]));

	$dayAlpha = $tabDay[date("w",strtotime($tabDateIncrement[$i]))];

	

	$title = "x";

	if($dayAlpha == 'Saturday' || $dayAlpha == 'Sunday') {

		$title = "";	

	}

	

	// Test for work day

	if(@$tabTest[$j]['date'] == $tabDateIncrement[$i]) {

		@$title = $tabTest[$j]['title'];

		$j++;

	}
	

 ?>
    <tr height=23 style='mso-height-source:userset;height:17.25pt'>
        <td height=23 class=xl70 style='height:17.25pt;border-top:none'><? echo $dayNumber; ?></td>
        <td class=xl74 style='border-top:none;border-left:none'><? echo $dayAlpha; ?></td>
        <td colspan=6 class=xl70 style='border-left:none'><? echo $title; ?></td>
        <td class=xl69 width=65 style='border-left:none;width:49pt'>&nbsp;</td>
    </tr>
    <?

 

 }

 

 ?>
    
    <!-- FIN LIGNE GENERE -->
    
    <tr height=23 style='mso-height-source:userset;height:17.25pt'>
        <td colspan=7 height=23 class=xl83 style='height:17.25pt'>Total<span

  style='mso-spacerun:yes'> </span></td>
        <td class=xl75 style='border-top:none;border-left:none'>13</td>
        <td class=xl69 width=65 style='border-left:none;width:49pt'>&nbsp;</td>
    </tr>
    <tr height=20 style='height:15.0pt'>
        <td colspan=3 height=20 class=xl84 style='height:15.0pt'>Consultant Signature</td>
        <td class=xl76 width=90 style='border-top:none;width:68pt'>&nbsp;</td>
        <td class=xl76 width=86 style='border-top:none;width:65pt'>&nbsp;</td>
        <td class=xl76 width=46 style='border-top:none;width:35pt'>&nbsp;</td>
        <td colspan=2 class=xl85 width=217 style='width:163pt'>Senior Consultant
            
            Signature</td>
        <td></td>
    </tr>
    
    <![if supportMisalignedColumns]>
    
    <tr height=0 style='display:none'>
        <td width=29 style='width:22pt'></td>
        <td width=120 style='width:90pt'></td>
        <td width=47 style='width:35pt'></td>
        <td width=90 style='width:68pt'></td>
        <td width=86 style='width:65pt'></td>
        <td width=46 style='width:35pt'></td>
        <td width=33 style='width:25pt'></td>
        <td width=184 style='width:138pt'></td>
        <td width=65 style='width:49pt'></td>
    </tr>
    
    <![endif]>
    
</table>
</body>
</html>