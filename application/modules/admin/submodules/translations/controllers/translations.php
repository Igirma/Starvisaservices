<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';

class translations extends admin
{		
	function __construct()
	{
		parent::__construct();
		
		$this->lang->load('translations');
		$this->load->model('translations_model');

	}
	
	
	function index()
	{	
			

		$data['languages'] = $this->languages_model->fetch_all();
		
		$data['translation'] = $this->translations_model->fetch_all();

		$data['default_language'] = $this->config->item('default_language');
		
		$this->load->view('translations_overview', $data);
	}
	
	function add()
	{	
		$this->load->view('translations_add_edit', $data);
	}

	function edit()
	{	
		$this->load->view('translations_add_edit', $data);

	}
}
?>