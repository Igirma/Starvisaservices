<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';

class help extends admin
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{	
		$this->load->view('help_overview');
	}
}

?>