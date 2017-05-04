<?php 
class loader
{
  var $url;
  var $rtr;
  var $db;
  var $alert;
  var $photo;
  var $lang;
  var $pagination;
  var $config;
  //var $twitter;
  //var $facebook;
  var $menu;
  //var $curs;
  var $phery;
  var $users;
  
  function __construct()
  {
    $this->url =& load_class('url', 'core');
    $this->rtr =& load_class('router', 'core');
    $this->db =& load_class('db', 'core');
    $this->alert =& load_class('alert', 'core');
    //$this->photo =& load_class('photo', 'core');
    $this->lang =& load_class('lang', 'core');
    $this->pagination =& load_class('pagination', 'core');
    $this->config =& load_class('config', 'core');
    $this->form =& load_class('form_validation', 'core');
    //$this->twitter =& load_class('twitter', 'core');
    //$this->facebook =& load_class('facebook', 'core');
    $this->googleurl =& load_class('googleurl', 'core');
    //$this->curs =& load_class('cursBnrXML', 'core');
    $this->menu =& load_class('menu', 'core');
    $this->phery =& load_class('Phery', 'core');
	if($this->url->segment(1) != 'users') {
		$this->users =& load_class('users', 'core');
	}
  }

  /**
   * Loads a model and makes it accessible through $this->model_name
   * @param string 	$model 		the model to load
   * @param string 	$module 	the module to look in
   */
  function model($model, $module = '')
  {		
    # fetch base class
    $BASE =& get_instance();
    
    # model name taken?
    if(isset($BASE->$model))
    {
      die('The model name you are loading is the name of a resource that is already being used: ' . $model);
    }
    
    load_class('model', 'core');
    
    # check if the model exists within the modules directory
    if(is_file($this->rtr->directory . 'models/' . $model . '.php'))
    {
      require_once($this->rtr->directory . 'models/' . $model . '.php');
      
      # make it accessible through $this->MODEL_NAME
      $BASE->$model = new $model();
      return;
    }
    
    if(is_file(APP_PATH . 'modules/admin/models/' . $model . '.php'))
    {
      require_once(APP_PATH . 'modules/admin/models/' . $model . '.php');
        
      # make it accessible through $this->MODEL_NAME
      $BASE->$model = new $model();
      return;
    }

    if(is_file(APP_PATH . 'modules/admin/submodules/' . $module . '/models/' . $model . '.php'))
    {
      require_once(APP_PATH . 'modules/admin/submodules/' . $module . '/models/' . $model . '.php');
      
      # make it accessible through $this->MODEL_NAME
      $BASE->$model = new $model();
      return;
    }
    
    # no model found? die...
    die($module . ' : Could not load model: ' . $model);
  }
  
  /**
   * Includes the views and passes the data array to the view
   * @param string 	$view 		the view to load
   * @param string 	$module 	the module to look in
   * @param array 	$data 		data to pass
   */
  function view($view, $data = array())
  {
    # check if the view exists within the modules directory
    if(is_file($this->rtr->directory . 'views/' . $view . '.php'))
    {			
      require_once($this->rtr->directory . 'views/' . $view . '.php');
      return;
    }
    
    # could not find a view? die...
    die('Could not load view:' . $view);
  }
}
?>