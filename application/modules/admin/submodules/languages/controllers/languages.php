<?php 
require_once APP_PATH . 'modules/admin/controllers/admin.php';

class languages extends admin
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$data = $this->languages_model->fetch_all();
		
		$this->load->view('languages_overview', $data);
	}
	
	function add()
	{
		$this->form->set_rules('name', 'Naam', 'required');
		$this->form->set_rules('code', 'Code', 'required|max_length[2]');
		
		if($this->form->run())
		{
			$this->languages_model->add($_POST);
			
			if(!$this->db->error)
			{
				$this->url->redirect(SITE_URL . LANG_CODE . '/admin/languages');
			}
		}
		
		$this->load->view('languages_add_edit');
	}
	
	function edit()
	{
		if($this->url->segment(3) == '')
			die('No language specified');
		
		$language = $this->url->segment(3);
		
		$this->form->set_rules('name', 'Naam', 'required|min_length[1]');
		$this->form->set_rules('code', 'Code', 'required|max_length[2]');
		
		if($this->form->run())
		{
			$this->languages_model->update(array('name' => $_POST['name'], 'code' => $_POST['code'], 'language_id' => $language));
				
			if(!$this->db->error)
			{
				$this->url->redirect(SITE_URL . LANG_CODE . '/admin/languages');
			}
		}
		
		$data = $this->languages_model->fetch($language);
		
		if($data != false)
		{
			$this->load->view('languages_add_edit', $data);
		}
	}
	
	function delete()
	{		
		$language = $this->url->segment(3);
		
		$this->languages_model->delete($language);
		
		if(!$this->db->error)
		{
			$this->url->redirect(SITE_URL . 'admin/languages/success');
		}
	}
}
?>