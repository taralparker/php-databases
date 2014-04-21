<?php
/**
 * Created by PhpStorm.
 * User: Tara
 * Date: 4/20/14
 * Time: 6:31 PM
 */

	$pageSessionType = "instructor";
	include "sessionValidator.php";
	// Include this for global database access variables
	include "databaseSettings.php";
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Update Load Preference</title>
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
        //Get current load Preference
        $sql = "SELECT loadPreference, CONCAT(firstName,' ',lastName) from Instructors WHERE rNumber = '$_SESSION[rNumber]'";
        //Display current load preference
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='htmlgrid' class='testgrid'>
			<tr>
			<th>Name</th>
			<th>Current Load Preference</th>
			</tr>";
            //Display result in a table
            while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
            {
                echo "<tr>";
                echo "<td>" . $row[ "CONCAT(firstName,' ',lastName)" ] . "</td>";
                echo "<td>" . $row[ "loadPreference" ] . "</td>";
                echo "</tr>";
            }

            echo "</table>";
            $result->close();

            ?>
            <br>

            <!-- Display form for user to choose a new load preference -->
            <form align="center" name="loadPreferenceForm" action="instructorLoadUpdate.php" method="post">
            <select name="loadPreferenceSemester">

            <!-- User can select Fall or Spring as load preference -->
            <?php
                $semesters = array( "Fall" , "Spring");
                for( $index = 0 ; $index < 2 ; $index++ )
                    echo "<option value='$semesters[$index]' ". ( $semesters[ "$index" ] == $_SESSION[ "loadPreferenceSemester" ] ? "selected" : "" ) .">$semesters[$index]</option>";
            ?>

            </select>

            <!-- On submit, instructorLoadUpdate.php is called to update the database and update this page -->
            <input type="submit" value="Set">
            </form>

        <?php

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
