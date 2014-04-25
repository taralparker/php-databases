<?php
	// Change this to "faculty", "instructor", or "business"
	$pageSessionType = "instructor";
	include "sessionValidator.php";
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Edit Textbooks</title>
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
		//@TODO: get the semester/year dynamically instead of hardcoding it

		$coursesSql = "
		select * from taughtBy natural join Sections natural join consistsOf natural join Courses where rNumber=$_SESSION[rNumber] and year=$year and semester='$semester' order by courseCode
		";
		if( $result1 = $mysqli->query( "select lastName, firstName from instructors where rNumber = $_SESSION[rNumber];" ) )
		{
			//This puts the first (technically the next remaining) row into the $row variable, whose columns can be accessed as follows
			$row1 = $result1->fetch_array( MYSQLI_ASSOC );
			//echo prints out html code, . (period) is the php string concatenation operator, and $row['attributeName'] returns the value of that attribute in this row
			echo "<p>Textbook information for $row1[firstName] $row1[lastName] ($_SESSION[rNumber]) in $semester $year.</p>";
			$result1->close();
		}
		else
		{
			echo $mysqli->error;
			echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
		}
		

		if( $result2 = $mysqli->query( $coursesSql ) )
		{
			echo "<table border='1' id='htmlgrid' class='testgrid'>
			<tr>
			<th>ISBN</th>
			<th>Book Title</th>
			<th>Authors</th>
			<th>Publisher</th>
			<th>Edition</th>
			</tr>";

			while( $row2 = $result2->fetch_array( MYSQLI_ASSOC ) )
			{
				echo "<tr><th colspan=5>CS $row2[courseCode]: $row2[courseTitle] - $row2[sectionNumber]</th></tr>";
				
				$textbookSql = "select * from usesBook natural join taughtBy natural join Textbooks natural join consistsOf where courseCode=$row2[courseCode] and rNumber=$_SESSION[rNumber] order by catalogYear desc, semester desc limit 1";

				if( $result3 = $mysqli->query( $textbookSql ) )
				{
					$row3 = $result3->fetch_array( MYSQLI_ASSOC );
					
					echo "<tr>";
					echo "<td>" . $row3[ "ISBN" ] . "</td>";
					echo "<td>" . $row3[ "bookTitle" ] . "</td>";
					echo "<td>" . $row3[ "author" ] . "</td>";
					echo "<td>" . $row3[ "publisher" ] . "</td>";
					echo "<td>" . $row3[ "edition" ] . "</td>";
					echo "</tr>";
					$result3->close();
				}
				else
				{
					echo "<tr><th colspan=5 style=\"background: red;\">Could not retrieve textbook information.</th></tr>";
				}
				
			}

			echo "</table>";
			$result2->close();
			
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
