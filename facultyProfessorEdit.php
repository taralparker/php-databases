<?php
	$pageSessionType = "faculty";
	include "sessionValidator.php";
	include "databaseSettings.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Professor Editor</title>
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
		if( $result = $mysqli->query( "SELECT * FROM Instructors;" ) )
		{
			echo "<table border='1' id='htmlgrid' class='testgrid' style='font-size:11px;'>
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

			while( $row = $result->fetch_array( MYSQLI_ASSOC ) )
			{
				echo '<tr id="' . $row[ "rNumber" ] . '">';
				echo "<td>" . $row[ "rNumber" ] . "</td>";
				echo "<td>" . $row[ "lastName" ] . "</td>";
				echo "<td>" . $row[ "firstName" ] . "</td>";
				echo "<td>" . $row[ "instructorTitle" ] . "</td>";
				echo "<td>" . $row[ "tenured" ] . "</td>";
				echo "<td>" . $row[ "joiningSemester" ] . "</td>";
				echo "<td>" . $row[ "joiningYear" ] . "</td>";
				//echo "<td>" . $row[ "loadPreference" ] . "</td>";
				echo "</tr>";
			}

			echo "</table>";
		}

		// Clean up
		$result->close();
		$mysqli->close();
	}
	else
	{
		printf( "Connection failed: %s<br>" , $mysqli->connect_error );
		exit();
	}
?>

      <br>
      <table border="1" id="htmlgrid2" class="testgrid" style="min-width: 90%;">
       <tr>
        <th>RNumber</th>
        <th>Last Name</th>
        <th>First Name</th>
        <th>Title</th>
        <th>Tenured</th>
        <th>Joining Semester</th>
        <th>Joining Year</th>
       </tr>
       <tr id="1">
        <td style="text-align: right;"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>Spring</td>
        <td></td>
       </tr>
      </table>
      <br>
      <button onclick="addInstructor();">Add</button>
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
<script src="js/sprintf.min.js" ></script>
<script src="js/util.js" ></script>

<script>
var editableGridView;
var editableGridEditor;

window.onload = function()
{
	editableGridView = new EditableGrid( "Professor View" , { editMode: "absolute", modelChanged: function( rowIndex , columnIndex , oldValue , newValue , row ) {
			updateCellValue( this , rowIndex , columnIndex , oldValue , newValue , row );
		} } );

	// Build and load the metadata in JS
	editableGridView .load(
		{ metadata: [
			{ name: "rNumber", datatype: "string", editable: false },
			{ name: "lastName", datatype: "string", editable: true },
			{ name: "firstName", datatype: "string", editable: true},
			{ name: "instructorTitle", datatype: "string", editable: true, values: { "" : "" , "Professor" : "Professor" , "FTI" : "FTI" , "GPTI" : "GPTI" } },
			{ name: "tenured", datatype: "boolean", editable: true },
			{ name: "joiningSemester", datatype: "string", editable: true, values: { "Spring" : "Spring" , "Summer I" : "Summer I" , "Summer II" : "Summer II" , "Fall" : "Fall" } },
			{ name: "joiningYear", datatype: "number", editable: true }
	] } );

	editableGridView.addCellValidator( "lastName" , new CellValidator(
	{
		isValid: function( value ) { return validateName( value ); }
	} ) );

	editableGridView.addCellValidator( "firstName" , new CellValidator(
	{
		isValid: function( value ) { return validateName( value ); }
	} ) );

	editableGridView.addCellValidator( "instructorTitle" , new CellValidator(
	{
		isValid: function( value ) { return validateTitle( value ); }
	} ) );

	editableGridView.addCellValidator( "joiningSemester" , new CellValidator(
	{
		isValid: function( value ) { return validateSemester( value ); }
	} ) );

	editableGridView.addCellValidator( "joiningYear" , new CellValidator(
	{
		isValid: function( value ) { return validateYear( value ); }
	} ) );

	// Attach to the HTML table and render
	editableGridView .attachToHTMLTable( "htmlgrid" );
	editableGridView .renderGrid();

	editableGridEditor = new EditableGrid( "Professor Editor" , { editMode: "absolute" , enableSort: false } );

	// Build and load the metadata in JS
	editableGridEditor.load(
		{ metadata: [
			{ name: "rNumber", datatype: "string", editable: true },
			{ name: "lastName", datatype: "string", editable: true },
			{ name: "firstName", datatype: "string", editable: true },
			{ name: "instructorTitle", datatype: "string", editable: true, values: { "" : "" , "Professor" : "Professor" , "FTI" : "FTI" , "GPTI" : "GPTI" } },
			{ name: "tenured", datatype: "boolean", editable: true },
			{ name: "joiningSemester", datatype: "string", editable: true, values: { "Spring" : "Spring" , "Summer I" : "Summer I" , "Summer II" : "Summer II" , "Fall" : "Fall" } },
			{ name: "joiningYear", datatype: "number", editable: true }
	] } );

	editableGridEditor.addCellValidator( "rNumber" , new CellValidator(
	{
		isValid: function( value ) { return validateRNumber( value ); }
	} ) );

	editableGridEditor.addCellValidator( "lastName" , new CellValidator(
	{
		isValid: function( value ) { return validateName( value ); }
	} ) );

	editableGridEditor.addCellValidator( "firstName" , new CellValidator(
	{
		isValid: function( value ) { return validateName( value ); }
	} ) );

	editableGridEditor.addCellValidator( "instructorTitle" , new CellValidator(
	{
		isValid: function( value ) { return validateTitle( value ); }
	} ) );

	editableGridEditor.addCellValidator( "joiningSemester" , new CellValidator(
	{
		isValid: function( value ) { return validateSemester( value ); }
	} ) );

	editableGridEditor.addCellValidator( "joiningYear" , new CellValidator(
	{
		isValid: function( value ) { return validateYear( value ); }
	} ) );

	// Attach to the HTML table and render
	editableGridEditor.attachToHTMLTable( "htmlgrid2" );
	editableGridEditor.renderGrid();
}

function addInstructor()
{
	/*
	editableGridEditor.enableSort = true;
	console.log( editableGridEditor );
	return;
	console.log( editableGridEditor.getCellEditor( 0 , 0 ) );
	var crap = new CellEditor();
	console.log( crap );
	delete crap;
	return;
	// BUG: Close open cell editors
	editableGridEditor.refreshGrid();
	*/

	// Collect data
	var rNumber , lastName , firstName , title , tenured , joiningSemester , joiningYear;
	var rowValues = editableGridEditor.getRowValues( 0 );
	rNumber = rowValues[ "rNumber" ];
	lastName = rowValues[ "lastName" ];
	firstName = rowValues[ "firstName" ];
	title = rowValues[ "instructorTitle" ];
	tenured = rowValues[ "tenured" ];
	joiningSemester = rowValues[ "joiningSemester" ];
	joiningYear = rowValues[ "joiningYear" ];

	//editableGridView.append( rNumber , { rNumber: rNumber , lastName: lastName , firstName: firstName , title: title , tenured: tenured , joiningSemester: joiningSemester , joiningYear: joiningYear } );
	//window.scrollTo( 0 , document.body.scrollHeight );

	// Validate one last time
	if( validateRNumber( rNumber ) && validateName( lastName ) && validateName( firstName ) && validateTitle( title ) && validateSemester( joiningSemester ) && validateYear( joiningYear ) )
	{
		// Generate query
		var sql = sprintf( "INSERT INTO `Instructors` ( `rNumber` , `firstName` , `lastName` , `instructorTitle` , `tenured` , `joiningSemester` , `joiningYear` , `loadPreference` ) VALUES ( %08d , '%s' , '%s' , '%s' , %b , '%s' , %s , NULL );" , rNumber - 0 , firstName , lastName , title , tenured - 0 , joiningSemester , joiningYear );

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
				var data = JSON.parse( response );

				if( data.success )
				{
					editableGridView.append( rNumber , { rNumber: rNumber , lastName: lastName , firstName: firstName , title: title , tenured: tenured , joiningSemester: joiningSemester , joiningYear: joiningYear } );
					window.scrollTo( 0 , document.body.scrollHeight );
				}
				else
					alert( data.errorString );
			},
			error: function( XMLHttpRequest , textStatus , exception )
			{
				alert( "Ajax failure: " + textStatus );
			},
			async: true
		} );
	}
}

function updateCellValue( editableGrid , rowIndex , columnIndex , oldValue , newValue , row , onResponse )
{
	// Generate query
	if( editableGrid.getColumnType( columnIndex ) == "boolean" )
		newValue = newValue == true ? 1 : 0;

	var sql = "UPDATE Instructors SET " + editableGrid.getColumnName( columnIndex ) + " = '" + newValue + "' WHERE rNumber = " + editableGrid.getRowId( rowIndex ) + ";";

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
			var data = JSON.parse( response );

			if( data.success )
			{
				//
			}
			else
			{
				editableGrid.setValueAt( rowIndex , columnIndex , oldValue );
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
