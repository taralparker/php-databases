<?php
/**
 * Created by PhpStorm.
 * User: Tara
 * Date: 4/26/14
 * Time: 4:57 PM
 */

// Permission Type
$pageSessionType = "business";
include "sessionValidator.php";
// Include this for global database access variables
include "databaseSettings.php";
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Professor History</title>
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
                    //Query to get Instructors
                    $sql = "SELECT CONCAT(lastName, ', ', firstName)
                            FROM Instructors
                            ORDER BY lastName";

                    //Create an array of Instructors
                    if( $result = $mysqli->query( $sql ) )
                    {
                        $count = mysqli_num_rows($result);
                        $instructors = array();
                        $i=0;

                        while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
                        {
                            $instructors[$i]= $row["CONCAT(lastName, ', ', firstName)"];
                            $i = $i+1;
                        }

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

                <!-- Display form for user to choose an instructor and number of years -->
                <br>
                <h1><center>Please select an instructor and the number of years you would like to view:</center></h1>
                <form align="center" name="selectInstructorAndYearForm" action="businessHistoryQuery.php" method="post">
                    <select name="selectInstructor">

                        <!-- User can select an instructor -->
                        <?php
                        $options = $instructors;
                        for( $index = 0 ; $index < $count ; $index++ )
                            echo "<option value='$instructors[$index]' ". ( $instructors[ "$index" ] == $_SESSION[ "selectInstructor" ] ? "selected" : "" ) .">$instructors[$index]</option>";
                        ?>

                    </select>
                    <select name="selectYear">

                        <!-- User can select between 1 and 20 years -->
                        <?php
                        for( $index = 1 ; $index < 21 ; $index++ )
                            echo "<option value='$index' ". ($index  == $_SESSION[ "selectYear" ] ? "selected" : "" ) .">$index</option>";
                        ?>

                    </select>

                    <!-- On submit, businessHistoryQuery.php is called to query the database and display the results -->
                    <input type="submit" value="Set">
                </form>

            </div>
        </td>
        <td id="bodyTableRight">
        </td>
    </tr>
</table>
</body>
</html>
