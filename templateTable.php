<?php
	// Change this to "faculty", "instructor", or "business"
	$pageSessionType = "instructor";
	include "sessionValidator.php";
	// Include this for global database access variables
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <!-- Set page title here -->
  <title>Template Page</title>
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

<?php
 	$mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

	if( !$mysqli->connect_errno )
	{
		$sql = "SELECT * from tas;";

		if( $result = $mysqli->query( $sql ) )
		{
			echo "<table border='1' id='htmlgrid' class='testgrid'>
			<tr>
			<th>Last Name</th>
			<th>First Name</th>
			</tr>";

			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				echo "<tr>";
				echo "<td>" . $row[ "lastName" ] . "</td>";
				echo "<td>" . $row[ "firstName" ] . "</td>";
				echo "</tr>";
			}

			echo "</table>";
			$result->close();
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
 </body>
</html>
