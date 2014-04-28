<?php

/**
 * Created by phpDesigner8
 * Author: Eric
 */
 
    //Permission
	$pageSessionType = "faculty";
	include "sessionValidator.php";
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Professor Editor</title>
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
		if( $result = $mysqli->query( "SELECT * FROM Sections;" ) )
		{
			echo "<table border='1' id='htmlgrid' class='testgrid' style='font-size:11px;'>
			<tr>
			<th>CRN</th>
			<th>Year</th>
			<th>Section Number</th>
			<th>Type</th>
			<th>Semester</th>
			<th>Days</th>
			<th>Start Time</th>
            <th>End Time</th>
            <th>Enrollment</th>
            <th>Capacity</th>
			</tr>";
			//<th>Load Preference</th>

			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				echo '<tr id="' . $row[ "CRN" ] . '">';
				echo "<td>" . $row[ "year" ] . "</td>";
				echo "<td>" . $row[ "sectionNumber" ] . "</td>";
				echo "<td>" . $row[ "type" ] . "</td>";
				echo "<td>" . $row[ "semester" ] . "</td>";
				echo "<td>" . $row[ "days" ] . "</td>";
				echo "<td>" . $row[ "startTime" ] . "</td>";
				echo "<td>" . $row[ "endTime" ] . "</td>";
                echo "<td>" . $row[ "enrollment" ] . "</td>";
				echo "<td>" . $row[ "capacity" ] . "</td>";
				echo "</tr>";
			}

			echo "</table>";
		}

		// Free up memory
		$result->close();
		$mysqli->close();
	}
	else
	{
		printf( "Connection failed: %s<br>" , $mysqli->connect_error );
		exit();
	}
?>

     </div>
    </td>
    <td id="bodyTableRight">
    </td>
   </tr>
  </table>
 </body>
</html>