<?php
	// Change this to "faculty", "instructor", or "business"
	$pageSessionType = "business";
	include "sessionValidator.php";
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Course View</title>
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
			echo "<form name=\"courseSelectionForm\" action=\"businessCourseView.php\" method=\"POST\">
				<select size=\"7\"name=\"courseSelect\" id=\"courseSelect\" onchange=\"this.form.submit();\">";
			$selectedCourse = "";
			if( !empty( $_POST ) && !empty( $_POST[ "courseSelect" ]))
			{
				$selectedCourse = $_POST[ "courseSelect"];	
			}
			else
			{
				echo "<option selected=\"selected\">Select a course</option>";
			}
			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				$selected = in_array($row[ "courseCode" ], $_POST[ "courseSelect" ]);//$row[ "courseCode" ] == $selectedCourse;
				echo "<option " . ($selected ? "selected=\"selected\"" : "") . "value=\"" . $row[ "courseCode" ] . "\">" . $row[ "courseCode" ] . "</option>";
			}
			echo "</select></form>";
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
