<?php
	session_start();
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Database Project</title>
  <link rel="stylesheet" href="css/style.css" type="text/css" media="screen">
 </head>
 <body>
  <div id="container">
   <div id="masthead">
    <div id="logo"></div>
    <div id="title"></div>
   </div>

<?php
	for( $index = 0 ; $index < 5 ; $index++ )
		echo "<br>";
?>

  <div style="background-color: #FFFFFF;" align="center">
   <form action="index.php" method="post">
    <table>
     <!--
     <tr>
      <div align="center">
       DBPro
      </div>
     </tr>
     -->
     <tr>
      <table border="1">
       <tr>
        <td>
         RNumber
        </td>
        <td>
         <input name="rNumber" type="text">
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
 </div>

<?php
	if( isset( $_POST[ "submit" ] , $_POST[ "rNumber" ] , $_POST[ "password" ] ) )
	{
		$rNum = intval( $_POST[ "rNumber" ] );

		if( $rNum > 0 )
		{
			$mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

			if( !$mysqli->connect_errno )
			{
				$pass = hash( "sha256" , $_POST[ "password" ] );

				$sql = "SELECT * FROM accounts WHERE rNumber=$rNum AND password='$pass' LIMIT 1";

				if( $result = $mysqli->query( $sql ) )
				{
					if( $row = $result->fetch_array( MYSQLI_ASSOC ) )
					{
						$_SESSION[ "rNumber" ] = $rNum;
						$_SESSION[ "expiration" ] = time() + 60;
						$_SESSION[ "type" ] = $row[ "permissionType" ];

						if( $_SESSION[ "type" ] == "Faculty" )
							header( "Location: facultyHome.php" );
						else if( $_SESSION[ "type" ] == "Instructor" )
							header( "Location: instructorHome.php" );
						else if( $_SESSION[ "type" ] == "Business" )
							header( "Location: businessHome.php" );
						else
							echo "<div align='center'>User has no account type. Please contact a system administrator.</div>";
					}
					else
						echo "<div align='center'>Invalid RNumber or password.</div>";

					$result->close();
				}
				else
					echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";

				$mysqli->close();
			}
			else
				echo "<div align='center'>Unable to connect to database.</div>";
		}
		else
			echo "<div align='center'>Invalid RNumber.</div>";
	}
?>

 </body>
</html>
