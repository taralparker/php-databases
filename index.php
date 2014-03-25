<?php
	session_start();
?>

<html>
 <head>
  <!-- <span class="Centerer" style="display: inline-block; height: 10%; vertical-align: middle;"></span> -->
  <div align="center">
  <!-- <div style="position:absolute; top:50%; left:50%; margin:0 auto;"> -->
  <!--  <div style="padding: 0 15px; width: 100%; display: inline-block; vertical-align: middle;"> -->
   <form action="index.php" method="post">
    <table>
     <tr>
      <div align="center">
       DBPro
      </div>
     </tr>
     <tr>
      <table border="1">
       <tr>
        <td>
         Username
        </td>
        <td>
         <input name="username" type="text">
        </td>
       </tr>
       <tr>
        <td>
         Password
        </td>
        <td>
         <input name="password" type="password">
        </td>
       </tr>
      </table>
     </tr>
     <tr>
      <br>
      <td colspan="2">
       <div align="center">
        <input name="submit" type="submit" value="Login">
       </div>
      <td>
     </tr>
    </table>
   </form>
  </div>

 <?php
	if( isset( $_POST[ "submit" ] , $_POST[ "username" ] , $_POST[ "password" ] ) )
	{
		$mysqli = new mysqli( "localhost" , "dbpro" , "datumbase" , "dbpro" );

		$usr = $_POST[ "username" ];
		$pas = hash( "sha256" , $_POST[ "password" ] );
		$sql = "SELECT * FROM Accounts WHERE username='$usr' AND password='$pas' LIMIT 1";
		$result = $mysqli->query( $sql );
		$row = $result->fetch_array( MYSQLI_ASSOC );

		if( $row[ "username" ] == $_POST[ "username" ] && $row[ "password" ] == $pas )
		{
			echo "
			<!-- <script type='text/javascript'>
			document.forms['submit'].clear();
			</script>
			<div align='center'>
				Authenticated.
			</div> -->
			";

			$_SESSION[ "username" ] = $usr;
			$_SESSION[ "expiration" ] = time()+60;
			$_SESSION[ "type" ] = $row[ "accountType" ];

			//$uniqueID = openssl_random_pseudo_bytes( 64 );
			//printf( "%i\n" , strlen( $uniqueID ) );
			//printf( "%s\n" , bin2hex( $uniqueID ) );

			//printf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

			//printf( "%s" , $_SESSION[ "username" ] );
			printf( "%s\n" , $_SESSION[ "type" ] );
			if( $_SESSION[ "type" ] == "faculty" )
				header( "Location: facultyHome.php" );
		}
		else
		{
			echo "
			<!-- <script type='text/javascript'>
			document.forms['submit'].clear();
			</script> -->
			<div align='center'>
				Invalid username or password.
			</div>
			";
		}
	}
?>

 </head>
</html>


