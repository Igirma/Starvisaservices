<?php 
require_once APP_PATH . 'modules/admin/controllers/admin.php';

class review extends admin
{
	function __construct()
	{
		parent::__construct();
		$this->lang->load('review');
	}
	
	function index()
	{
 
		$filter = $this->url->segment(2);
	
		$this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
		
		if($this->form->run())
		{
			$this->review_model->update_overview($_POST);
			if(!$this->db->error)
			{
				// $this->alert->clear();
				$this->alert->add($this->lang->line('success'), 'success');
				$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
			}
		}
		
		$data = $this->review_model->fetch_all($filter);
	
		$this->load->view('review_overview', $data);
	}
	
	function edit()
	{
		$review_id = $this->url->segment(3);
		
		$this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
		
		if($this->form->run())
		{
			$this->review_model->edit($_POST, $review_id);
			
			if(!$this->db->error)
			{
				$this->alert->add($this->lang->line('success'), 'success');
				if(isset($_POST['save_and_back']))
				{
					$this->url->redirect(SITE_URL . 'admin/review');
				}
				else
				{
					$this->url->redirect(SITE_URL . 'admin/review/edit/' . $review_id);
				}
			}
		}
	
		$data = $this->review_model->fetch($review_id);
	
		$this->load->view('review_add_edit', $data);
	}
	
	function delete()
	{
		$review_id = $this->url->segment(3);
		
		$this->review_model->delete($review_id);
		
		if(!$this->db->error)
		{
			$this->url->redirect(SITE_URL . 'admin/review');
		}
	}
}

?>