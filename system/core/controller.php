<?php 
/**
 * 
 *
 */
class controller
{
	private static $instance;
	var $load;
	var $form;
	
	function __construct()
	{
		self::$instance =& $this;
		
		foreach(is_loaded() as $var => $class)
		{
			#assign all loaded main classes it's own $this->var variable, so $URL->segment becomes $this->url->segment etc
			$this->$var =& load_class($class, 'core');
		}
		$this->load =& load_class('loader', 'core');
		$this->form =& load_class('form_validation', 'core');
	}
	
	/**
	 * Enables classes to use the master $this
	 */
	public static function &get_instance()
	{
		return self::$instance;
	}
}
?>