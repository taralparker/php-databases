<?php
/**
 * Created by PhpStorm.
 * User: Tara
 * Date: 4/24/14
 * Time: 9:31 PM
 */

//Permission Type
$pageSessionType = "business";
include "sessionValidator.php";
include "databaseSettings.php";
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Professor View</title>
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
        //TA RATIO FOR UNDERGRADUATE COURSES
        $sql = "SELECT CONCAT(lastName, ', ', firstName), (sum(hoursPerWeek)/sum(enrollment))
                FROM (((Sections JOIN taughtBy using (CRN, semester, year) JOIN Instructors using (rNumber)) JOIN hasTA using (CRN, semester, year) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear))
                WHERE year >= (2014 - $year) AND ( courseCode LIKE '1%' OR courseCode LIKE '2%' OR courseCode LIKE '3%' OR courseCode LIKE '4%')
                GROUP BY Instructors.lastName";

        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='undergradRatio' class='testgrid'>
                            <tr>
                            <th>Instructor</th>
                            <th>TA Ratio</th>
                            </tr>";

            //Display result in a table
            ?>
            <h1><center>TA Ratio for Undergraduate Courses</center></h1>
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

        ?><br><?php
        //TA RATIO FOR GRADUATE COURSES
        $sql = "SELECT CONCAT(lastName, ', ', firstName), (sum(hoursPerWeek)/sum(enrollment))
                FROM (((Sections JOIN taughtBy using (CRN, semester, year) JOIN Instructors using (rNumber)) JOIN hasTA using (CRN, semester, year) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear))
                WHERE year >= (2014 - $year) AND ( courseCode LIKE '5%' OR courseCode LIKE '6%' OR courseCode LIKE '7%' OR courseCode LIKE '8%')
                GROUP BY Instructors.lastName";

        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='gradRatio' class='testgrid'>
                            <tr>
                            <th>Instructor</th>
                            <th>TA Ratio</th>
                            </tr>";

            //Display result in a table
            ?>
            <h1><center>TA Ratio for Graduate Courses</center></h1>

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

        ?><br><?php
        //DISTINCT COURSES
        $sql = "SELECT CONCAT(lastName, ', ', firstName), count(distinct courseCode)
                FROM ((((Sections JOIN taughtBy using (CRN, semester, year)) JOIN Instructors using (rNumber)) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear))
                WHERE year >= (2014 - $year)
                GROUP BY lastName";

        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='distinctCourses' class='testgrid'>
                            <tr>
                            <th>Instructor</th>
                            <th>Distinct Courses</th>
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

        ?><br><?php
        //NEW COURSES
        $sql = "SELECT CONCAT(lastName, ', ', firstName), count(distinct courseCode)
                FROM ((((Sections JOIN taughtBy using (CRN, semester, year)) JOIN Instructors AS T1 using (rNumber)) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear))
                WHERE year >= (2014 - $year) AND courseCode NOT IN (
                  SELECT courseCode
                  FROM ((((Sections JOIN taughtBy using (CRN, semester, year)) JOIN Instructors AS T2 using (rNumber)) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear))
                  WHERE year < (2014 - $year) AND T1.rNumber = T2.rNumber)
                GROUP BY rNumber
                ORDER BY T1.lastName";

        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='newCourses' class='testgrid'>
                            <tr>
                            <th>Instructor</th>
                            <th>New Courses</th>
                            </tr>";

            //Display result in a table
            ?>
            <h1><center>New Courses Taught</center></h1>

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

        ?><br><?php
        //TOTAL UNDERGRAD COURSES TAUGHT
        $sql = "SELECT CONCAT(lastName, ', ', firstName), count(courseCode)
                FROM ((((Sections JOIN taughtBy using (CRN, semester, year)) JOIN Instructors using (rNumber)) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear))
                WHERE year >= (2014 - $year) AND ( courseCode LIKE '4%' OR courseCode LIKE '3%' OR courseCode LIKE '2%' OR courseCode LIKE '1%')
                GROUP BY Instructors.lastName";

        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='totalUndergrad' class='testgrid'>
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

        ?><br><?php
        //TOTAL GRAD COURSES TAUGHT
        $sql = "SELECT CONCAT(lastName, ', ', firstName), count(courseCode)
                FROM ((((Sections JOIN taughtBy using (CRN, semester, year)) JOIN Instructors using (rNumber)) JOIN consistsOf using (CRN, semester, year)) JOIN Courses using (courseCode, catalogYear))
                WHERE year >= (2014 - $year) AND ( courseCode LIKE '5%' OR courseCode LIKE '6%' OR courseCode LIKE '7%' OR courseCode LIKE '8%')
                GROUP BY Instructors.lastName";

        //Display query result
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='totalGrad' class='testgrid'>
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
	if( document.getElementById( "undergradRatio" ) )
	{
		editableGrid = new EditableGrid( "undergradRatio" , { editMode: "absolute" } );

		// Build and load the metadata in JS
		editableGrid.load(
			{ metadata: [
				{ name: "instructor", datatype: "string", editable: false },
				{ name: "taRatio", datatype: "string", editable: false }
		] } );

		// Attach to the HTML table and render
		editableGrid.attachToHTMLTable( "undergradRatio" );
		editableGrid.renderGrid();
	}

	if( document.getElementById( "gradRatio" ) )
	{
		editableGrid = new EditableGrid( "gradRatio" , { editMode: "absolute" } );

		// Build and load the metadata in JS
		editableGrid.load(
			{ metadata: [
				{ name: "instructor", datatype: "string", editable: false },
				{ name: "taRatio", datatype: "string", editable: false }
		] } );

		// Attach to the HTML table and render
		editableGrid.attachToHTMLTable( "gradRatio" );
		editableGrid.renderGrid();
	}

	if( document.getElementById( "distinctCourses" ) )
	{
		editableGrid = new EditableGrid( "distinctCourses" , { editMode: "absolute" } );

		// Build and load the metadata in JS
		editableGrid.load(
			{ metadata: [
				{ name: "instructor", datatype: "string", editable: false },
				{ name: "distinctCourses", datatype: "string", editable: false }
		] } );

		// Attach to the HTML table and render
		editableGrid.attachToHTMLTable( "distinctCourses" );
		editableGrid.renderGrid();
	}

	if( document.getElementById( "newCourses" ) )
	{
		editableGrid = new EditableGrid( "newCourses" , { editMode: "absolute" } );

		// Build and load the metadata in JS
		editableGrid.load(
			{ metadata: [
				{ name: "instructor", datatype: "string", editable: false },
				{ name: "newCourses", datatype: "string", editable: false }
		] } );

		// Attach to the HTML table and render
		editableGrid.attachToHTMLTable( "newCourses" );
		editableGrid.renderGrid();
	}

	if( document.getElementById( "totalUndergrad" ) )
	{
		editableGrid = new EditableGrid( "totalUndergrad" , { editMode: "absolute" } );

		// Build and load the metadata in JS
		editableGrid.load(
			{ metadata: [
				{ name: "instructor", datatype: "string", editable: false },
				{ name: "courses", datatype: "string", editable: false }
		] } );

		// Attach to the HTML table and render
		editableGrid.attachToHTMLTable( "totalUndergrad" );
		editableGrid.renderGrid();
	}

	if( document.getElementById( "totalGrad" ) )
	{
		editableGrid = new EditableGrid( "totalGrad" , { editMode: "absolute" } );

		// Build and load the metadata in JS
		editableGrid.load(
			{ metadata: [
				{ name: "instructor", datatype: "string", editable: false },
				{ name: "courses", datatype: "string", editable: false }
		] } );

		// Attach to the HTML table and render
		editableGrid.attachToHTMLTable( "totalGrad" );
		editableGrid.renderGrid();
	}
}
</script>
