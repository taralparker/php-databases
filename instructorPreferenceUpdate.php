<?php


$pageSessionType = "instructor";
include "sessionValidator.php";
include 'databaseSettings.php';

    $mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );
    //Set month to the user's input value
    $month = $_POST['coursePreferenceSemester'];
    //Create SQL query with user's input month and rNumber
    $sql = "UPDATE Prefers SET rating = '$rating' WHERE rNumber = '$_SESSION[rNumber]' and '$row[courseCode]' and '$row[courseTitle]' ";

    //If the update is successful, update page to instructorLoadEdit.php
    if( $result = $mysqli->query( $sql ) ){
        header('Location: instructorPreferenceEdit.php');
    }
    //If there is a query error, display a message
    else
    {
        echo $mysqli->error;
        echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
    }
