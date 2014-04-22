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
	$mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

	if( !$mysqli->connect_errno )
	{
		$sql = "SELECT rNumber , courseCode , semester , catalogYear , rating , CRN
		FROM prefers NATURAL JOIN consistsof
		WHERE year = $_SESSION[demoYear]
		ORDER BY rNumber;";

		$sql = "
		select distinct rNumber , courseCode , semester , rating
		from prefers natural join consistsof
		where ( catalogYear = 1213 and semester <> 'Fall' )
		or ( catalogYear = 1314 and semester = 'Fall' )
		ORDER BY rNumber;
		";

		if( $result = $mysqli->query( $sql ) )
		{
			echo "<table border='1' id='htmlgrid' class='testgrid'>
			<tr>
			<th>RNumber</th>
			<th>Course Code</th>
			<th>Semester</th>
			<th>Rating</th>
			</tr>";

			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				echo "<tr id=" . $row[ "rNumber" ] . ">";
				echo "<td>" . $row[ "rNumber" ] . "</td>";
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
