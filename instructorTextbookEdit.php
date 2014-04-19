<!--Copied from templateTable.php-->
<?php
	// Change this to "faculty", "instructor", or "business"
	$pageSessionType = "instructor";
	include "sessionValidator.php";
	// Include this for global database access variables
	include "databaseSettings.php";
?>
<!-- This is actual html-->
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <!-- Set page title here -->
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
		//This function converts a string from military time to HH:MM format
		$timeString = ((floor($time/100) % 12 == 0) ? "12" : (floor($time/100) % 12)) . ":" . (($time % 100 == 0) ? "00" : ($time % 100));
		return $timeString;
	}
	
	//Creates a new MySQL object to connect to the local database
 	$mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

	if( !$mysqli->connect_errno )
	{
		//For now, year and semester either default to Spring 2014 or can be changed in instructorHome.php and kept track of by the session
		$year = isset( $_SESSION[ "demoYear" ] ) ? $_SESSION[ "demoYear" ] : 2014;
		$semester = isset( $_SESSION[ "demoSemester" ] ) ? $_SESSION[ "demoSemester" ] : "Spring";
		//@TODO: get the semester/year dynamically instead of hardcoding it
		
		//The SQL query, whitespace doesn't matter (except for spaces between words ofc)
		//year=$year because it's an integer, semester='$semester' because it's a string
		//rNumber is stored by the session, so that's how it's accessed
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
		//Can also run queries like this, from inline strings
		if( $result = $mysqli->query( "select lastName, firstName from instructors where rNumber = $_SESSION[rNumber];" ) )
		{
			//This puts the first (technically the next remaining) row into the $row variable, whose columns can be accessed as follows
			$row = $result->fetch_array( MYSQLI_ASSOC );
			//echo prints out html code, . (period) is the php string concatenation operator, and $row['attributeName'] returns the value of that attribute in this row
			echo "<p>Textbook information for " . $row [ "firstName" ] . " " . $row [ "lastName" ] . " (" . $_SESSION['rNumber'] . ") in " . $semester . " " . $year . ".</p>";
		}
		else
		{
			echo $mysqli->error;
			echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
		}
		// Start making the table
		//Here we use the complicated query from above
		if( $result = $mysqli->query( $sql ))
		{
		//Just create the header row with titles
		//Need this id and class on the table for css and javascript purposes
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
			//Iterate through the rows returned
			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				//Print out rows to match the header, the attributeNames are just what they would be in SQL
				//Times are converted to look good
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

			echo "</table>"; //Close the table
			//Everything past here is error stuff and boilerplate
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
