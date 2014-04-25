<?php
	// Change this to "faculty", "instructor", or "business"
	$pageSessionType = "faculty";
	include "sessionValidator.php";
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Edit TAs</title>
  <link rel="stylesheet" href="css/style.css" type="text/css" media="screen">
  <script language="JavaScript">
    function toggle(id)
	{
        var state = document.getElementById(id).style.display;
		if (state == 'block')
		{
			document.getElementById(id).style.display = 'none';
		}
		else
		{
			document.getElementById(id).style.display = 'block';
		}
    }
</script>
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
				<select size=\"7\"name=\"courseSelect[]\" multiple=\"yes\" id=\"courseSelect\" onchange=\"this.form.submit();\">";
			if( empty( $_POST ) || empty( $_POST[ "courseSelect" ]))
			{
				echo "<option selected=\"selected\">Select a course</option>";
			}
			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				$selected = in_array($row[ "courseCode" ], $_POST[ "courseSelect" ]);
				echo "<option " . ($selected ? "selected=\"selected\"" : "") . "value=\"$row[courseCode] \">CS $row[courseCode] </option>";
			}
			echo "</select></form>";
		}
		else
		{
			echo $mysqli->error;
			echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
		}
		
		echo "<h2>Teaching assistants for $semester $year</h2>";
		
	

		if( !empty( $_POST ) && !empty( $_POST[ "courseSelect" ]))
		{
			foreach ($_POST[ "courseSelect" ] as $course)
			{
				//echo "<h1><a name=\"$course\" href=\"#$course\" onclick=\"toggle('$course');\">CS $course</a></h1><div class='courseDiv' id='$course'>";
				echo "<h1>CS $course</h1><div class='courseDiv' id='$course'>";
				$sql = "
				select * from
				Sections natural left outer join hasTA natural left outer join TAs
				where Sections.CRN in
				(select CRN from consistsOf
				where courseCode=$course and year=$year and semester='$semester') and Sections.year=$year and Sections.semester='$semester'";
				if( $result = $mysqli->query( $sql ) )
				{
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

					echo "</table></div><p />";
					$result->close();
				}
				else
				{
					echo $mysqli->error;
					echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
				}
			}
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
