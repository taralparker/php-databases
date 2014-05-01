<?php
	$pageSessionType = "instructor";
	include "sessionValidator.php";
	include "databaseSettings.php";
	include "util.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Textbook Editor</title>
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
		//@TODO: get the semester/year dynamically instead of hardcoding it

		$coursesSql = "
		select * from taughtBy natural join Sections natural join consistsOf natural join Courses where rNumber=$_SESSION[rNumber] and year=$year and semester='$semester' order by courseCode
		";
		if( $result1 = $mysqli->query( "select lastName, firstName from Instructors where rNumber = $_SESSION[rNumber];" ) )
		{
			//This puts the first (technically the next remaining) row into the $row variable, whose columns can be accessed as follows
			$row1 = $result1->fetch_array( MYSQLI_ASSOC );
			//echo prints out html code, . (period) is the php string concatenation operator, and $row['attributeName'] returns the value of that attribute in this row
			echo "<p>Textbook information for $row1[firstName] $row1[lastName] ($_SESSION[rNumber]) in $semester $year.</p>";
			$result1->close();
		}
		else
		{
			echo $mysqli->error;
			echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
		}


		if( $result2 = $mysqli->query( $coursesSql ) )
		{
			echo "<table border='1' id='htmlgrid' class='testgrid'>
			<tr>
			<th style='display:none; visibility:collapse;'></th>
			<th>ISBN</th>
			<th>Book Title</th>
			<th>Authors</th>
			<th>Publisher</th>
			<th>Edition</th>
			</tr>";

			while( $row2 = $result2->fetch_array( MYSQLI_ASSOC ) )
			{
				echo "<tr><th colspan=6>CS $row2[courseCode]: $row2[courseTitle] - $row2[sectionNumber]</th></tr>";

				$textbookSql = "select * from usesBook natural join taughtBy natural join Textbooks natural join consistsOf where courseCode=$row2[courseCode] and rNumber=$_SESSION[rNumber] order by catalogYear desc, semester desc limit 1";

				if( $result3 = $mysqli->query( $textbookSql ) )
				{
					$row3 = $result3->fetch_array( MYSQLI_ASSOC );

					echo "<tr id='$row3[CRN]'>";
					echo "<td style='display:none; visibility:collapse;'></td>";
					echo "<td>" . $row3[ "ISBN" ] . "</td>";
					echo "<td>" . $row3[ "bookTitle" ] . "</td>";
					echo "<td>" . $row3[ "author" ] . "</td>";
					echo "<td>" . $row3[ "publisher" ] . "</td>";
					echo "<td>" . $row3[ "edition" ] . "</td>";
					echo "</tr>";
					$result3->close();
				}
				else
				{
					echo "<tr><th colspan=5 style=\"background: red;\">Could not retrieve textbook information.</th></tr>";
				}

			}

			echo "</table>";
			$result2->close();

		}
		else
			echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
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
		editableGrid = new EditableGrid( "Textbook Table" , { editMode: "relative", enableSort: false, modelChanged: function( rowIndex , columnIndex , oldValue , newValue , row ) {
			updateCellValue( this , rowIndex , columnIndex , oldValue , newValue , row );
		} } );

		// Build and load the metadata in JS
		editableGrid.load(
			{ metadata: [
				{ name: "empty", datatype: "string", editable: false },
				{ name: "ISBN", datatype: "string", editable: true, values: {
<?php
	$sql = "SELECT DISTINCT ISBN FROM Textbooks ORDER BY ISBN ASC;";

	$result = $mysqli->query( $sql );

	for( $index = 0 ; $row = $result->fetch_array( MYSQLI_NUM ) ; $index++ )
	{
		if( $index )
			echo " , ";
		echo "'$row[0]' : '$row[0]'";
	}

	$result->close();
?>
				} },
				{ name: "bookTitle", datatype: "string", editable: false },
				{ name: "authors", datatype: "string", editable: false },
				{ name: "publisher", datatype: "string", editable: false },
				{ name: "edition", datatype: "string", editable: false }
		] } );

		// Attach to the HTML table and render
		editableGrid.attachToHTMLTable( "htmlgrid" );
		editableGrid.renderGrid();
	}
}

function updateCellValue( editableGrid , rowIndex , columnIndex , oldValue , newValue , row , onResponse )
{
	// Generate query
	var sql = "UPDATE usesBook SET ISBN = '" + newValue + "' WHERE CRN = " + editableGrid.getRowId( rowIndex ) + " and semester = '" + <?php echo "'$semester'"; ?> + "' and year = " + <?php echo $year; ?> + " and ISBN = " + oldValue + ";";
	//console.log( sql );

	var data = doQuery( sql );
	//console.log( data );

	if( data.success )
	{
		if( !data.affectedRows )
		{
			// Tuple doesn't exist

			sql = "INSERT INTO usesBook ( CRN , semester , year , ISBN ) VALUES ( " + editableGrid.getRowId( rowIndex ) + " , '" + <?php echo "'$semester'"; ?> + "' , " + <?php echo $year; ?> + " , " + newValue + " );";
			//console.log( sql );

			data = doQuery( sql );
			//console.log( data );

			if( !data.success )
			{
				editableGrid.setValueAt( rowIndex , columnIndex , oldValue );
				return;
			}
		}

		sql = "SELECT * FROM Textbooks WHERE ISBN = " + newValue + ";";
		//console.log( sql );

		data = doQuery( sql );
		//console.log( data );

		editableGrid.setValueAt( rowIndex , 2 , data.success ? data.value[ 0 ].bookTitle : "Unknown" , true );
		editableGrid.setValueAt( rowIndex , 3 , data.success ? data.value[ 0 ].author : "Unknown" , true );
		editableGrid.setValueAt( rowIndex , 4 , data.success ? data.value[ 0 ].publisher : "Unknown" , true );
		editableGrid.setValueAt( rowIndex , 5 , data.success ? data.value[ 0 ].edition : "Unknown" , true );
	}
	else
		editableGrid.setValueAt( rowIndex , columnIndex , oldValue );
}
</script>

<?php $mysqli->close(); ?>
