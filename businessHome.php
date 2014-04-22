<?php
	$pageSessionType = "business";
	include "sessionValidator.php";
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Business Home</title>
  <link rel="stylesheet" href="css/style.css" type="text/css" media="screen">
 </head>

<?php
	if( !empty( $_POST ) && !empty( $_POST[ "demoSemester" ] ) && !empty( $_POST[ "demoYear" ] ) )
	{
		$_SESSION[ "demoSemester" ] = $_POST[ "demoSemester" ];
		$_SESSION[ "demoYear" ] = $_POST[ "demoYear" ];
	}
?>

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

      <br>
      <form align="center" name="dateForm" action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>" method="post">
       <select name="demoSemester">

<?php
	$semesters = array( "Fall" , "Spring" , "Summer I" , "Summer II" );
	for( $index = 0 ; $index < 4 ; $index++ )
		echo "<option value='$semesters[$index]' ". ( $semesters[ "$index" ] == $_SESSION[ "demoSemester" ] ? "selected" : "" ) .">$semesters[$index]</option>";
?>

       </select>
       <select name="demoYear">

<?php
	for( $year = 2009 ; $year < 2015 ; $year++ )
		echo "<option value='$year' ". ( $year == $_SESSION[ "demoYear" ] ? "selected" : "" ) .">$year</option>";
?>

       </select>
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
