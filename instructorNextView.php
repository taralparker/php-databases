<?php
	$pageSessionType = "instructor";
	include "sessionValidator.php";
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Future Classes</title>
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

 <!--
 <div id="wrap">
  <!-- Feedback message zone -- >
  <div id="message"></div>

  <!-- Grid contents -- >
  <div id="tablecontent"></div>

  <!-- Paginator control -- >
  <div id="paginator"></div>
 </div>
 -->

<?php
 	$mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

	if( !$mysqli->connect_errno )
	{
		$year = isset( $_SESSION[ "demoYear" ] ) ? $_SESSION[ "demoYear" ] : 2014;
		$semester = isset( $_SESSION[ "demoSemester" ] ) ? $_SESSION[ "demoSemester" ] : "Spring";

		if( $semester == "Fall" )
		{
			$year++;
			$semester = "Spring";
		}
		else if( $semester == "Spring" )
			$semester = "Summer I";
		else if( $semester == "Summer I" )
			$semester = "Summer II";
		else if( $semester == "Summer II" )
			$semester = "Fall";

		$sql = "
		select courseCode, startTime, endTime, days, room, bldg
		from sections natural join consistsof
		where year = $year and semester = '$semester' and CRN in (select CRN from taughtby where rNumber = $_SESSION[rNumber] and year = $year );
		";

		if( $result = $mysqli->query( $sql ) )
		{
			echo "<table border='1' id='htmlgrid' class='testgrid'>
			<tr>
			<th>Course Code</th>
			<th>Start Time</th>
			<th>End Time</th>
			<th>Days</th>
			<th>Room</th>
			<th>Building</th>
			</tr>";

			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				echo "<tr>";
				echo "<td>" . $row[ "courseCode" ] . "</td>";
				echo "<td>" . number_format( ( $row[ "startTime" ] >= 1300 ? $row[ "startTime" ] - 1200 : $row[ "startTime" ] ) / 100 , 2 , ':' , '' ) . ( $row[ "startTime" ] < 1200 ? " AM" : " PM" ) . "</td>";
				echo "<td>" . number_format( ( $row[ "endTime" ] >= 1300 ? $row[ "endTime" ] - 1200 : $row[ "endTime" ] ) / 100 , 2 , ':' , '' ) . ( $row[ "endTime" ] < 1200 ? " AM" : " PM" ) . "</td>";
				echo "<td>" . $row[ "days" ] . "</td>";
				echo "<td>" . $row[ "room" ] . "</td>";
				echo "<td>" . $row[ "bldg" ] . "</td>";
				echo "</tr>";
			}

			echo "</table>";
			$result->close();
		}

		$mysqli->close();
	}
	else
		printf( "Connection failed: %s<br>" , $mysqli->connect_error );
?>

  </div>
 </body>
</html>

<!-- JavaScript -->
<script src="js/editablegrid-2.0.1.js"></script>
<script src="js/jquery-1.7.2.min.js" ></script>

<script>
window.onload = function()
{
	editableGrid = new EditableGrid( "Future Classes" , { editMode: "absolute" } );

	// Build and load the metadata in JS
	editableGrid.load(
		{ metadata: [
			{ name: "courseCode", datatype: "string", editable: false },
			{ name: "startTime", datatype: "hours", editable: false },
			{ name: "endTime", datatype: "string", editable: false },
			{ name: "days", datatype: "string", editable: false },
			{ name: "room", datatype: "string", editable: false },
			{ name: "building", datatype: "string", editable: false }
	] } );

	// Attach to the HTML table and render
	editableGrid.attachToHTMLTable( "htmlgrid" );
	editableGrid.renderGrid();
}
</script>
