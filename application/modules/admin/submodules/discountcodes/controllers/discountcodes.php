<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';
require_once SYS_PATH . 'core/twitter.php';


class discountcodes extends admin
{
	function __construct()
	{
		parent::__construct();
		
		$this->lang->load('discountcodes');

	}
	
	function index()
	{
		$this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
		
		if($this->form->run())
		{
			$this->discountcodes_model->update_overview($_POST);
			
			if(!$this->db->error)
			{
				// $this->alert->clear();
				$this->alert->add($this->lang->line('success'), 'success');
				$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
			}
		}
		
		$data = $this->discountcodes_model->fetch_all();
		$this->load->view('discountcodes_overview', $data);
	}
	
	function add()
	{
		$this->form->set_rules('discountcodes[title]', $this->lang->line('title'), 'required');
		$this->form->set_message('discountcodes_date', $this->lang->line('discountcodes_date_length'));
		
		if($this->form->run())
		{
			$discountcodes_id = $this->discountcodes_model->add($_POST);

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
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $discountcodes_id );
				}
			}
		}
		
		$this->load->view('discountcodes_add_edit');
	}
	
	function edit()
	{
		$this->form->set_rules('discountcodes[title]', $this->lang->line('title'), 'required');
		$this->form->set_message('discountcodes_date', $this->lang->line('discountcodes_date_length'));
		
		$discountcodes_id = $this->url->segment(3);
		
		if($this->form->run())
		{

			$this->discountcodes_model->edit($_POST, $discountcodes_id);
			
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
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $discountcodes_id );
				}
			}
		}
		if($this->url->segment(4) == 'delete'){
			$this->discountcodes_model->delete_code($this->url->segment(5));
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $discountcodes_id );
		}
		
		$data = $this->discountcodes_model->fetch($discountcodes_id);
		
		$this->load->view('discountcodes_add_edit', $data);
	}
	
	function delete()
	{
		$id = $this->url->segment(3);
		
		$this->discountcodes_model->delete($id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
		}
	}

}
?>