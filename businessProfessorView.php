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

                <h1><center>How many years would you like to view?</center></h1>
                <!-- Display form for user to choose year -->
                <form align="center" name="selectYearForm" action="businessProfessorQuery.php" method="post">
                    <select name="selectYear">

                        <!-- User can select between 1 and 20 years -->
                        <?php
                        for( $index = 1 ; $index < 21 ; $index++ )
                            echo "<option value='$index' ". ($index  == $_SESSION[ "selectYear" ] ? "selected" : "" ) .">$index</option>";
                        ?>

                    </select>

                    <!-- On submit, businessProfessorQuery.php is called to query the database and display the results -->
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

