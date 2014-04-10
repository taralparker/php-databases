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
		if( strcasecmp( $_SESSION[ "type" ] , $pageSessionType ) )
		{
			unset( $pageSessionType );
			//header( "HTTP/1.0 401 Unauthorized" );
			//header( "Location: index.php" );
			exit();
		}
		/*
		if( isset( $_SESSION[ "expiration" ] ) && ( $_SESSION[ "expiration" ] < time() ) )
		{

		}
		*/
		
		// unset( $pageSessionType );
	}
?>
