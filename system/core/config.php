<?php 
class config
{
	var $config = array();
	
	function __construct()
	{
		$this->config =& get_config();
	}
	
	/**
	 * Fetch an item: $config[index][item]
	 * @param string $item
	 * @param string $index
	 * @return boolean|multitype:
	 */
	function item($item, $index = '')
	{
		if($index == '')
		{
			if(!isset($this->config[$item]))
			{
				return false;
			}
	
			$pref = $this->config[$item];
		}
		else
		{
			if(!isset($this->config[$index]))
			{
				return false;
			}
	
			if(!isset($this->config[$index][$item]))
			{
				return false;
			}
	
			$pref = $this->config[$index][$item];
		}
	
		return $pref;
	}
}
?>