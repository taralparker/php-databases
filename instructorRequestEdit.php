<?php

/**
 * Created by phpDesigner8
 * Author: Eric
 */
 
	// Permission
	$pageSessionType = "instructor";
	include "sessionValidator.php";
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <!-- Set page title here -->
  <title>Update Course Preference</title>
  <link rel="stylesheet" href="css/style.css" type="text/css" media="screen">
 </head>

 <body>
  <div id="container">
   <div id="masthead">
    <div id="logo"></div>
    <div id="title"></div>
   </div>

<?php echo file_get_contents( $pageSessionType."Header.php" ); ?>

   <br>

<?php
 	$mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

	if( !$mysqli->connect_errno )
	{

        $sql = "SELECT CONCAT(firstName,' ',lastName), catalogYear, courseCode, rating FROM Instructors natural join Prefers natual join Request WHERE rNumber = '$_SESSION[rNumber]'";

		if( $result = $mysqli->query( $sql ) )
		{
			echo "<table border='1' id='htmlgrid' class='testgrid'>
			<tr>
			<th>Name</th>
			<th>Catalog Year</th>
			<th>Course Code</th>
			<th>Justification</th>
			</tr>";

            //Display result in a table
           while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
           {
               echo "<tr>";
			   echo "<td>" . $row[ "CONCAT(firstName,' ',lastName)" ] . "</td>";
			   echo "<td>" . $row[ "catalogYear" ] . "</td>";
			   echo "<td>" . $row[ "courseCode" ] . "</td>";
			   echo "<td>" . $row[ "justification" ] . "</td>";
               echo "</tr>";
            }

            echo "</table>";
            $result->close();

            ?>
            <br>
 
             <!-- Display form for user to add a justification to a course -->
             <form align="center" name="coursePreferenceForm" action="instructorPreferenceUpdate.php" method="post">
             
			 <select name="courseCode">
			 
			 <?php
				$query = "SELECT courseCode , courseTitle FROM Courses" ;
				$result = $mysqli->query($query);
				while($row = $result->fetch_array( MYSQLI_ASSOC )) {
					echo '<option value="'.$row['courseCode'].'">' . "CS " . $row['courseCode'] . '</option>';   
				}

			?>
			 </select>
			
             
             <select name="justification">
			
			<!-- User can now add their justification to wanting/denying a particular class -->
             <?php

            //justification input and save back to database for the instructor
             ?>
 
             </select>
 
             <!-- On submit, will update the current database -->
             <input type="submit" value="Set">
             </form>
 
	<?php
 
         }
         //Display an error if there is a database error
         else
         {
             echo $mysqli->error;
             echo "<div align='center'>Invalid request. Please contact a system administrator.</div>";
         }
 
         $mysqli->close();
     }
     //If there is no connection to the DB, display an error
     else
         echo "<div align='center'>Unable to connect to database.</div>";
     ?>
 
</div>
</body>
</html>
