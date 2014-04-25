<?php
/**
 * Created by PhpStorm.
 * User: Tara
 * Date: 4/24/14
 * Time: 9:31 PM
 */

$pageSessionType = "business";
include "sessionValidator.php";
// Include this for global database access variables
include "databaseSettings.php";
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Professor Information</title>
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
    $year = $_POST['selectYear'];
    $mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

    if( !$mysqli->connect_errno )
    {
        //TA RATIO FOR UNDERGRADUATE COURSES
        $sql = "SELECT CONCAT(lastName, ', ', firstName), (sum(hoursPerWeek)/sum(enrollment)) FROM (((Sections JOIN taughtBy using (CRN, semester, year) JOIN Instructors using (rNumber)) JOIN hasTA using (CRN, semester, year) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear)) WHERE year >= (2014 - $year) AND ( courseCode LIKE '1%' OR courseCode LIKE '2%' OR courseCode LIKE '3%' OR courseCode LIKE '4%') GROUP BY Instructors.lastName";
        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='htmlgrid' class='testgrid'>
			<tr>
			<th>Instructor</th>
			<th>TA Ratio</th>
			</tr>";

            //Display result in a table
            ?>
            <h1><center>TA Ratio for undergraduate courses</center></h1>
            <?php
            while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
            {
                echo "<tr>";
                echo "<td>" . $row[ "CONCAT(lastName, ', ', firstName)" ] . "</td>";
                echo "<td>" . $row[ "(sum(hoursPerWeek)/sum(enrollment))" ] . "</td>";
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

        //TA RATIO FOR GRADUATE COURSES
        $sql = "SELECT CONCAT(lastName, ', ', firstName), (sum(hoursPerWeek)/sum(enrollment)) FROM (((Sections JOIN taughtBy using (CRN, semester, year) JOIN Instructors using (rNumber)) JOIN hasTA using (CRN, semester, year) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear)) WHERE year >= (2014 - $year) AND ( courseCode LIKE '5%' OR courseCode LIKE '6%' OR courseCode LIKE '7%' OR courseCode LIKE '8%') GROUP BY Instructors.lastName";
        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='htmlgrid' class='testgrid'>
            <tr>
            <th>Instructor</th>
            <th>TA Ratio</th>
            </tr>";

            //Display result in a table
            ?>
            <h1><center>TA Ratio for undergraduate courses</center></h1>

            <?php
            while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
            {
                echo "<tr>";
                echo "<td>" . $row[ "CONCAT(lastName, ', ', firstName)" ] . "</td>";
                echo "<td>" . $row[ "(sum(hoursPerWeek)/sum(enrollment))" ] . "</td>";
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

        //DISTINCT COURSES
        $sql = "SELECT CONCAT(lastName, ', ', firstName), count(distinct courseCode) FROM ((((Sections JOIN taughtBy using (CRN, semester, year)) JOIN Instructors using (rNumber)) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear)) WHERE year >= (2014 - $year) GROUP BY lastName";
        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='htmlgrid' class='testgrid'>
            <tr>
            <th>Instructor</th>
            <th>Courses</th>
            </tr>";

            //Display result in a table
            ?>
            <h1><center>Distinct Courses Taught</center></h1>

            <?php
            while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
            {
                echo "<tr>";
                echo "<td>" . $row[ "CONCAT(lastName, ', ', firstName)" ] . "</td>";
                echo "<td>" . $row[ "count(distinct courseCode)" ] . "</td>";
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

        //TOTAL UNDERGRAD COURSES TAUGHT
        $sql = "SELECT CONCAT(lastName, ', ', firstName), count(courseCode) FROM ((((Sections JOIN taughtBy using (CRN, semester, year)) JOIN Instructors using (rNumber)) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear))  WHERE year >= (2014 - $year) AND ( courseCode LIKE '4%' OR courseCode LIKE '3%' OR courseCode LIKE '2%' OR courseCode LIKE '1%') GROUP BY Instructors.lastName";

        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='htmlgrid' class='testgrid'>
            <tr>
            <th>Instructor</th>
            <th>Courses</th>
            </tr>";

            //Display result in a table
            ?>
            <h1><center>Total Undergraduate Courses Taught</center></h1>

            <?php
            while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
            {
                echo "<tr>";
                echo "<td>" . $row[ "CONCAT(lastName, ', ', firstName)" ] . "</td>";
                echo "<td>" . $row[ "count(courseCode)" ] . "</td>";
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


        //TOTAL GRAD COURSES TAUGHT
        $sql = "SELECT CONCAT(lastName, ', ', firstName), count(courseCode) FROM ((((Sections JOIN taughtBy using (CRN, semester, year)) JOIN Instructors using (rNumber)) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear))  WHERE year >= (2014 - $year) AND ( courseCode LIKE '5%' OR courseCode LIKE '6%' OR courseCode LIKE '7%' OR courseCode LIKE '8%') GROUP BY Instructors.lastName";

        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='htmlgrid' class='testgrid'>
        <tr>
        <th>Instructor</th>
        <th>Courses</th>
        </tr>";

            //Display result in a table
            ?>
            <h1><center>Total Graduate Courses Taught</center></h1>

            <?php
            while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
            {
                echo "<tr>";
                echo "<td>" . $row[ "CONCAT(lastName, ', ', firstName)" ] . "</td>";
                echo "<td>" . $row[ "count(courseCode)" ] . "</td>";
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
<br>
<br>
</div>
</body>
</html>
