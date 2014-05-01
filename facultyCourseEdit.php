<?php
	$pageSessionType = "faculty";
	include "sessionValidator.php";
	include "databaseSettings.php";
	include "util.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Course Editor</title>
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

      <form id="semesterForm" method="post">
       <input type="submit" value="Load">
       <select id="semester" name="semester">
<?php
	$semesters = array( "Fall" , "Spring" , "Summer I" , "Summer II" );

	for( $index = 0 ; $index < 4 ; $index++ )
		echo "<option value='" . $semesters[ $index ] . "'" . ( $_POST[ "semester" ] == $semesters[ $index ] ? "selected" : "" ) . ">" . $semesters[ $index ] . "</option>";
?>
       </select>
       <input id="year" name="year" type="text" size="4" autocomplete="off" value="<?php echo isset( $_POST[ "year" ] ) ? $_POST[ "year" ] : date( "Y" ); ?>">
      </form>

<?php
	if( isset( $_POST[ "semester" ] ) && isset( $_POST[ "year" ] ) && $_POST[ "year" ] > 1920 && $_POST[ "year" ] < 3000 )
	{
		$mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

		if( !$mysqli->connect_errno )
		{
			$sql = "SELECT CRN , courseCode , courseTitle , sectionNumber , type , rNumber , firstName , lastName , startTime , endTime , days , bldg , room , enrollment , capacity FROM Sections NATURAL JOIN consistsOf NATURAL JOIN Courses NATURAL JOIN taughtBy NATURAL JOIN Instructors WHERE year = " . $_POST[ "year" ] . " and semester = '" . $_POST[ "semester" ] . "' ORDER BY courseCode ASC , sectionNumber ASC;";

			if( $result = $mysqli->query( $sql ) )
			{
				echo "<table border='1' id='htmlgrid' class='testgrid'>
				<tr>
 				<th style='display:none; visibility:collapse;'></th>
				<th>Section</th>
				<th>Type</th>
				<th>RNumber</th>
				<th>Name</th>
				<th>Start Time</th>
				<th>End Time</th>
				<th>Days</th>
				<th>Room</th>
				<th>Building</th>
				<th>Capacity</th>
				<th>Enrollment</th>
				</tr>";

				$lastCourseCode = 0;
				while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
				{
					if( $lastCourseCode != $row[ "courseCode" ] )
					{
						echo "<tr><th colspan=12>$row[courseCode] - $row[courseTitle]</th></tr>";
						$lastCourseCode = $row[ "courseCode" ];
					}

					echo "<tr id='$row[CRN]'>";
					echo "<td style='display:none; visibility:collapse;'></td>";
					echo "<td>" . $row[ "sectionNumber" ] . "</td>";
					echo "<td>" . $row[ "type" ] . "</td>";
					echo "<td>" . $row[ "rNumber" ] . "</td>";
					echo "<td>" . $row[ "firstName" ] . " " . $row[ "lastName" ] . "</td>";
					//echo "<td>" . $row[ "lastName" ] . "</td>";
					echo "<td>" . militaryTimeToNormalTime( $row[ "startTime" ] ) . "</td>";
					echo "<td>" . militaryTimeToNormalTime( $row[ "endTime" ] ) . "</td>";
					echo "<td>" . $row[ "days" ] . "</td>";
					echo "<td>" . $row[ "bldg" ] . "</td>";
					echo "<td>" . $row[ "room" ] . "</td>";
					echo "<td>" . $row[ "enrollment" ] . "</td>";
					echo "<td>" . $row[ "capacity" ] . "</td>";
					echo "</tr>";
				}

				echo "</table>";
				$result->close();
			}
			else
				echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
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
		editableGrid = new EditableGrid( "Everything Table" , { editMode: "relative", enableSort: false, modelChanged: function( rowIndex , columnIndex , oldValue , newValue , row ) {
			updateCellValue( this , rowIndex , columnIndex , oldValue , newValue , row );
		} } );

		// Build and load the metadata in JS
		editableGrid.load(
			{ metadata: [
				{ name: "empty", datatype: "string", editable: false },
				{ name: "sectionNumber", datatype: "string", editable: false },
				{ name: "type", datatype: "string", editable: true, values: { "Lecture" : "Lecture" , "Lab" : "Lab" } },
				{ name: "rNumber", datatype: "string", editable: true, values: {
<?php
	$sql = "SELECT rNumber FROM Instructors ORDER BY rNumber ASC;";

	$result = $mysqli->query( $sql );

	echo "'' : ''";

	if( $result )
	{
		while( $row = $result->fetch_array( MYSQLI_NUM ) )
			echo " , '$row[0]' : '$row[0]'";

		$result->close();
	}
?>
				} },
				{ name: "name", datatype: "string", editable: false },
				{ name: "startTime", datatype: "string", editable: true },
				{ name: "endTime", datatype: "string", editable: true },
				{ name: "days", datatype: "string", editable: true },
				{ name: "bldg", datatype: "string", editable: true },
				{ name: "room", datatype: "string", editable: true },
				{ name: "enrollment", datatype: "string", editable: true },
				{ name: "capacity", datatype: "string", editable: true }
		] } );

		// Attach to the HTML table and render
		editableGrid.attachToHTMLTable( "htmlgrid" );
		editableGrid.renderGrid();
	}
}

function updateCellValue( editableGrid , rowIndex , columnIndex , oldValue , newValue , row , onResponse )
{
	var CRN = editableGrid.getRowId( rowIndex );
	var semester = <?php echo "'$_POST[semester]'"; ?>;
	var year = <?php echo $_POST[ "year" ]; ?>;

	if( columnIndex == 3 )
	{
		var sql = "UPDATE taughtBy SET rNumber = " + newValue + " WHERE CRN = " + CRN + " and semester = '" + semester + "' and year = " + year + " and rNumber = " + oldValue + ";";
		//console.log( sql );

		var data = doQuery( sql );
		//console.log( data );

		if( data.success )
		{
			var sql = "SELECT CONCAT( firstName , ' ' , lastName ) as name FROM Instructors WHERE rNumber = " + newValue + ";";
			//console.log( sql );

			var data = doQuery( sql );
			//console.log( data );

			editableGrid.setValueAt( rowIndex , columnIndex + 1 , data.success ? data.value[ 0 ].name : "Unknown" );
		}
		else
			editableGrid.setValueAt( rowIndex , columnIndex , oldValue );
	}
	else if( columnIndex == 5 || columnIndex == 6 )
	{
		var time = timeToMilitaryTime( newValue )

		if( time != -1 )
		{
			var sql = "UPDATE Sections SET " + editableGrid.getColumnName( columnIndex ) + " = " + time + " WHERE CRN = " + CRN + " and year = " + year + ";";
			//console.log( sql );

			var data = doQuery( sql );
			//console.log( data );

			editableGrid.setValueAt( rowIndex , columnIndex , data.success ? militaryTimeToNormalTime( time ) : oldValue );
		}
		else
			editableGrid.setValueAt( rowIndex , columnIndex , oldValue );
	}
	else
	{
		var sql = "UPDATE Sections SET " + editableGrid.getColumnName( columnIndex ) + " = '" + newValue + "' WHERE CRN = " + CRN + " and year = " + year + ";";
		//console.log( sql );

		var data = doQuery( sql );
		//console.log( data );

		editableGrid.setValueAt( rowIndex , columnIndex , data.success && data.affectedRows ? newValue : oldValue );
	}
}
</script>

<?php $mysqli->close(); ?>
