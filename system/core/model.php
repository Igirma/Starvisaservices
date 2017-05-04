<?php 
class model
{	
	function __get($key)
	{
		$CI =& get_instance();
		return $CI->$key;
	}
}
?>