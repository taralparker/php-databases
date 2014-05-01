<?php
	$pageSessionType = "instructor";
	include "sessionValidator.php";
	include "databaseSettings.php";
	include "util.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Preference Editor</title>
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
	   <input type="submit" value="View">
       <input id="fromYear" name="fromYear" type="text" autocomplete="off" size="4" value="<?php echo isset( $_POST[ "fromYear" ] ) ? $_POST[ "fromYear" ] : date( "Y" ); ?>" onchange="catalogYearChanged();">
       -
       <input id="toYear" name="toYear" type="text" autocomplete="off" size="4" value="<?php echo isset( $_POST[ "toYear" ] ) ? $_POST[ "toYear" ] : ( date( "Y" ) + 1 ); ?>" readonly>
      </form>

<?php
	if( isset( $_POST[ "fromYear" ] ) && isset( $_POST[ "toYear" ] ) )
	{
		$mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

		if( !$mysqli->connect_errno )
		{
			//$sql = "SELECT courseCode , courseTitle , rating FROM Prefers JOIN Courses WHERE rNumber = " . $_SESSION[ "rNumber" ] . " and catalogYear = " . $_POST[ "fromYear" ] . $_POST[ "toYear" ] . " ORDER BY courseCode ASC;";
			//$sql = "SELECT courseCode , courseTitle FROM Courses WHERE catalogYear = " . $_POST[ "fromYear" ] . $_POST[ "toYear" ] . ";";
			$sql = "
			SELECT c.courseCode , courseTitle , rating , previousRating
			FROM
			(
				SELECT a.courseCode as courseCode , courseTitle , rating
				FROM
				(
					SELECT distinct courseCode , courseTitle
					FROM Courses
				) AS a
				LEFT JOIN
				(
					SELECT courseCode , rating
					FROM Prefers
					WHERE rNumber = " . $_SESSION[ "rNumber" ] . " and catalogYear = " . $_POST[ "fromYear" ] . $_POST[ "toYear" ] . "
				) AS b
				ON a.courseCode = b.courseCode
			) AS c
			LEFT JOIN
			(
				SELECT courseCode , rating as previousRating
				FROM Prefers
				WHERE rNumber = " . $_SESSION[ "rNumber" ] . " and catalogYear = " . ( $_POST[ "fromYear" ] - 1 ) . ( $_POST[ "toYear" ] - 1 ) . "
			) AS d
			ON c.courseCode = d.courseCode;
			";

			if( $result = $mysqli->query( $sql ) )
			{
				echo "<table border='1' id='htmlgrid' class='testgrid'>
				<tr>
				<th>Course Code</th>
				<th>Course Title</th>
				<th>Rating</th>
				<th>Previous Rating</th>
				</tr>";

				while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
				{
					echo "<tr id='$row[courseCode]'>";
					echo "<td>" . $row[ "courseCode" ] . "</td>";
					echo "<td>" . $row[ "courseTitle" ] . "</td>";
					echo "<td>" . $row[ "rating" ] . "</td>";
					echo "<td>" . $row[ "previousRating" ] . "</td>";
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
<script src="js/jquery-1.7.2.min.js" ></script>
<script src="js/util.js" ></script>

<script>
window.onload = function()
{
	if( document.getElementById( "htmlgrid" ) )
	{
		editableGrid = new EditableGrid( "Prefers Table" , { editMode: "relative", enableSort: true, modelChanged: function( rowIndex , columnIndex , oldValue , newValue , row ) {
			updateCellValue( this , rowIndex , columnIndex , oldValue , newValue , row );
		} } );

		// Build and load the metadata in JS
		editableGrid.load(
			{ metadata: [
				{ name: "courseCode", datatype: "string", editable: false },
				{ name: "courseTitle", datatype: "string", editable: false },
				{ name: "rating", datatype: "string", editable: true , values: { "NULL" : "" , "1" : "1" , "2" : "2" , "3" : "3" } },
				{ name: "previousRating", datatype: "string", editable: false }
		] } );

		// Attach to the HTML table and render
		editableGrid.attachToHTMLTable( "htmlgrid" );
		editableGrid.renderGrid();
	}
}

function updateCellValue( editableGrid , rowIndex , columnIndex , oldValue , newValue , row , onResponse )
{
	if( newValue == "NULL" )
		newvalue = 0;
	newValue -= 0;

	if( !oldValue && newValue )
	{
		var sql = "INSERT INTO Prefers ( rNumber , courseCode , catalogYear , rating ) VALUES ( " + <?php echo $_SESSION[ "rNumber" ]; ?> + " , " + editableGrid.getRowId( rowIndex ) + " , " + <?php echo $_POST[ "fromYear" ] . $_POST[ "toYear" ] ?> + " , " + newValue + " );";
		console.log( sql );

		var data = doQuery( sql );
		console.log( data );

		if( !data.success )
			editableGrid.setValueAt( rowIndex , columnIndex , oldValue );
	}
	else if( oldValue && newValue )
	{
		var sql = "UPDATE Prefers SET rating = " + newValue + " WHERE rNumber = " + <?php echo $_SESSION[ "rNumber" ]; ?> + " and courseCode = " + editableGrid.getRowId( rowIndex ) + " and catalogYear = " + <?php echo $_POST[ "fromYear" ] . $_POST[ "toYear" ] ?> + ";";
		console.log( sql );

		var data = doQuery( sql );
		console.log( data );

		if( !data.success )
			editableGrid.setValueAt( rowIndex , columnIndex , oldValue );
	}
	else if( oldValue && !newValue )
	{
		var sql = "DELETE FROM Prefers WHERE rNumber = " + <?php echo $_SESSION[ "rNumber" ]; ?> + " and courseCode = " + editableGrid.getRowId( rowIndex ) + " and catalogYear = " + <?php echo $_POST[ "fromYear" ] . $_POST[ "toYear" ] ?> + ";";
		console.log( sql );

		var data = doQuery( sql );
		console.log( data );

		editableGrid.setValueAt( rowIndex , columnIndex , data.success ? "" : oldValue );
	}
}

function catalogYearChanged()
{
	var fromYear = document.getElementById( "fromYear" );
	var toYear = document.getElementById( "toYear" );

	if( fromYear.value <= 1920 || fromYear.value >= 3000 )
		fromYear.value = <?php echo date( "Y" ); ?>;

	toYear.value = ( fromYear.value - 0 ) + 1;
}
</script>
