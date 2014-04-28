<?php

/**
 * Created by phpDesigner8
 * Author: Eric
 */
 
	// Permission
	$pageSessionType = "business";
	include "sessionValidator.php";
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Course View</title>
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
	$mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

	if( !$mysqli->connect_errno )
	{
		if( $result = $mysqli->query( "select distinct courseCode from Courses order by courseCode;"))
		{
			$year = isset( $_SESSION[ "demoYear" ] ) ? $_SESSION[ "demoYear" ] : 2014;
			$semester = isset( $_SESSION[ "demoSemester" ] ) ? $_SESSION[ "demoSemester" ] : "Spring";
			echo "<form name=\"courseSelectionForm\"action=\"businessCourseView.php\" method=\"POST\">
				<select size=\"7\"name=\"courseSelect\" id=\"courseSelect\">";
			if(empty( $_POST ) || empty( $_POST[ "courseSelect" ]) || !is_numeric($_POST[ "courseSelect" ]))
			{
				echo "<option selected=\"selected\">Select a course</option>";
			}
			else
			{
				echo "<option>Select a course</option>";
			}
			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				$selected = ($row[ "courseCode" ] == $_POST[ "courseSelect" ]);
				echo "<option " . ($selected ? "selected=\"selected\"" : "") . "value=\"" . $row[ "courseCode" ] . "\">CS " . $row[ "courseCode" ] . "</option>";
			}
			echo "</select>";

			echo "<select size=\"7\"name=\"yearSelect\" id=\"yearSelect\">";
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

			if(!empty( $_POST ) && !empty( $_POST[ "courseSelect" ]))
			{
				if(is_numeric($_POST[ "courseSelect" ]))	
				{
					echo "<h1>All section information of CS $_POST[courseSelect] in the last " . (($selectedYear > 1) ? ($selectedYear . " years") : "year") . "</h1>";
					$targetYear = $year - $selectedYear; //calculate the target year to find the sections properly
					if( $result2 = $mysqli->query( "select catalogYear, firstName, lastName, enrollment, semester, title, year from consistsOf natural join Courses natural join Sections natural join taughtBy natural join Instructors where courseCode=$_POST[courseSelect] and year > $targetYear order by catalogYear desc, semester desc;" ))
					{
						echo "<table border='1' id='htmlgrid' class='testgrid'>
							<tr>
							<th>Year</th>
							<th>Semester</th>
							<th>Section</th>
                            <th>Title</th>
							<th>Instructor</th>
							<th>Enrollment</th>
							</tr>";

						$currentYear = 1;
						while( $row2 = $result2->fetch_array( MYSQLI_ASSOC ) )
						{
							if($currentYear != $row2[ "catalogYear" ])
							{
								$currentYear = $row2[ "catalogYear" ];
								echo "<tr><th colspan=5 style=\"text-align: center; text-size: 125%; font-weight: bold;\">"
								. substr($currentYear, 0, 4) . " - " . substr($currentYear, -4) . "</th></tr>";
							}
							echo "<tr>";
							echo "<td>" . $row2[ "year" ] . "</td>
							<td>" . $row2[ "semester" ] . "</td>
							<td>" . $row2[ "sectionNumber" ] . "</td>
                            <td>" . $row2["title" ] . "</td>
							<td>" . $row2[ "firstName" ] . " " . $row2[ "lastName" ] . "</td>
							<td>" . $row2[ "enrollment" ] . "</td></tr>";
						}
						echo "</table>";
						$result2->close();
					}
					else
					{
						echo $mysqli->error;
						echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
					}
				}
			}
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
