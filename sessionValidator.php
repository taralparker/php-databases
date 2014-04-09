<?php
	session_start();

	if( !isset( $_SESSION[ "rNumber" ] ) )
	{
		session_destroy();
		header( "Location: index.php" );
		exit();
	}
	else
	{
		if( $_SESSION[ "type" ] != $checkSessionType )
		{
			unset( $checkSessionType );
			//header( "HTTP/1.0 401 Unauthorized" );
			//header( "Location: index.php" );
			exit();
		}
		/*
		if( isset( $_SESSION[ "expiration" ] ) && ( $_SESSION[ "expiration" ] < time() ) )
		{

		}
		*/
		
		unset( $checkSessionType );
	}
?>
