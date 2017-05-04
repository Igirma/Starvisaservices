<?php 
ini_set('session.gc_maxlifetime', '7200');
session_start();

error_reporting(E_ALL);
//error_reporting(E_ALL & ~E_NOTICE);

require_once 'system/config/const.php';
require_once 'system/core/common_functions.php';
require_once 'system/core/core.php';

//empty_dir(BASE_PATH . '/application/elements/media');

?>