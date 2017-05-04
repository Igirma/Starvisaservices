<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';

class sendingcosts extends admin
{
	function __construct()
	{
		parent::__construct();
		
		$this->lang->load('sendingcosts');
		
	}
	
	function index()
	{
		$this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
		
		if($this->form->run())
		{
			$this->sendingcosts_model->update_overview($_POST);
			
			if(!$this->db->error)
			{
				// $this->alert->clear();
				$this->alert->add($this->lang->line('success'), 'success');
				$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
			}
		}
		
		$data = $this->sendingcosts_model->fetch_all($this->config->item('default_language'));
		$this->load->view('sendingcosts_overview', $data);
	}
	
	function add()
	{
		
		if($this->form->run())
		{
			$sendingcosts_id = $this->sendingcosts_model->add($_POST);
	
			if(!$this->db->error)
			{
				// $this->alert->clear();
				$this->alert->add($this->lang->line('success'), 'success');
				
				if(isset($_POST['save_and_back']))
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
				}
				else
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER. '/edit/' . $sendingcosts_id);
				}
			}
		}
		$data['countries'] = $this->sendingcosts_model->fetch_countries($this->config->item('default_language'));
		$data['discount_type'] = $this->sendingcosts_model->fetch_discount_type();
		
		$this->load->view('sendingcosts_add_edit', $data);
	}
	
	function edit()
	{
		
		$sendingcosts_id = $this->url->segment(3);

		if($this->form->run())
		{
			
			$sendingcosts_id = $this->sendingcosts_model->edit($_POST);
			
			if(!$this->db->error)
			{
				// $this->alert->clear();
				$this->alert->add($this->lang->line('success'), 'success');
					
				if(isset($_POST['save_and_back']))
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
				}
				else
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER. '/edit/' . $sendingcosts_id);
				}
			}
		}

		$data = $this->sendingcosts_model->fetch($sendingcosts_id, $this->config->item('default_language'));		
		if(isset($data['sendingcosts'][0]))
			$data['countries'] = $this->sendingcosts_model->fetch_countries($this->config->item('default_language'), $data['sendingcosts'][0]['country_id']);
		else $data['countries'] = $this->sendingcosts_model->fetch_countries($this->config->item('default_language'));
		$data['discount_type'] = $this->sendingcosts_model->fetch_discount_type();
		
		$this->load->view('sendingcosts_add_edit', $data);
	}
	
	function delete()
	{
		$id = $this->url->segment(3);
		
		$this->sendingcosts_model->delete($id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
		}
	}

}
?>