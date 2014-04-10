<!-- Check if the user is allowed to access this page -->
<?php
	$pageSessionType = "faculty";
	include "sessionValidator.php";
	include "databaseSettings.php";
?>

<html>
 <!-- Page settings -->
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Instructor Edit</title>
  <link rel="stylesheet" href="css/style.css" type="text/css" media="screen">
 </head>

 <body>
  <!-- Texas Tech banner -->
  <div id="container">
   <div id="masthead">
    <div id="logo"></div>
    <div id="title"></div>
   </div>

<!-- This contains the link list at the top of the page -->
<?php echo file_get_contents( $pageSessionType."Header.php" ); ?>

   <div align="center">
   <br>

<!-- Generate the table with PHP -->
<?php
 	$mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

	if( !$mysqli->connect_errno )
	{
		// If the page was submitted with an update request, do so
		if( !empty( $_POST ) )
		{
			if( !empty( $_POST[ "rNumber" ] ) && !empty( $_POST[ "lastName" ] ) && !empty( $_POST[ "firstName" ] ) && !empty( $_POST[ "tenured" ] ) && !empty( $_POST[ "joiningSemester" ] ) && !empty( $_POST[ "joiningYear" ] ) )
			{
				$tenu = $_POST[ "tenured" ] === "Y" ? 1 : 0;
				$sql = "INSERT INTO instructors( rNumber , lastName , firstName , instructorTitle , tenured , joiningSemester ,
joiningYear , loadPreference ) VALUES ('$_POST[rNumber]','$_POST[lastName]','$_POST[firstName]','$_POST[instructorTitle]',$tenu,'$_POST[joiningSemester]',$_POST[joiningYear], NULL )";
				//printf( "%s<br>" , $sql );

				if( $mysqli->query( $sql ) )
					printf( "Insertion complete.<br>" );
				else
					printf( "Insertion failed: %s<br>" , $mysqli->error );
			}
			else
				printf( "One or more required fields are blank." );
		}

		// Start making the table
		if( $result = $mysqli->query( "SELECT * FROM instructors;" ) )
		{
			echo "<table border='1'>
			<tr>
			<th>RNumber</th>
			<th>Last Name</th>
			<th>First Name</th>
			<th>Title</th>
			<th>Tenured</th>
			<th>Joining Semester</th>
			<th>Joining Year</th>
			</tr>";
			//<th>Load Preference</th>

			// Create each row
			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				echo "<tr>";
				echo "<td>" . $row[ "rNumber" ] . "</td>";
				echo "<td>" . $row[ "lastName" ] . "</td>";
				echo "<td>" . $row[ "firstName" ] . "</td>";
				echo "<td>" . $row[ "instructorTitle" ] . "</td>";
				echo "<td>" . ( $row[ "tenured" ] ? "Y" : "N" ) . "</td>";
				echo "<td>" . $row[ "joiningSemester" ] . "</td>";
				echo "<td>" . $row[ "joiningYear" ] . "</td>";
				//echo "<td>" . $row[ "loadPreference" ] . "</td>";
				echo "</tr>";
			}

			echo "</table>";

			// Clean up
			$result->close();
		}

		$mysqli->close();
	}
	else
	{
		// Bail out here
		printf( "Connection failed: %s<br>" , $mysqli->connect_error );
		exit();
	}
?>

   </div>

   <br>
   <br>

   <!-- Make the form box -->
   <div align="center">
    <form name="instructorForm" action="facultyProfessorEdit.php" method="post">
     <table border='1'>
      <tr>
       <td>RNumber</td>
       <td>
       <input type="text" name="rNumber" onchange="checkRNumber();" style="color: #000000;" required>
       </td>
      </tr>
      <tr>
       <td>Last Name</td>
       <td>
       <input type="text" name="lastName" onchange="checkLastName();" style="color: #000000;" required>
       </td>
      </tr>
      <tr>
       <td>First Name</td>
       <td>
       <input type="text" name="firstName" onchange="checkFirstName();" style="color: #000000;" required>
       </td>
      </tr>
      <tr>
       <td>Title</td>
       <td>
       <input type="text" name="instructorTitle" style="color: #000000;">
       </td>
      </tr>
      <tr>
       <td>Tenured</td>
       <td>
        <input type="radio" name="tenured" value="N" checked="true">No
        <input type="radio" name="tenured" value="Y">Yes
       </td>
      </tr>
      <tr>
       <td>Joining Semester</td>
       <td>
       <!--
       <input type="text" name="joiningSemester" list="semester" onchange="checkJoiningSemester();" style="color: #000000;" required>
       <datalist id="semester">
       -->
        <select name="joiningSemester">
         <!-- <option value=""></option> -->
         <option value="Spring">Spring</option>
         <option value="Summer I">Summer I</option>
         <option value="Summer II">Summer II</option>
         <option value="Fall">Fall</option>
        </select>
       </td>
      </tr>
      <tr>
       <td>Joining Year</td>
       <td>
        <input type="text" name="joiningYear" value="<?php echo date( "Y" ); ?>" regex="[0-9]{4}" onchange="checkJoiningYear();" style="color: #000000;" required>
       </td>
      </tr>
      <tr>
       <td colspan="2">
        <div align="center">
         <!-- <input type="submit" value="Add"> -->
         <input type="button" value="Add" onclick="checkAll();">
        </div>
       </td>
      </tr>
     </table>
    </form>
   </div>
  </div>
 </body>
</html>

<!-- JavaScript gives more control over real-time value checking -->
<script>
function checkRNumber()
{
	// Ensure numbers greater than zero
	var element = document.getElementsByName( "rNumber" )[ 0 ];
	// !!( element.value - 0 )
	var valid = element.value.match( /^\+?0*[1-9][0-9]*$/g );
	element.style.backgroundColor = valid ? "#80FF80" : "#FF8080";
	return valid;
}

function checkLastName()
{
	// Ensure no spaces
	var element = document.getElementsByName( "lastName" )[ 0 ];
	var valid = element.value.length && !element.value.match( /\s/g );
	element.style.backgroundColor = valid ? "#80FF80" : "#FF8080";
	return valid;
}

function checkFirstName()
{
	// Ensure no spaces
	var element = document.getElementsByName( "firstName" )[ 0 ];
	var valid = element.value.length && !element.value.match( /\s/g );
	element.style.backgroundColor = valid ? "#80FF80" : "#FF8080";
	return valid;
}

function checkJoiningSemester()
{
	/*
	var element = document.getElementsByName( "joiningSemester" )[ 0 ];
	var valid = element.value.length;
	element.style.backgroundColor = valid ? "#80FF80" : "#FF8080";
	return valid;
	*/
	return true;
}

function checkJoiningYear()
{
	// Ensure only strings of four digits between 1920 and 3000
	var element = document.getElementsByName( "joiningYear" )[ 0 ];
	var valid = element.value - 0 && element.value.match( /^[0-9]{1,4}$/g );
	element.style.backgroundColor = valid ? "#80FF80" : "#FF8080";
	return valid;
}

function checkAll()
{
	// If all constrained values are valid, submit the form for addition into the database
	if( checkRNumber() && checkLastName() && checkFirstName() && checkJoiningSemester() && checkJoiningYear() )
		document.getElementsByName( "instructorForm" )[ 0 ].submit();
}

checkRNumber();
checkLastName();
checkFirstName();
checkJoiningSemester();
checkJoiningYear();
</script>
