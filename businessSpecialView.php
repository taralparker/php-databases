<?php
	$pageSessionType = "business";
	include "sessionValidator.php";
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Special View</title>
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

      <form method="post">
       <input type="submit" value="Show">
       Last
       <input name="nYears" type="text" size="4" autocomplete="off" value="<?php echo isset( $_POST[ "nYears" ] ) && is_numeric( $_POST[ "nYears" ] ) ? $_POST[ "nYears" ] : 5 ?>">
       Year(s)
      </form>

<?php
	if( isset( $_POST[ "nYears" ] ) && is_numeric( $_POST[ "nYears" ] ) )
	{
		$mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

		if( !$mysqli->connect_errno )
		{
			$sql = "SELECT courseCode , sectionNumber , firstName , lastName , courseTitle , semester , year , enrollment from Sections NATURAL JOIN consistsOf NATURAL JOIN Courses NATURAL JOIN taughtBy NATURAL JOIN Instructors WHERE ( courseCode = 5331 or courseCode = 5332 ) and year >= " . ( date( "Y" ) - $_POST[ "nYears" ] ) . " ORDER BY courseCode ASC , year DESC , semester DESC;";

			if( $result = $mysqli->query( $sql ) )
			{
				echo "<table border='1' id='htmlgrid' class='testgrid'>
				<tr>
				<th>Course Code</th>
				<th>Section</th>
				<th>First Name</th>
				<th>Last Name</th>
				<th>Course Title</th>
				<th>Semester</th>
				<th>Year</th>
				<th>Enrollment</th>
				</tr>";

				while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
				{
					echo "<tr>";
					echo "<td>" . $row[ "courseCode" ] . "</td>";
					echo "<td>" . $row[ "sectionNumber" ] . "</td>";
					echo "<td>" . $row[ "firstName" ] . "</td>";
					echo "<td>" . $row[ "lastName" ] . "</td>";
					echo "<td>" . $row[ "courseTitle" ] . "</td>";
					echo "<td>" . $row[ "semester" ] . "</td>";
					echo "<td>" . $row[ "year" ] . "</td>";
					echo "<td>" . $row[ "enrollment" ] . "</td>";
					echo "</tr>";
				}

				echo "</table>";
				$result->close();
			}
			else
				echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";

			$mysqli->close();
		}
		else
			echo "<div align='center'>Unable to connect to database.</div>";
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

<!-- JavaScript -->
<script src="js/editablegrid-2.0.1.js"></script>

<script>
window.onload = function()
{
	if( document.getElementById( "htmlgrid" ) )
	{
		editableGrid = new EditableGrid( "Special Table" , { editMode: "relative" } );

		// Build and load the metadata in JS
		editableGrid.load(
			{ metadata: [
				{ name: "courseCode", datatype: "string", editable: false },
				{ name: "section", datatype: "string", editable: false },
				{ name: "firstName", datatype: "string", editable: false },
				{ name: "lastName", datatype: "string", editable: false },
				{ name: "courseTitle", datatype: "string", editable: false },
				{ name: "semester", datatype: "string", editable: false },
				{ name: "year", datatype: "string", editable: false },
				{ name: "enrollment", datatype: "integer", editable: false }
		] } );

		// Attach to the HTML table and render
		editableGrid.attachToHTMLTable( "htmlgrid" );
		editableGrid.renderGrid();
	}
}
</script>
