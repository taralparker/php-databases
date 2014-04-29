<?php
/**
 * Created by PhpStorm.
 * User: Tara
 * Date: 4/20/14
 * Time: 6:48 PM
 */

$pageSessionType = "instructor";
include "sessionValidator.php";
include 'databaseSettings.php';

    $mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );
    //Set month to the user's input value
    $month = $_POST['loadPreferenceSemester'];
    //Create SQL query with user's input month and rNumber
    $sql = "UPDATE Instructors SET loadPreference = '$month' WHERE rNumber = '$_SESSION[rNumber]' ";

    //If the update is successful, update page to instructorLoadEdit.php
    if( $result = $mysqli->query( $sql ) ){
        header('Location: instructorLoadEdit.php');
    }
    //If there is a query error, display a message
    else
    {
        echo $mysqli->error;
        echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
    }
