<?php 
class cache
{
	var $cachetime;
	
	/**
	 * Output a cached file
	 * @param int $cachetime time to cache in seconds
	 */
	function output($cachetime)
	{
		$CI = &get_instance();
		
		$this->cachetime = $cachetime;
		
		$page = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		$cachefile = CACHE_PATH . md5($page) . '.cache';
		
		$cachefile_created = (file_exists($cachefile)) ? filemtime($cachefile) : 0;
		
		clearstatcache();
		
		if(time() - $this->cachetime < $cachefile_created) 
		{
			@readfile($cachefile);
			exit();
		}
		
		ob_start();
	}
	
	/**
	 * Create a cachefile based on full url
	 */
	function create()
	{
		$CI = &get_instance();
		
		$page = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		$cachefile = CACHE_PATH . md5($page) . '.cache';
		
		$cachefile_created = (file_exists($cachefile)) ? filemtime($cachefile) : 0;
		
		clearstatcache();
		$fp = fopen($cachefile, 'w');
		@fwrite($fp, ob_get_contents());
		@fclose($fp);
		@ob_end_flush();
	}
	
	function clear()
	{
		if(count(glob(CACHE_PATH . '/*')) > 1)
		{
			foreach(glob(CACHE_PATH . '/*') as $cachefile)
			{
				@unlink($cachefile);
			}
		}
	}
}
?>