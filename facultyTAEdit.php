<?php
	// Change this to "faculty", "instructor", or "business"
	$pageSessionType = "faculty";
	include "sessionValidator.php";
	// Include this for global database access variables
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <!-- Set page title here -->
  <title>TA Edit</title>
  <link rel="stylesheet" href="css/style.css" type="text/css" media="screen">
 </head>

 <body>
  <div id="container">
   <div id="masthead">
    <div id="logo"></div>
    <div id="title"></div>
   </div>

<?php echo file_get_contents( $pageSessionType."Header.php" ); ?>

   <br>

<?php
	function convertTime($time)
	{
		$timeString = ((floor($time/100) % 12 == 0) ? "12" : (floor($time/100) % 12)) . ":" . (($time % 100 == 0) ? "00" : ($time % 100));
		return $timeString;
	}
	
 	$mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );
	
	if( !$mysqli->connect_errno )
	{
		$year = isset( $_SESSION[ "demoYear" ] ) ? $_SESSION[ "demoYear" ] : 2014;
		$semester = isset( $_SESSION[ "demoSemester" ] ) ? $_SESSION[ "demoSemester" ] : "Spring";
		
		if( $result = $mysqli->query( "
		select distinct courseCode
		from consistsOf natural join Courses
		where semester='$semester' and year=$year
		order by courseCode;"))
		{
			echo "<form name=\"courseSelectionForm\" action=\"facultyTAEdit.php\" method=\"POST\">
				<select name=\"courseSelect\" id=\"courseSelect\" onchange=\"this.form.submit();\">
						";
			$selectedCourse = "";
			if( !empty( $_POST ) && !empty( $_POST[ "courseSelect" ]))
			{
				$selectedCourse = $_POST[ "courseSelect"];	
			}
			else
			{
				echo "<option selected=\"selected\">Select a course</option>";
			}
			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				$selected = $row[ "courseCode" ] == $selectedCourse;
				echo "<option " . ($selected ? "selected=\"selected\"" : "") . "value=\"" . $row[ "courseCode" ] . "\">" . $row[ "courseCode" ] . "</option>";
			}
			echo "</select></form>";
		}
		else
		{
			echo $mysqli->error;
			echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
		}
		
	
	
		$sql = "
		select * from
		Sections natural left outer join hasTA natural left outer join TAs
		where Sections.CRN in
		(select CRN from consistsOf
		where courseCode=$_POST[courseSelect] and year=$year and semester='$semester') and Sections.year=$year and Sections.semester='$semester'";

		if( !empty( $_POST ) && !empty( $_POST[ "courseSelect" ]))
		{
			if( $result = $mysqli->query( $sql ) )
			{
				echo "<p>Teaching assistants for CS " . $_POST[ "courseSelect" ] . " during " . $semester . " " . $year . ".</p>";
				echo "<table border='1' id='htmlgrid' class='testgrid'>
				<tr>
				<th>CRN</th>
				<th>Section</th>
				<th>Type</th>
				<th>Days</th>
				<th>Start Time</th>
				<th>End Time</th>
				<th>TA Name</th>
				<th>RNumber</th>
				<th>Hours/Week</th>
				</tr>";

				while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
				{
					echo "<tr>";
					echo "<td>" . $row[ "CRN" ] . "</td>";
					echo "<td>" . $row[ "sectionNumber" ] . "</td>";
					echo "<td>" . $row[ "type" ] . "</td>";
					echo "<td>" . $row[ "days" ] . "</td>";
					echo "<td>" . convertTime($row[ "startTime" ]) . "</td>";
					echo "<td>" . convertTime($row[ "endTime" ]) . "</td>";
					echo "<td>" . $row[ "firstName" ] . " " . $row[ "lastName" ] . "</td>";		
					echo "<td>" . $row[ "rNumber" ] . "</td>";
					echo "<td>" . $row[ "hoursPerWeek" ] . "</td>";
					echo "</tr>";
				}

				echo "</table>";
				$result->close();
			}
			else
			{
				echo $mysqli->error;
				echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
			}
		}

		$mysqli->close();
	}
	else
		echo "<div align='center'>Unable to connect to database.</div>";
?>

  </div>
 </body>
</html>
