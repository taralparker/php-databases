<?php
	include "databaseSettings.php";

	$mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

	// Construct return array
	$info = array();
	$info[ "success" ] = false;
	$info[ "errorString" ] = "";
	$info[ "type" ] = NULL;
	$info[ "value" ] = NULL;
	$info[ "affectedRows" ] = 0;

	if( !$mysqli->connect_errno )
	{
		$result = $mysqli->query( $_POST[ "query" ] );

		//$result = $mysqli->query( "UPDATE Instructors SET loadPreference = 'Spring' WHERE rNumber = 20441254;" );
		//$result = $mysqli->query( "SELECT * FROM Textbooks WHERE ISBN = 9781423911795 or ISBN = 9783596513987;" );

		$info[ "success" ] = !strlen( $mysqli->error );
		$info[ "errorString" ] = $mysqli->error;
		$info[ "type" ] = gettype( $result );
		$info[ "affectedRows" ] = $mysqli->affected_rows;

		// An object type is a mysqli_result. Only from select queries
		if( $info[ "type" ] == "object" )
		{
			$info[ "value" ] = array();

			for( $index = 0 ; $row = $result->fetch_array( MYSQLI_ASSOC ) ; $index++ )
				$info[ "value" ][ $index ] = $row;
		}
		else
			$info[ "value" ] = $result;

		// Debugging
		/*
		echo "<br>Success: ";
		echo $info[ "success" ] ? "true" : "false";
		echo "<br>Error: ";
		echo $info[ "errorString" ];
		echo "<br>Type: ";
		echo $info[ "type" ];
		echo "<br>Value: ";
		echo var_dump( $info[ "value" ] );
		echo "<br>";
		*/

		//echo $result ? "ok" : $mysqli->error;
	}
	else
		$info[ "errorString" ] = $mysqli->connect_error;

	$mysqli->close();

	echo json_encode( $info );
?>
