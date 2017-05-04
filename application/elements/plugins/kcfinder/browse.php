<?php

/** This file is part of KCFinder project
  *
  *      @desc Browser calling script
  *   @package KCFinder
  *   @version 2.5
  *    @author Pavel Tzonkov <pavelc@users.sourceforge.net>
  * @copyright 2010, 2011 KCFinder Project
  *   @license http://www.opensource.org/licenses/gpl-2.0.php GPLv2
  *   @license http://www.opensource.org/licenses/lgpl-2.1.php LGPLv2
  *      @link http://kcfinder.sunhater.com
  */
  
session_start();
require "../../../../system/config/config.php";

$mysqli = new mysqli("localhost", $config['default_site']['user'], $config['default_site']['pass'], $config['default_site']['database']);

/* check connection */
if ($mysqli->connect_errno) {
    die("You are not loogged in.");
    exit();
}

/* Select queries return a resultset */
if ($result = $mysqli->query('SELECT `user`.login_salt,`user`.ip FROM `user` WHERE `user`.login_salt = "'.$_SESSION['login_salt'].'" AND `user`.ip = "'.$_SERVER['REMOTE_ADDR'].'"')) {
    
	if($result->num_rows == 0)
	{
		die("You are not loogged in.");
	}
	
    /* free result set */
    $result->close();
}
else die("You are not loogged in.");

$mysqli->close();
  
require "core/autoload.php";
$browser = new browser();
$browser->action();

?>