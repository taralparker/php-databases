<?php
	// Change this to "faculty", "instructor", or "business"
	$pageSessionType = "instructor";
	include "sessionValidator.php";
	// Include this for global database access variables
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
		
        $sql = "SELECT CONCAT(firstName,' ',lastName), catalogYear, courseCode, rating FROM Instructors natural join Prefers WHERE rNumber = '$_SESSION[rNumber]'";

		if( $result = $mysqli->query( $sql ) )
		{
			echo "<table border='1' id='htmlgrid' class='testgrid'>
			<tr>
			<th>Name</th>
			<th>Catalog Year</th>
			<th>Course Code</th>
			<th>Rating</th>
			</tr>";

            //Display result in a table
           while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
           {
               echo "<tr>";
			   echo "<td>" . $row[ "CONCAT(firstName,' ',lastName)" ] . "</td>";
			   echo "<td>" . $row[ "catalogYear" ] . "</td>";
			   echo "<td>" . $row[ "courseCode" ] . "</td>";
			   echo "<td>" . $row[ "rating" ] . "</td>";
               echo "</tr>";
            }

            echo "</table>";
            $result->close();

            ?>
            <br>
 
             <!-- Display form for user to choose a new load preference -->
             <form align="center" name="coursePreferenceForm" action="instructorPreferenceUpdate.php" method="post">
             
			 <select name="courseCode">
			 
			 <?php
				$query = "SELECT courseCode , courseTitle FROM Courses" ;
				$result = $mysqli->query($query);
				while($row = $result->fetch_array( MYSQLI_ASSOC )) {
					echo '<option value="'.$row['courseCode'].'">' . "CS " . $row['courseCode'] . " - " . $row['courseTitle'] . '</option>';   
				}
			
			?>
			 </select>
			 
			 <select name="courseRating">
			
			<!-- User can select Fall or Spring as course preference -->
             <?php
				
				
                 $rating = array( "1" , "2", "3");
				 
                 for( $index = 0 ; $index < 3 ; $index++ )
                     echo "<option value='$rating[$index]' ". ( $rating[ "$index" ] == $_SESSION[ "courseRating" ] ? "selected" : "" ) .">$rating[$index]</option>";
             ?>
 
             </select>
 
             <!-- On submit, instructorLoadUpdate.php is called to update the database and update this page -->
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
