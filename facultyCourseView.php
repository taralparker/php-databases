<?php
	$pageSessionType = "faculty";
	include "sessionValidator.php";
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Course View</title>
  <link rel="stylesheet" href="css/styleTest.css" type="text/css" media="screen">
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
		$year = isset( $_SESSION[ "demoYear" ] ) ? $_SESSION[ "demoYear" ] : 2014;

		if( $result = $mysqli->query( "SELECT courseCode , courseTitle FROM courses WHERE catalogYear = '" . $year . ( $year + 1 ) . "';" ) )
		{
			echo "<table border='1' id='htmlgrid' class='testgrid' style='font-size:11px;'>
			<tr>
			<th>Course Code</th>
			<th>Course Title</th>
			</tr>";

			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				echo '<tr id="' . $row[ "courseCode" ] . '">';
				echo "<td>" . $row[ "courseCode" ] . "</td>";
				echo "<td>" . $row[ "courseTitle" ] . "</td>";
				echo "</tr>";
			}

			echo "</table>";
		}

		// Clean up
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
			{ name: "courseTitle", datatype: "string", editable: false }
	] } );

	// Attach to the HTML table and render
	editableGrid.attachToHTMLTable( "htmlgrid" );
	editableGrid.renderGrid();
}
</script>
