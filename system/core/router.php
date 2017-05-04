<?php 
class router
{
	var $config;
	var $url;
	var $default_controller;
	var $class;
	var $method;
	var $directory;
	var $admin = false;
	var $routes = array();
	
	function __construct()
	{
		$this->config 	=& load_class('config', 'core');
		$this->url 		=& load_class('url', 'core');
		$this->db 		=& load_class('db', 'core');
	}

	function _set_routing()
	{	
		$this->default_controller = $this->config->item('default_controller');
		
		# fetch the url and create segments
		$this->url->_fetch_url_string();
		$this->url->_explode_segments();
		
		$r = $this->db->query('SELECT `page_content`.page_id FROM `page_content` WHERE `page_content`.slug = ? AND `page_content`.language_id = ?', array(end($this->url->segments), CUR_LANG));
		if(!$r)
		{
			define('PAGE_ID', NULL);
		}
		else
		{
			define('PAGE_ID', $r[0]['page_id']);
		}
		
		require_once(SYS_PATH . 'config/routes.php');
		$this->routes = (!isset($route[0]) OR ! is_array($route[0])) ? (!isset($category_route[0]) OR ! is_array($category_route[0])) ? array() : $category_route[0] : $route[0];
	}
	
	function fetch_controller()
	{
		return $this->controller;
	}

	/**
	 * Fetches controller starting from the back of the URL
	 * @param string $module		URL part 0
	 * @param string $controller	URL part 1
	 * @param string $submodule		URL part 1
	 */
	function fetch_controller_file($module, $controller, $submodule = null)
	{
		# is a route set in the DB than call a custom controller within the page module. 
		# Example: `/fotoalbum` calls custom controller `photo.php`
		if(isset($this->routes['controller']))
		{
			$module = $this->routes['controller'];
		}
		
		# used for disabling cache in admin
		if($module == 'admin')
		{
			$this->admin = true;
		}
		
		if($module == '')
		{
			$module = 'home';
		}
		
		# sub-module exists?
		if(is_dir(APP_PATH . 'modules/' . $module . '/submodules/' . $submodule . '/'))
		{
			$this->directory = APP_PATH . 'modules/' . $module . '/submodules/' . $submodule . '/';
			
			# sub-module sub-controller exists?
			if(is_file($this->directory . 'controllers/' . $this->url->segment(2) . '.php'))
			{
				$this->controller = $this->url->segment(2);
				return $this->directory . 'controllers/' . $this->url->segment(2) . '.php';
			}
				
			# sub-module controller exists?
			if(is_file($this->directory . 'controllers/' . $controller . '.php'))
			{
				$this->controller = $controller;
				return $this->directory . 'controllers/' . $controller . '.php';
			}
		}
		
		# module exists?
		if(is_dir(APP_PATH . 'modules/' . $module . '/'))
		{
			$this->directory = APP_PATH . 'modules/' . $module . '/';
			
			# module sub-controller exists?
			if(is_file($this->directory . 'controllers/' . $controller . '.php'))
			{
				$this->controller = $controller;
				return $this->directory . 'controllers/' . $controller . '.php';
			}
			
			# default module controller exists?
			if(is_file($this->directory . 'controllers/' . $module . '.php'))
			{
				$this->controller = $module;
				return $this->directory . 'controllers/' . $module . '.php';
			}
		}
		
		# is it a regular controller within the pages module?
		if(is_dir(APP_PATH . 'modules/' . $this->default_controller . '/'))
		{
			$this->directory = APP_PATH . 'modules/' . $this->default_controller . '/';
			
			# module controller exists?
			if(is_file($this->directory . 'controllers/' . $module . '.php'))
			{
				$this->controller = $module;
				return $this->directory . 'controllers/' . $module . '.php';
			}
		}
		
		# no modules or controllers found? load default_controller
		$this->controller = $this->default_controller;
		$this->directory = APP_PATH . 'modules/' . $this->controller . '/';
		return APP_PATH . 'modules/' . $this->default_controller . '/controllers/' . $this->default_controller . '.php';
		
	}
	
	function set_method(&$CO = null)
	{
		foreach($this->url->segments as $segment)
		{
			if(method_exists($CO, $segment))
			{
				$this->method = $segment;
				return;
			}
		}
	
		$this->method = 'index';
	}
	
	function fetch_method(&$CO)
	{
		$this->set_method($CO);
		return $this->method;
	}
}
?>