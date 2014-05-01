<?php
	// Change this to "faculty", "instructor", or "business"
	$pageSessionType = "faculty";
	include "sessionValidator.php";
	include "databaseSettings.php";
	include "util.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Edit TAs</title>
  <link rel="stylesheet" href="css/style.css" type="text/css" media="screen">
  <script language="JavaScript">
    function toggle(id)
	{
        var state = document.getElementById(id).style.display;
		if (state == 'block')
		{
			document.getElementById(id).style.display = 'none';
		}
		else
		{
			document.getElementById(id).style.display = 'block';
		}
    }
</script>
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
		return militaryTimeToNormalTime( $time );
		$timeString = ((floor($time/100) % 12 == 0) ? "12" : (floor($time/100) % 12)) . ":" . (($time % 100 == 0) ? "00" : ($time % 100));
		return $timeString;
	}

 	$mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

	if( !$mysqli->connect_errno )
	{
		$year = isset( $_SESSION[ "demoYear" ] ) ? $_SESSION[ "demoYear" ] : 2014;
		$semester = isset( $_SESSION[ "demoSemester" ] ) ? $_SESSION[ "demoSemester" ] : "Spring";

		if( $result = $mysqli->query( "
		select distinct courseCode
		from consistsOf natural join Courses
		where semester='$semester' and year=$year
		order by courseCode;"))
		{
			echo "<form name=\"courseSelectionForm\" action=\"facultyTAEdit.php\" method=\"POST\">
				<select size=\"7\"name=\"courseSelect[]\" multiple=\"yes\" id=\"courseSelect\" onchange=\"this.form.submit();\">";
			if(empty( $_POST ) || empty( $_POST[ "courseSelect" ]))
			{
				echo "<option selected=\"selected\">Select a course</option>";
			}
			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				$selected = in_array( $row[ "courseCode" ], $_POST[ "courseSelect" ]);
				echo "<option " . ($selected ? "selected=\"selected\"" : "") . "value=\"$row[courseCode]\">CS $row[courseCode] </option>";
			}
			echo "</select></form>";
		}
		else
		{
			echo $mysqli->error;
			echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
		}

		echo "<h2>Teaching assistants for $semester $year</h2>";



		if( !empty( $_POST ) && !empty( $_POST[ "courseSelect" ]))
		{
			echo "<table border='1' id='htmlgrid' class='testgrid'>";
			foreach ($_POST[ "courseSelect" ] as $course)
			{
				//echo "<h1><a name=\"$course\" href=\"#$course\" onclick=\"toggle('$course');\">CS $course</a></h1><div class='courseDiv' id='$course'>";
				echo "<tr><td colspan=10 style=\"font-weight: bold; font-size:125%; text-align:center;\">CS $course</td></tr>";
				$sql = "
				select * from
				Sections natural left outer join hasTA natural left outer join TAs
				where Sections.CRN in
				(select CRN from consistsOf
				where courseCode=$course and year=$year and semester='$semester') and Sections.year=$year and Sections.semester='$semester'";
				if( $result = $mysqli->query( $sql ) )
				{
					echo "
					<tr>
					<th style='display:none; visibility:collapse;'></th>
					<th>CRN</th>
					<th>Section</th>
					<th>Type</th>
					<th>Days</th>
					<th>Start Time</th>
					<th>End Time</th>
					<th>TA Name</th>
					<th>RNumber</th>
					<th>Hours/Week</th>
					</tr>";

					while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
					{
						echo "<tr id='$row[CRN]'>";
						echo "<td style='display:none; visibility:collapse;'></td>";
						echo "<td>" . $row[ "CRN" ] . "</td>";
						echo "<td>" . $row[ "sectionNumber" ] . "</td>";
						echo "<td>" . $row[ "type" ] . "</td>";
						echo "<td>" . $row[ "days" ] . "</td>";
						echo "<td>" . convertTime($row[ "startTime" ]) . "</td>";
						echo "<td>" . convertTime($row[ "endTime" ]) . "</td>";
						echo "<td>" . $row[ "firstName" ] . " " . $row[ "lastName" ] . "</td>";
						echo "<td>" . $row[ "rNumber" ] . "</td>";
						echo "<td>" . $row[ "hoursPerWeek" ] . "</td>";
						echo "</tr>";
					}

					$result->close();
				}
				else
				{
					echo "<tr><td colspan=10 style=\"background:red;\">Invalid request. Please contact a system administrator.</td></tr>";
				}
			}
			echo "</table>";
		}
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

<!-- JavaScript -->
<script src="js/editablegrid-2.0.1.js"></script>
<script src="js/jquery-1.7.2.min.js" ></script>
<script src="js/util.js" ></script>

<script>
window.onload = function()
{
	if( document.getElementById( "htmlgrid" ) )
	{
		editableGrid = new EditableGrid( "TA Table" , { editMode: "relative", enableSort: false, modelChanged: function( rowIndex , columnIndex , oldValue , newValue , row ) {
			updateCellValue( this , rowIndex , columnIndex , oldValue , newValue , row );
		} } );

		// Build and load the metadata in JS
		editableGrid.load(
			{ metadata: [
				{ name: "empty", datatype: "string", editable: false },
				{ name: "CRN", datatype: "string", editable: false },
				{ name: "section", datatype: "string", editable: false },
				{ name: "type", datatype: "string", editable: false },
				{ name: "days", datatype: "string", editable: false },
				{ name: "startTime", datatype: "string", editable: false },
				{ name: "endTime", datatype: "string", editable: false },
				{ name: "taName", datatype: "string", editable: false },
				{ name: "rNumber", datatype: "string", editable: true, values: {
<?php
	$sql = "SELECT rNumber FROM TAs ORDER BY rNumber ASC;";

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
				{ name: "hoursPerWeek", datatype: "string", editable: true }
		] } );

		editableGrid.addCellValidator( "hoursPerWeek" , new CellValidator(
		{
			isValid: function( value ) { return value >= 0.0; }
		} ) );

		// Attach to the HTML table and render
		editableGrid.attachToHTMLTable( "htmlgrid" );
		editableGrid.renderGrid();
	}
}

function updateCellValue( editableGrid , rowIndex , columnIndex , oldValue , newValue , row , onResponse )
{
	if( columnIndex == 8 )
	{
		if( !oldValue.length && newValue.length )
		{
			// Insert new tuple

			var sql = "INSERT INTO hasTA( CRN , semester , year , rNumber , hoursPerWeek ) VALUES ( " + editableGrid.getRowId( rowIndex ) + " , '" + <?php echo "'$semester'"; ?> + "' , " + <?php echo $year; ?> + " , " + newValue + " , 0 );";
			console.log( sql );

			var data = doQuery( sql );
			console.log( data );

			if( data.success )
			{
				sql = "SELECT CONCAT( firstName , ' ' , lastName ) as taName FROM TAs WHERE rNumber = " + newValue + ";";
				console.log( sql );

				var data = doQuery( sql );
				console.log( data );

				if( data.success )
					editableGrid.setValueAt( rowIndex , columnIndex - 1 , data.success ? data.value[ 0 ].taName : "Unknown" );

				editableGrid.setValueAt( rowIndex , columnIndex + 1 , 0 );
			}
		}
		else if( oldValue.length && newValue.length )
		{
			// Update tuple

			var sql = "UPDATE hasTA SET rNumber = " + newValue + " WHERE CRN = " + editableGrid.getRowId( rowIndex ) + " and semester = '" + <?php echo "'$semester'"; ?> + "' and year = " + <?php echo $year; ?> + " and rNumber = " + oldValue + ";";
			console.log( sql );

			var data = doQuery( sql );
			console.log( data );

			if( data.success )
			{
				var sql = "SELECT CONCAT( firstName , ' ' , lastName ) as taName FROM TAs WHERE rNumber = " + newValue + ";";
				console.log( sql );

				var data = doQuery( sql );
				console.log( data );

				if( data.success )
					editableGrid.setValueAt( rowIndex , columnIndex - 1 , data.success ? data.value[ 0 ].taName : "Unknown" );
			}
			else
				editableGrid.setValueAt( rowIndex , columnIndex , oldValue );
		}
		else if( oldValue.length && !newValue.length )
		{
			// Delete tuple

			var sql = "DELETE FROM hasTA WHERE CRN = " + editableGrid.getRowId( rowIndex ) + " and semester = '" + <?php echo "'$semester'"; ?> + "' and year = " + <?php echo $year; ?> + " and rNumber = " + oldValue + ";";
			console.log( sql );

			var data = doQuery( sql );
			console.log( data );

			if( data.success )
			{
				editableGrid.setValueAt( rowIndex , columnIndex - 1 , "" );
				editableGrid.setValueAt( rowIndex , columnIndex + 1 , "" );
			}
			else
				editableGrid.setValueAt( rowIndex , columnIndex , oldValue );
		}
	}
	else if( columnIndex == 9 )
	{
		var rNumber = editableGrid.getValueAt( rowIndex , 8 );

		if( rNumber.length )
		{
			var sql = "UPDATE hasTA SET hoursPerWeek = " + newValue + " WHERE CRN = " + editableGrid.getRowId( rowIndex ) + " and semester = '" + <?php echo "'$semester'"; ?> + "' and year = " + <?php echo $year; ?> + " and rNumber = " + rNumber + ";";
			console.log( sql );

			var data = doQuery( sql );
			console.log( data );

			if( !data.success )
				editableGrid.setValueAt( rowIndex , columnIndex , oldValue );
		}
		else
			editableGrid.setValueAt( rowIndex , columnIndex , "" );
	}
}
</script>

<?php $mysqli->close(); ?>
