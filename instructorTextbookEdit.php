<?php
	// Change this to "faculty", "instructor", or "business"
	$pageSessionType = "instructor";
	include "sessionValidator.php";
	// Include this for global database access variables
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Textbook Editor</title>
  <link rel="stylesheet" href="css/style.css" type="text/css" media="screen">
 </head>

 <body>
  <div id="container">
   <div id="masthead">
    <div id="logo"></div>
    <div id="title"></div>
   </div>

<?php echo file_get_contents( $pageSessionType."Header.php" ); ?>

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
		$semester = isset( $_SESSION[ "demoSemester" ] ) ? $_SESSION[ "demoSemester" ] : "Spring"; //@TODO: to change this
		// If the page was submitted with an update request, do so
		/*if( !empty( $_POST ) )
		{
			if( !empty( $_POST[ "rNumber" ] ) && !empty( $_POST[ "lastName" ] ) && !empty( $_POST[ "firstName" ] ) && !empty( $_POST[ "tenured" ] ) && !empty( $_POST[ "joiningSemester" ] ) && !empty( $_POST[ "joiningYear" ] ) )
			{
				$tenu = $_POST[ "tenured" ] === "Y" ? 1 : 0;
				$sql = "INSERT INTO instructors( rNumber , lastName , firstName , instructorTitle , tenured , joiningSemester ,
joiningYear , loadPreference ) VALUES ('$_POST[rNumber]','$_POST[lastName]','$_POST[firstName]','$_POST[instructorTitle]',$tenu,'$_POST[joiningSemester]',$_POST[joiningYear], NULL )";
				//printf( "%s<br>" , $sql );

				if( $mysqli->query( $sql ) )
					printf( "Insertion complete.<br>" );
				else
					printf( "Insertion failed: %s<br>" , $mysqli->error );
			}
			else
				printf( "One or more required fields are blank." );
		}*/

		$sql = "
		select * from
		((select * from taughtBy natural join consistsOf natural join Courses natural join Sections
		where rNumber=$_SESSION[rNumber] and year=$year and semester='$semester') as A
		natural left outer join
		(select * from usesBook natural join Textbooks
		where (CRN, year, semester) in
		(select CRN, year, semester from taughtBy where rNumber=$_SESSION[rNumber])) as C)
		order by courseCode, CRN
		";
		if( $result = $mysqli->query( "select lastName, firstName from instructors where rNumber = $_SESSION[rNumber];" ) )
		{
			$row = $result->fetch_array( MYSQLI_ASSOC );
			echo "<p>Textbook information for " . $row [ "firstName" ] . " " . $row [ "lastName" ] . " (" . $_SESSION['rNumber'] . ") in " . $semester . " " . $year . ".</p>";
		}
		else
		{
			echo $mysqli->error;
			echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
		}
		// Start making the table
		if( $result = $mysqli->query( $sql ))
		{
			echo "<table border='1' id='htmlgrid' class='testgrid'>
			<tr>
			<th>Course Code</th>
			<th>Course Title</th>
			<th>CRN</th>
			<th>Section</th>
			<th>Days</th>
			<th>Start Time</th>
			<th>End Time</th>
			<th>ISBN</th>
			<th>Book Title</th>
			<th>Author</th>
			<th>Publisher</th>
			<th>Edition</th>
			</tr>";
			
			// Create each row
			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				echo "<tr>";
				echo "<td>" . $row[ "courseCode" ] . "</td>";
				echo "<td>" . $row[ "courseTitle" ] . "</td>";
				echo "<td>" . $row[ "CRN" ] . "</td>";
				echo "<td>" . $row[ "sectionNumber" ] . "</td>";
				echo "<td>" . $row[ "days" ] . "</td>";
				echo "<td>" . convertTime($row[ "startTime" ]) . "</td>";
				echo "<td>" . convertTime($row[ "endTime" ]) . "</td>";
				echo "<td>" . $row[ "ISBN" ] . "</td>";
				echo "<td>" . $row[ "bookTitle" ] . "</td>";
				echo "<td>" . $row[ "author" ] . "</td>";
				echo "<td>" . $row[ "publisher" ] . "</td>";
				echo "<td>" . $row[ "edition" ] . "</td>";
				echo "</tr>";
			}

			echo "</table>";

			// Clean up
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
	{
		// Bail out here
		printf( "Connection failed: %s<br>" , $mysqli->connect_error );
		exit();
	}
?>

  </div>
 </body>
</html>
