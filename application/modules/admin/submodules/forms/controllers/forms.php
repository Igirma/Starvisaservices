<?php 
require_once APP_PATH . 'modules/admin/controllers/admin.php';

class forms extends admin
{
	function __construct()
	{
		parent::__construct();
		$this->lang->load('forms');
	}
	
	function index()
	{
		$filter = $this->url->segment(2);
	
		$data = $this->forms_model->fetch_all($filter);
	
		$this->load->view('forms_overview', $data);
	}
	
	function archive()
	{
		$filter = $this->url->segment(3);
	
		$data = $this->forms_model->fetch_archive($filter);
	
		$this->load->view('forms_overview', $data);
	}

	function edit()
	{
		$form_id = $this->url->segment(3);
		
		$this->form->set_rules('archive', 'archive', 'required|is_numeric');
		
		if($this->form->run())
		{
			$this->forms_model->edit($_POST, $form_id);
		
			if(!$this->db->error)
			{
				if(isset($_POST['save_and_back']))
				{
					$this->url->redirect(SITE_URL . 'admin/forms');
				}
				else
				{
					$this->url->redirect(SITE_URL . 'admin/forms/edit/' . $form_id);
				}
			}
		}
	
		$data = $this->forms_model->fetch($form_id);
	
		$this->load->view('forms_add_edit', $data);
	}
	
	function delete()
	{
		$form_id = $this->url->segment(3);
		
		$this->forms_model->delete($form_id);
		
		if(!$this->db->error)
		{
			$this->url->redirect(SITE_URL . 'admin/forms');
		}
	}
}

?>