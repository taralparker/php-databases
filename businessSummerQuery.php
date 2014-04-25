<?php
/**
 * Created by PhpStorm.
 * User: Tara
 * Date: 4/20/14
 * Time: 6:31 PM
 */

$pageSessionType = "business";
include "sessionValidator.php";
// Include this for global database access variables
include "databaseSettings.php";
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Summer Courses</title>
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
    //Set year to the user's input value
    $year = $_POST['chooseYear'];
    $mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

    if( !$mysqli->connect_errno )
    {
        //Get summer courses from the last n years
        $sql = "SELECT courseCode, CONCAT(lastName, ', ', firstName), enrollment, year FROM (((consistsOf join sections using (crn,year)) join taughtBy using (crn,year)) join Instructors using (rNumber)) WHERE year > (2014-5) AND year<=2014 AND (Sections.semester = 'Summer I' OR Sections.semester = 'Summer II')";

        //Display summer courses from the last n years
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='htmlgrid' class='testgrid'>
			<tr>
			<th>Course Code</th>
			<th>Instructor</th>
			<th>Enrollment</th>
			<th>Year</th>
			</tr>";
            //Display result in a table
            while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
            {
                echo "<tr>";
                echo "<td>" . $row[ "courseCode" ] . "</td>";
                echo "<td>" . $row[ "CONCAT(lastName, ', ', firstName)" ] . "</td>";
                echo "<td>" . $row[ "enrollment" ] . "</td>";
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
</body>
</html>
