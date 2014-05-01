<?php
	$pageSessionType = "instructor";
	include "sessionValidator.php";
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Load Editor</title>
  <link rel="stylesheet" href="css/style.css" type="text/css" media="screen">
 </head>

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

<?php
    $mysqli = new mysqli( $dbproHost , $dbproUsername , $dbproPassword , $dbproSchema );

    if( !$mysqli->connect_errno )
    {
        //Get current load Preference
        $sql = "SELECT loadPreference, CONCAT(firstName,' ',lastName)
                FROM Instructors
                WHERE rNumber = '$_SESSION[rNumber]'";

        //Display current load preference
        if( $result = $mysqli->query( $sql ) )
        {
            echo "<table border='1' id='htmlgrid' class='testgrid'>
			<tr>
			<th>Name</th>
			<th>Current Load Preference</th>
			</tr>";
            //Display result in a table
            while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
            {
                echo "<tr id='1'>";
                echo "<td>" . $row[ "CONCAT(firstName,' ',lastName)" ] . "</td>";
                echo "<td>" . $row[ "loadPreference" ] . "</td>";
                echo "</tr>";
            }

            echo "</table>";
            $result->close();
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
    </td>
    <td id="bodyTableRight">
    </td>
   </tr>
  </table>
 </body>
</html>

<!-- JavaScript -->
<script src="js/editablegrid-2.0.1.js"></script>
<script src="js/jquery-1.7.2.min.js" ></script>

<script>
window.onload = function()
{
	rNumber = <?php echo $_SESSION[ "rNumber" ]; ?>;

	if( document.getElementById( "htmlgrid" ) )
	{
		editableGrid = new EditableGrid( "Load Preference Table" , { editMode: "absolute", enableSort: false, modelChanged: function( rowIndex , columnIndex , oldValue , newValue , row ) {
			updateCellValue( this , rowIndex , columnIndex , oldValue , newValue , row );
		} } );

		// Build and load the metadata in JS
		editableGrid.load(
			{ metadata: [
				{ name: "name", datatype: "string", editable: false },
				{ name: "loadPreference", datatype: "string", editable: true, values: { "" : "" , "Spring" : "Spring" , "Fall" : "Fall" } }
		] } );

		editableGrid.addCellValidator( "loadPreference" , new CellValidator(
		{
			isValid: function( value ) { return !value.length || value == "Spring" || value == "Fall"; }
		} ) );

		// Attach to the HTML table and render
		editableGrid.attachToHTMLTable( "htmlgrid" );
		editableGrid.renderGrid();
	}
}

function updateCellValue( editableGrid , rowIndex , columnIndex , oldValue , newValue , row , onResponse )
{
	// Generate query
	var sql = "UPDATE Instructors SET loadPreference = '" + newValue + "' WHERE rNumber = " + rNumber + ";";

	// Send query
	$.ajax(
	{
		url: 'query.php',
		type: 'POST',
		dataType: "html",
		data: {
			query: sql
		},
		success: function( response )
		{
			//var data = JSON && JSON.parse( response ) || $.parseJSON( response );
			var data = JSON.parse( response );
			
			if( data.success )
			{
				// Nothing to do
			}
			else
			{
				editableGrid.setValueAt( rowIndex , columnIndex , oldValue );
				alert( data.errorString );
			}
		},
		error: function( XMLHttpRequest , textStatus , exception )
		{
			alert( "Ajax failure: " + textStatus );
		},
		async: true
	} );
}
</script>
