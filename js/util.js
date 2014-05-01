// http://stackoverflow.com/questions/7744912/making-a-javascript-string-sql-friendly

function mysql_real_escape_string( str )
{
	return str.replace( /[\0\x08\x09\x1a\n\r"'\\\%]/g , function( char ) {
		switch ( char )
		{
			case "\0":
				return "\\0";
			case "\x08":
				return "\\b";
			case "\x09":
				return "\\t";
			case "\x1a":
				return "\\z";
			case "\n":
				return "\\n";
			case "\r":
				return "\\r";
			case "\"":
			case "'":
			case "\\":
			case "%":
				return "\\" + char; // prepends a backslash to backslash, percent,
				// and double/single quotes
		}
	} );
}

function doQuery( sql )
{
	// Send query
	var result = $.ajax(
	{
		url: 'query.php',
		type: 'POST',
		dataType: "html",
		data: {
			query: sql
		},
		error: function( XMLHttpRequest , textStatus , exception )
		{
			alert( "Ajax failure: " + textStatus );
		},
		async: false
	} );

	return JSON.parse( result.responseText );
}

function timeToMilitaryTime( timeString )
{
	timeString = timeString.toUpperCase();
	var am = timeString.indexOf( "AM" ) != -1;
	var pm = timeString.indexOf( "PM" ) != -1;
	var time = timeString.replace( /[^0-9]/g , "" ) - 0;

	if( time >= 0 && time < 2400 && !( am && pm ) )
	{
		if( pm && time < 1200 )
			time += 1200;

		if( am && time >= 1200 )
			time -= time - 1200;

		return time;
	}

	return -1;
}

function militaryTimeToNormalTime( time )
{
	var hours = Math.floor( time / 100 ) % 12;
	var minutes = ( time % 100 );
	minutes = ( minutes < 10 ? "0" : "" ) + minutes;

	return ( hours ? hours : 12 ) + ":" + minutes + ( time >= 1200 ? " PM" : " AM" );
}

function validateRNumber( value )
{
	return value - 0 > 0 && value.match( /[0-9]+/ );
}

function validateSemester( value )
{
	return value.match( /^(Fall|Spring|Summer I|Summer II)$/ );
}

function validateYear( value )
{
	value = value - 0;
	return value > 1920 && value < 3000;
}

function validateName( value )
{
	return value.length && value.match( /^([a-zA-Z]+(\-|\'|\. )?)*[a-zA-Z]+$/ );
}

function validateTitle( value )
{
	 return value.match( /^(Professor|FTI|GPTI)?$/ );
}
