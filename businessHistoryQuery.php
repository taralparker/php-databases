<?php
/**
 * Created by PhpStorm.
 * User: Tara
 * Date: 4/26/14
 * Time: 4:58 PM
 */

//Permission Type
$pageSessionType = "business";
include "sessionValidator.php";
include "databaseSettings.php";
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Template</title>
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
    $instructor = $_POST['selectInstructor'];
    $mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );
    ?>
    <br>
    <br>
    <font size="75pt">
        <?php echo $instructor; ?>
    </font>
    <br>
    <br>
    <?php
    if( !$mysqli->connect_errno )
    {
        //Query all courses taught by selected instructor in the last n years
        $sql = "SELECT courseCode, courseTitle, semester, year, enrollment, bldg
                FROM ((((Sections JOIN taughtBy using (CRN, semester, year)) JOIN Instructors using (rNumber)) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear))
                WHERE year >= (2014 - $year) AND CONCAT(lastName, ', ', firstName) = '$instructor'
                ORDER BY year DESC, CASE semester
                  WHEN 'FALL' THEN 1
                  WHEN 'Summer II' THEN 2
                  WHEN 'Summer I' THEN 3
                  WHEN 'SPRING' THEN 4 END, semester";

        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='htmlgrid' class='testgrid'>
                            <tr>
                            <th>Course Code</th>
                            <th>Title</th>
                            <th>Semester</th>
                            <th>Year</th>
                            <th>Enrollment</th>
                            <th>Building</th>
                            </tr>";

            //Display result in a table
            ?>
            <h1><center>All Courses Taught</center></h1>
            <?php
            while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
            {
                echo "<tr>";
                echo "<td>" . $row[ "courseCode" ] . "</td>";
                echo "<td>" . $row[ "courseTitle" ] . "</td>";
                echo "<td>" . $row[ "semester" ] . "</td>";
                echo "<td>" . $row[ "year" ] . "</td>";
                echo "<td>" . $row[ "enrollment" ] . "</td>";
                echo "<td>" . $row[ "bldg" ] . "</td>";
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

        ?><br><?php
        //Number of distinct courses taught in the last n years
        $sql = "SELECT count(distinct(courseCode))
                FROM ((((Sections JOIN taughtBy using (CRN, semester, year)) JOIN Instructors using (rNumber)) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear))
                WHERE year >= (2014 - $year) AND CONCAT(lastName, ', ', firstName) = '$instructor'";

        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='htmlgrid' class='testgrid'>
                            <tr>
                            <th>Number of Distinct Courses taught</th>
                            </tr>";

            //Display result in a table
            ?>
            <h1><center>All Courses Taught</center></h1>
            <?php
            while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
            {
                echo "<tr>";
                echo "<td>" . $row[ "count(distinct(courseCode))" ] . "</td>";
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

        ?><br><?php
        //ALL DISTINCT UNDERGRADUATE / REQUIRED COURSES
        $sql = "SELECT courseCode, count(courseCode), avg(enrollment), avg(hoursPerWeek), (avg(hoursPerWeek))/(avg(enrollment))
                FROM (((((Sections JOIN taughtBy using (CRN, semester, year)) JOIN Instructors using (rNumber)) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear)) LEFT OUTER JOIN hasTA using (CRN, semester, year))
                WHERE year >= (2014 - $year) AND CONCAT(lastName, ', ', firstName) = '$instructor' AND required = 1 AND ( courseCode LIKE '1%' OR courseCode LIKE '2%' OR courseCode LIKE '3%' OR courseCode LIKE '4%')
                GROUP BY courseCode
                ORDER BY courseCode";

        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='htmlgrid' class='testgrid'>
                            <tr>
                            <th>Course Code</th>
                            <th>Times Repeated</th>
                            <th>Average Enrollment</th>
                            <th>Average TA Hours Per Week</th>
                            <th>Ratio (TA:Enrollment)</th>
                            </tr>";

            //Display result in a table
            ?>
            <h1><center>All Required Undergraduate Courses Taught</center></h1>
            <?php
            while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
            {
                echo "<tr>";
                echo "<td>" . $row[ "courseCode" ] . "</td>";
                echo "<td>" . $row[ "count(courseCode)" ] . "</td>";
                echo "<td>" . $row[ "avg(enrollment)" ] . "</td>";
                echo "<td>" . $row[ "avg(hoursPerWeek)" ] . "</td>";
                echo "<td>" . $row[ "(avg(hoursPerWeek))/(avg(enrollment))" ] . "</td>";
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

        ?><br><?php
        //ALL DISTINCT UNDERGRADUATE / NON REQUIRED COURSES
        //Query all courses taught by selected instructor in the last n years
        $sql = "SELECT courseCode, count(courseCode), avg(enrollment), avg(hoursPerWeek), (avg(hoursPerWeek))/(avg(enrollment))
                FROM (((((Sections JOIN taughtBy using (CRN, semester, year)) JOIN Instructors using (rNumber)) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear)) LEFT OUTER JOIN hasTA using (CRN, semester, year))
                WHERE year >= (2014 - $year) AND CONCAT(lastName, ', ', firstName) = '$instructor' AND required = 0 AND ( courseCode LIKE '1%' OR courseCode LIKE '2%' OR courseCode LIKE '3%' OR courseCode LIKE '4%')
                GROUP BY courseCode
                ORDER BY courseCode";

        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='htmlgrid' class='testgrid'>
                            <tr>
                            <th>Course Code</th>
                            <th>Times Repeated</th>
                            <th>Average Enrollment</th>
                            <th>Average TA Hours Per Week</th>
                            <th>Ratio (TA:Enrollment)</th>
                            </tr>";

            //Display result in a table
            ?>
            <h1><center>All Non-required Undergraduate Courses Taught</center></h1>
            <?php
            while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
            {
                echo "<tr>";
                echo "<td>" . $row[ "courseCode" ] . "</td>";
                echo "<td>" . $row[ "count(courseCode)" ] . "</td>";
                echo "<td>" . $row[ "avg(enrollment)" ] . "</td>";
                echo "<td>" . $row[ "avg(hoursPerWeek)" ] . "</td>";
                echo "<td>" . $row[ "(avg(hoursPerWeek))/(avg(enrollment))" ] . "</td>";
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

        ?><br><?php
        //ALL DISTINCT GRADUATE / REQUIRED COURSES
        //Query all courses taught by selected instructor in the last n years
        $sql = "SELECT courseCode, count(courseCode), avg(enrollment), avg(hoursPerWeek), (avg(hoursPerWeek))/(avg(enrollment))
                FROM (((((Sections JOIN taughtBy using (CRN, semester, year)) JOIN Instructors using (rNumber)) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear)) LEFT OUTER JOIN hasTA using (CRN, semester, year))
                WHERE year >= (2014 - $year) AND CONCAT(lastName, ', ', firstName) = '$instructor' AND required = 1 AND ( courseCode LIKE '5%' OR courseCode LIKE '6%' OR courseCode LIKE '7%' OR courseCode LIKE '8%')
                GROUP BY courseCode
                ORDER BY courseCode";

        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='htmlgrid' class='testgrid'>
                            <tr>
                            <th>Course Code</th>
                            <th>Times Repeated</th>
                            <th>Average Enrollment</th>
                            <th>Average TA Hours Per Week</th>
                            <th>Ratio (TA:Enrollment)</th>
                            </tr>";

            //Display result in a table
            ?>
            <h1><center>All Required Graduate Courses Taught</center></h1>
            <?php
            while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
            {
                echo "<tr>";
                echo "<td>" . $row[ "courseCode" ] . "</td>";
                echo "<td>" . $row[ "count(courseCode)" ] . "</td>";
                echo "<td>" . $row[ "avg(enrollment)" ] . "</td>";
                echo "<td>" . $row[ "avg(hoursPerWeek)" ] . "</td>";
                echo "<td>" . $row[ "(avg(hoursPerWeek))/(avg(enrollment))" ] . "</td>";
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

        ?><br><?php
        //ALL DISTINCT GRADUATE / NON REQUIRED COURSES
        //Query all courses taught by selected instructor in the last n years
        $sql = "SELECT courseCode, count(courseCode), avg(enrollment), avg(hoursPerWeek), (avg(hoursPerWeek))/(avg(enrollment))
                FROM (((((Sections JOIN taughtBy using (CRN, semester, year)) JOIN Instructors using (rNumber)) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear)) LEFT OUTER JOIN hasTA using (CRN, semester, year))
                WHERE year >= (2014 - $year) AND CONCAT(lastName, ', ', firstName) = '$instructor' AND required = 0 AND ( courseCode LIKE '5%' OR courseCode LIKE '6%' OR courseCode LIKE '7%' OR courseCode LIKE '8%')
                GROUP BY courseCode
                ORDER BY courseCode";

        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='htmlgrid' class='testgrid'>
                            <tr>
                            <th>Course Code</th>
                            <th>Times Repeated</th>
                            <th>Average Enrollment</th>
                            <th>Average TA Hours Per Week</th>
                            <th>Ratio (TA:Enrollment)</th>
                            </tr>";

            //Display result in a table
            ?>
            <h1><center>All Non-required Graduate Courses Taught</center></h1>
            <?php
            while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
            {
                echo "<tr>";
                echo "<td>" . $row[ "courseCode" ] . "</td>";
                echo "<td>" . $row[ "count(courseCode)" ] . "</td>";
                echo "<td>" . $row[ "avg(enrollment)" ] . "</td>";
                echo "<td>" . $row[ "avg(hoursPerWeek)" ] . "</td>";
                echo "<td>" . $row[ "(avg(hoursPerWeek))/(avg(enrollment))" ] . "</td>";
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
        ?><br><br><?php
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
