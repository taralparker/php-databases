<?php
	$pageSessionType = "instructor";
	include "sessionValidator.php";
	include "databaseSettings.php";
	include "util.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Request Editor</title>
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
		$year = isset( $_SESSION[ "demoYear" ] ) ? $_SESSION[ "demoYear" ] : 2014;

        $sql = "SELECT courseCode , catalogYear , justification FROM Requests WHERE rNumber = " . $_SESSION[ "rNumber" ] .  " ORDER BY catalogYear DESC;";

		if( $result = $mysqli->query( $sql ) )
		{
			echo "<table border='1' id='htmlgrid' class='testgrid'>
			<tr>
			<th>Catalog Year</th>
			<th>Course Code</th>
			<th>Justification</th>
			</tr>";

			//Display result in a table
			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				echo "<tr id='$row[catalogYear]$row[courseCode]'>";
				echo "<td>" . catalogYearToHumanReadable( $row[ "catalogYear" ] ) . "</td>";
				echo "<td>" . $row[ "courseCode" ] . "</td>";
				echo "<td>" . $row[ "justification" ] . "</td>";
				echo "</tr>";
			}

			echo "</table>";
			$result->close();
?>
      <br>

      <input id="fromYear" type="text" size="4" value="<?php echo $year; ?>" onchange="catalogYearChanged()">
      -
      <input id="toYear" size="4" value="<?php echo $year + 1; ?>" readonly>

      <select id="courseCode">
<?php
	$query = "SELECT DISTINCT courseCode FROM Courses;";
	$result = $mysqli->query( $query );

	while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
		echo "<option value='" . $row[ "courseCode" ] . "'>" . "CS " . $row[ "courseCode" ] . "</option>";
?>
      </select>
      <br>

      <textarea id="justification" rows="4" style="resize: vertical; width: 90%;"></textarea>
      <br>

      <button onclick="addRequest();">Add / Update</button>

<?php
		}
		else
		{
			echo $mysqli->error;
			echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
		}

		$mysqli->close();
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
<script src="js/jquery-1.7.2.min.js" ></script>
<script src="js/util.js"></script>

<script>
function addRequest()
{
	var fromYear = document.getElementById( "fromYear" ).value;
	var toYear = document.getElementById( "toYear" ).value;
	var catalogYear = fromYear + toYear;
	var courseCode = document.getElementById( "courseCode" ).value;
	var justification = document.getElementById( "justification" ).value;

	if( justification.match( /\S+/g ).length < 200 )
	{
		var sql = "SELECT rNumber FROM Requests WHERE rNumber = " + <?php echo $_SESSION[ "rNumber" ]; ?> + " and courseCode = " + courseCode + " and catalogYear = " + catalogYear + ";";
		console.log( sql );

		var data = doQuery( sql );
		console.log( data );

		if( data.success )
		{
			if( data.affectedRows )
			{
				var sql = "UPDATE Requests SET justification = '" + mysql_real_escape_string( justification ) + "' WHERE rNumber = " + <?php echo $_SESSION[ "rNumber" ]; ?> + " and courseCode = " + courseCode + " and catalogYear = " + catalogYear + ";";
				//console.log( sql );

				var data = doQuery( sql );
				//console.log( data );

				if( data.success )
				{
					if( data.affectedRows )
					{
						var row = document.getElementById( catalogYear + courseCode );
						row.cells[ 2 ].innerHTML = justification;
					}
				}
				else
					alert( data.errorString );
			}
			else
			{
				sql = "INSERT INTO Requests ( rNumber , courseCode , catalogYear , justification ) VALUES ( " + <?php echo $_SESSION[ "rNumber" ]; ?> + " , " + courseCode + " , " + catalogYear + " , '" + mysql_real_escape_string( justification ) + "' );";
				//console.log( sql );

				data = doQuery( sql );
				//console.log( data );

				if( data.success )
				{
					var table = document.getElementById( "htmlgrid" );

					var row = table.insertRow( -1 );
					row.id = catalogYear + courseCode;
					row.insertCell( 0 ).innerHTML = fromYear + "-" + toYear;
					row.insertCell( 1 ).innerHTML = courseCode;
					row.insertCell( 2 ).innerHTML = justification;
					window.scrollTo( 0 , document.body.scrollHeight );
				}
				else
					alert( data.errorString );
			}
		}
		else
			alert( data.errorString );
	}
	else
		alert( "Justification must be less that 200 words." );
}

function catalogYearChanged()
{
	var fromYear = document.getElementById( "fromYear" );
	var toYear = document.getElementById( "toYear" );

	if( fromYear.value <= 1920 || fromYear.value >= 3000 )
		fromYear.value = <?php echo $year; ?>;

	toYear.value = ( fromYear.value - 0 ) + 1;
}
</script>
