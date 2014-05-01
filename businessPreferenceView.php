<?php
	$pageSessionType = "business";
	include "sessionValidator.php";
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Preference View</title>
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
	if( isset( $_SESSION[ "demoYear" ] ) )
	{
		$mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

		if( !$mysqli->connect_errno )
		{
			$sql = "
			SELECT DISTINCT rNumber , lastName , firstName , courseCode , semester , rating
			FROM Prefers NATURAL JOIN consistsOf NATURAL JOIN Instructors
			WHERE year = $_SESSION[demoYear]
			ORDER BY rNumber;";

			if( $result = $mysqli->query( $sql ) )
			{
				echo "<table border='1' id='htmlgrid' class='testgrid'>
				<tr>
				<th>RNumber</th>
				<th>Last Name</th>
				<th>First Name</th>
				<th>Course Code</th>
				<th>Semester</th>
				<th>Rating</th>
				</tr>";

				while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
				{
					echo "<tr id=" . $row[ "rNumber" ] . ">";
					echo "<td>" . $row[ "rNumber" ] . "</td>";
					echo "<td>" . $row[ "firstName" ] . "</td>";
					echo "<td>" . $row[ "lastName" ] . "</td>";
					echo "<td>" . $row[ "courseCode" ] . "</td>";
					echo "<td>" . $row[ "semester" ] . "</td>";
					echo "<td>" . $row[ "rating" ] . "</td>";
					echo "</tr>";
				}

				echo "</table>";
				$result->close();
			}
			else
			{
				//echo $mysqli->error;
				echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
			}

			$mysqli->close();
		}
		else
			echo "<div align='center'>Unable to connect to database.</div>";
	}
	else
		echo "<div align='center'>Demonstration year not set.</div>";
?>

     </div>
    </td>
    <td id="bodyTableRight">
    </td>
   </tr>
  </table>
 </body>
</html>

<!-- JavaScript -->
<script src="js/editablegrid-2.0.1.js"></script>
<script src="js/jquery-1.7.2.min.js" ></script>

<script>
window.onload = function()
{
	if( document.getElementById( "htmlgrid" ) )
	{
		editableGrid = new EditableGrid( "Future Classes" , { editMode: "absolute" } );

		// Build and load the metadata in JS
		editableGrid.load(
			{ metadata: [
				{ name: "rNumber", datatype: "string", editable: false },
				{ name: "lastName", datatype: "string", editable: false },
				{ name: "firstName", datatype: "string", editable: false },
				{ name: "courseCode", datatype: "string", editable: false },
				{ name: "semester", datatype: "string", editable: false },
				{ name: "rating", datatype: "string", editable: false }
		] } );

		// Attach to the HTML table and render
		editableGrid.attachToHTMLTable( "htmlgrid" );
		editableGrid.renderGrid();
	}
}
</script>
