<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';

class translations extends admin
{		
	function __construct()
	{
		parent::__construct();
		
		$this->lang->load('translations');

	}
	
	
	function index()
	{	
			

		$data['languages'] = $this->languages_model->fetch_all();

		$data['default_language'] = $this->config->item('default_language');
		
		$this->load->view('translations_overview', $data);
	}
	
	function add()
	{
		$data = array();
		$this->load->view('translations_add_edit', $data);
		exit;
	}

}
?>