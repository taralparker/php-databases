<?php
	// Change this to "faculty", "instructor", or "business"
	$pageSessionType = "business";
	include "sessionValidator.php";
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Textbook View</title>
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
		if( $result = $mysqli->query( "select distinct courseCode from Courses order by courseCode;"))
		{
			$year = isset( $_SESSION[ "demoYear" ] ) ? $_SESSION[ "demoYear" ] : 2014;
			$semester = isset( $_SESSION[ "demoSemester" ] ) ? $_SESSION[ "demoSemester" ] : "Spring";
			echo "<form name=\"courseSelectionForm\"action=\"businessTextView.php\" method=\"POST\">
				<select size=\"7\"name=\"courseSelect\" id=\"courseSelect\">";
			if(empty( $_POST ) || empty( $_POST[ "courseSelect" ]) || !is_numeric($_POST[ "courseSelect" ]))
			{
				echo "<option selected=\"selected\">Select a course</option>";
			}
			else
			{
				echo "<option>Select a course</option>";
			}
			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				$selected = ($row[ "courseCode" ] == $_POST[ "courseSelect" ]);
				echo "<option " . ($selected ? "selected=\"selected\"" : "") . "value=\"" . $row[ "courseCode" ] . "\">CS " . $row[ "courseCode" ] . "</option>";
			}
			echo "</select>";
			echo "<input type=\"submit\" value=\"Submit\"></form>";

			if(!empty( $_POST ) && !empty( $_POST[ "courseSelect" ]))
			{
				if(is_numeric($_POST[ "courseSelect" ]))
				{
					echo "<h1>Textbooks instructors have used for CS" . $_POST[ "courseSelect"] . "</h1>";
					if( $result = $mysqli->query( "select concat(firstName, \" \", lastName) as name, lastName, firstName, ISBN, bookTitle, author, publisher, edition, year, semester from Sections natural join consistsOf natural join Instructors natural join taughtBy natural join usesBook natural join Textbooks where courseCode=$_POST[courseSelect] order by lastName, firstName, year desc, semester;" ))
					{
						$professorName = "";
						echo "<table border='1' id='textbookTable' class='testgrid'>";

						while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
						{
							if($professorName != $row[ "name" ])
							{
								$professorName = $row[ "name" ];
								echo "<tr><td colspan=5 style=\"text-align: center; text-size: 125%; font-weight: bold;\">$professorName</td></tr>
									<tr><th>Year</th>
									<th>Semester</th>
									<th>Book Title</th>
									<th>Author(s)</th>
									<th>Edition</th></tr>";
							}
							echo "<tr>";
							echo "<td>" . $row[ "year" ] . "</td>
							<td>" . $row[ "semester" ] . "</td>
							<td>" . $row[ "bookTitle" ] . "</td>
							<td>" . $row[ "author" ] . "</td>
							<td>" . $row[ "edition" ] . "</td></tr>";
						}
						echo "</table>";
					}
					else
					{
						echo $mysqli->error;
						echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
					}
				}
			}
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
