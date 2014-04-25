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

            <!-- Display form for user to choose a new load preference -->
            <form align="center" name="selectYearForm" action="businessSummerQuery.php" method="post">
                <select name="selectYear">

                    <!-- User can select Fall or Spring as load preference -->
                    <?php
                    for( $index = 1 ; $index < 21 ; $index++ )
                        echo "<option value='$index' ". ($index  == $_SESSION[ "selectYear" ] ? "selected" : "" ) .">$index</option>";
                    ?>

                </select>

                <!-- On submit, businessSummerQuery.php is called to query the database and display the results -->
                <input type="submit" value="Set">
            </form>



</div>
</body>
</html>
