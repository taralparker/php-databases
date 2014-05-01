<?php
/**
 * Created by PhpStorm.
 * User: Tara
 * Date: 4/20/14
 * Time: 6:31 PM
 */

//Permission Type
$pageSessionType = "business";
include "sessionValidator.php";
include "databaseSettings.php";

?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Summer View</title>
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
                //Set year to the user's input value
                $year = $_POST['selectYear'];
                $mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

                if( !$mysqli->connect_errno )
                {
                    //Get summer courses from the last n years
                    $sql = "SELECT courseCode, CONCAT(lastName, ', ', firstName), enrollment, Sections.semester, year
                            FROM (((consistsOf join Sections using (crn,year)) join taughtBy using (crn,year)) join Instructors using (rNumber))
                            WHERE year >= (2014-$year ) AND year<=2014 AND (Sections.semester = 'Summer I' OR Sections.semester = 'Summer II')
                            ORDER BY courseCode, year";

                    //Display summer courses from the last n years
                    if( $result = $mysqli->query( $sql ) )
                    {
                        echo "<table border='1' id='htmlgrid' class='testgrid'>
                            <tr>
                            <th>Course Code</th>
                            <th>Instructor</th>
                            <th>Enrollment</th>
                            <th>Semester</th>
                            <th>Year</th>
                            </tr>";
                        //Display result in a table
                        while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
                        {
                            echo "<tr>";
                            echo "<td>" . $row[ "courseCode" ] . "</td>";
                            echo "<td>" . $row[ "CONCAT(lastName, ', ', firstName)" ] . "</td>";
                            echo "<td>" . $row[ "enrollment" ] . "</td>";
                            echo "<td>" . $row[ "semester" ] . "</td>";
                            echo "<td>" . $row[ "year" ] . "</td>";
                            echo "</tr>";
                        }

                        echo "</table>";
                        $result->close();

                    }
                    //Display an error if there is a database error
                    else
                    {
                        echo $mysqli->error;
                        echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
                    }

                    $mysqli->close();
                }
                //If there is no connection to the DB, display an error
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

<script>
window.onload = function()
{
	if( document.getElementById( "htmlgrid" ) )
	{
		editableGrid = new EditableGrid( "htmlgrid" , { editMode: "absolute" } );

		// Build and load the metadata in JS
		editableGrid.load(
			{ metadata: [
				{ name: "courseCode", datatype: "string", editable: false },
				{ name: "instructor", datatype: "string", editable: false },
				{ name: "enrollment", datatype: "string", editable: false },
				{ name: "semester", datatype: "string", editable: false },
				{ name: "year", datatype: "string", editable: false }
		] } );

		// Attach to the HTML table and render
		editableGrid.attachToHTMLTable( "htmlgrid" );
		editableGrid.renderGrid();
	}
}
</script>
