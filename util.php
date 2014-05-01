<?php
	function militaryTimeToNormalTime( $value )
	{
		return is_numeric( $value ) ? ( number_format( ( $value >= 1300 ? $value - 1200 : ( $value < 100 ? $value + 1200 : $value ) ) / 100 , 2 , ':' , '' ) . ( $value < 1200 ? " AM" : " PM" ) ) : $value;
	}

	function semesterYearToCatalogYear( $semester , $year )
	{
		if( $semester == "Spring" )
			return ( $year - 1 ) * 10000 + $year;
		else
			return $year * 10000 + ( $year + 1 );
	}

	function catalogYearToHumanReadable( $catalogYear )
	{
		return is_numeric( $catalogYear ) ? number_format( $catalogYear / 10000 , 4 , '-' , '' ) : $catalogYear;
	}
?>
