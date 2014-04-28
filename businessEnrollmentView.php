<?php
	// Change this to "faculty", "instructor", or "business"
	$pageSessionType = "business";
	include "sessionValidator.php";
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Enrollment View</title>
  <link rel="stylesheet" href="css/style.css" type="text/css" media="screen">
 </head>

 <body>
  <table id="bodyTable" align="center">
   <tr>
    <td id="bodyTableLeft">
    </td>
    <td id="bodyTableMiddle" valign="top">
     <div id="masthead">
      <div id="logo"></div>
      <div id="title"></div>
     </div>

<?php include $pageSessionType."Sidebar.html"; ?>

     <!-- Page contents -->
     <div id="content" align="center">

<?php

	function getLevel($courseCode)
	{
		return ($courseCode - ($courseCode % 1000));
	}
	$mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

	if( !$mysqli->connect_errno )
	{
		$year = isset( $_SESSION[ "demoYear" ] ) ? $_SESSION[ "demoYear" ] : 2014;
		$semester = isset( $_SESSION[ "demoSemester" ] ) ? $_SESSION[ "demoSemester" ] : "Spring";
		echo "<form name=\"tearSelectionForm\"action=\"businessEnrollmentView.php\" method=\"POST\">
			 <select size=\"7\"name=\"yearSelect\" id=\"yearSelect\">";
		$selectedYear = "5";
		if(!empty( $_POST ) && $_POST[ "yearSelect" ])
		{
			$selectedYear = $_POST[ "yearSelect" ];
		}
		for( $counter = 1; $counter <= 20; $counter++)
		{
			$selected1 = ($counter == $selectedYear);
			echo "<option " . ($selected1 ? "selected=\"selected\"" : "") . "value=\"" . $counter . "\">" . $counter . "</option>";
		}
		echo "</select>";
		echo "<input type=\"submit\" value=\"Submit\"></form>";
		$targetYear = $year - $selectedYear;
		if($result = $mysqli->query( "select * from (select distinct courseCode from Courses) as A natural left outer join (select distinct courseCode, count(distinct CRN) as numClasses, sum(enrollment) as totalEnrollment from Sections natural join consistsOf where year>$targetYear and semester in ('Fall', 'Spring') group by courseCode) as B;" ))
		{
			echo "<h1>Cumulative enrollment for all classes over the last " . (($selectedYear > 1) ? ($selectedYear . " years") : "year") . "</h1>";
			echo "<table border='1' id='htmlgrid' class='testgrid'>
					<tr>
					<th>Course Code</th>
					<th>Total Enrollment</th>
					</tr>";
			$level = -1;
			$totalEnrollment = 0;
			businessRegularView.php
			echo "</table>";
			$result->close();
		}
		else
		{
			echo $mysqli->error;
			echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
		}
		$mysqli->close();
	}
	else
		echo "<div align='center'>Unable to connect to database.</div>";
?>

     </div>
    </td>
    <td id="bodyTableRight">
    </td>
   </tr>
  </table>
 </body>
</html>
