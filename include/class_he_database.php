<?php

if( !class_exists('he_database') ) {
class he_database
{
	function query($query)
	{
		global $database;
		return $database->database_query($query);
	}
	
	function num_rows($resource)
	{
		global $database;
		return $database->database_num_rows($resource);
	}
	
	function fetch_array($query, $field_as_index = null)
	{
		global $database;
		
		$rows = array();
		$res = he_database::query($query);

		if ( $field_as_index === null )
		{
			while($row = $database->database_fetch_assoc($res))
			  $rows[] = $row;
		}
		else
		{
			while($row = $database->database_fetch_assoc($res))
			  $rows[$row[$field_as_index]] = $row;
		}
		
		return $rows;
	}
	
	function fetch_column($query, $first_field_is_index = false)
	{
		global $database;
		
		$rows = array();
		$res = he_database::query($query);

		if ( $first_field_is_index )
		{
			while($row = $database->database_fetch_array($res))
			  $rows[$row[0]] = $row[1];
		}
		else
		{
			while($row = $database->database_fetch_array($res))
			  $rows[] = $row[0];
		}
		
		return $rows;
	}
	
	function fetch_row($query)
	{
		global $database;
		return $database->database_fetch_assoc(he_database::query($query));
	}
	
	function fetch_row_from_resource($resource)
	{
		global $database;
		return $database->database_fetch_assoc($resource);
	}
	
	function fetch_field($query)
	{
		global $database;
		$row = $database->database_fetch_array(he_database::query($query));
		return $row ? $row[0] : null;
	}
	
	function real_escape($unescaped_string)
	{
		global $database;
		return $database->database_real_escape_string($unescaped_string);
	}
	
	function insert_id()
	{
		global $database;
		return $database->database_insert_id();
	}
	
	function affected_rows()
	{
		global $database;
		return $database->database_affected_rows();
	}

	function compile_placeholder($query_tpl)
	{
		$compiled = array();
		$i = 0;	// placeholders counter
		$p = 0; // current position
		$prev_p = 0; // previous position
		
		while ( false !== ($p = strpos($query_tpl, '?', $p)) )
		{
			$compiled[] = substr($query_tpl, $prev_p, $p-$prev_p);
			
			$type_char = $char = $query_tpl{$p-1};
			
			switch ( $type_char ) {
				case '"': case "'": case '`':
					$type = $type_char;	// string
					break;
				default:
					$type = '';		// integer
					break;
			}
			
			$next_char = isset($query_tpl{$p+1}) ? $query_tpl{$p+1} : null;
			if ( $next_char === '@' ) {	// array list
				$compiled[] = array($i++, $type, '@');
				$prev_p = ($p=$p+2);
			}
			else {
				$compiled[] = array($i++, $type);
				$prev_p = ++$p;
			}
		}
		
		$tail_length = (strlen($query_tpl) - $prev_p);
		if ( $tail_length ) {
			$compiled[] = substr($query_tpl, -$tail_length);
		}
		
		return $compiled;
	}
	
	function placeholder()
	{
		$arguments = func_get_args();
		$c_query = array_shift($arguments);
		
		if ( !is_array($c_query) ) {
			$c_query = he_database::compile_placeholder($c_query);
		}
		
		$query = '';
		
		foreach ( $c_query as $piece )
		{
			if ( !is_array($piece) ) {
				$query .= $piece;
				continue;
			}
			
			list( $index, $type ) = $piece;
			
			if ( isset($piece[2]) ) // array value
			{
				$array = $arguments[$index];
				
				switch ( $type ) {
					case '"': case "'": case '`':
						$query .= implode("$type,$type", array_map(array(__CLASS__, 'real_escape'), $array));
						break;
					default:
						$query .= implode(",", array_map('intval', $array));
						break;
				}
			}
			else // scalar value
			{
				$var = $arguments[$index];
				
				switch ( $type ) {
					case '"': case "'": case '`':
						$query .= he_database::real_escape($var);
						break;
					default:
						$query .= (int)$var;
						break;
				}
			}
		}
		
		return $query;
	}
}

}
?>