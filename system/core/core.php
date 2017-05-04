<?php 
/**
 * This file handles all requests and calls the controller/method based on the url
 */

# load all main classes
$DB =& load_class('db', 'core');
$CFG =& load_class('config', 'core');
$URL =& load_class('url', 'core');
$RTR =& load_class('router', 'core');
$LANG =& load_class('lang', 'core');
$CACHE =& load_class('cache', 'core');
$ALERT =& load_class('alert', 'core');
$PAGINATION =& load_class('pagination', 'core');
//$TWITTER =& load_class('twitter', 'core');
//$FACEBOOK =& load_class('facebook', 'core');
$GOOGLEAPI =& load_class('googleurl', 'core');

# parse url and set controller/method
$RTR->_set_routing();

# include the controller class, all controllers extend from this class
require_once SYS_PATH . 'core/controller.php';

# include the called controller 
$controller_file = $RTR->fetch_controller_file($URL->segment(0), $URL->segment(1), $URL->segment(1));
$controller = $RTR->fetch_controller();
define('CONTROLLER', $controller);

require_once($controller_file);

# call the included class set by $RTR->fetch_controller and fetch the method set by $RTR->set_routing
$CO = new $controller();
$method = $RTR->fetch_method($CO);

cron();

# call the requested method
call_user_func_array(array($CO, $method), array());

//log_msg($controller . '::' . $method);

# create cache of not in admin
if(!$RTR->admin)
{
	$CACHE->create();
}
?>