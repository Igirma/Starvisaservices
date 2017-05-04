<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';
require_once SYS_PATH . 'core/twitter.php';


class order_mails extends admin
{
	function __construct()
	{
		parent::__construct();
		
		$this->lang->load('order_mails');

	}
	
	function index()
	{
		$this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
		
		if($this->form->run())
		{
			$this->order_mails_model->update_overview($_POST);
			
			if(!$this->db->error)
			{
				// $this->alert->clear();
				$this->alert->add($this->lang->line('success'), 'success');
				$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
			}
		}
		
		$data = $this->order_mails_model->fetch_all();
		$data['languages'] = $this->languages_model->fetch_all();	
		$data['order_status'] = $this->order_mails_model->order_status();
		$this->load->view('order_mails_overview', $data);
	}
	
	function add()
	{
		
		$this->form->set_rules('order_mails[client_fromname]', $this->lang->line('client_fromname'), 'required');
		$this->form->set_rules('order_mails[client_subject]', $this->lang->line('client_subject'), 'required');
		$this->form->set_rules('order_mails[admin_fromname]', $this->lang->line('admin_fromname'), 'required');
		$this->form->set_rules('order_mails[admin_subject]', $this->lang->line('admin_subject'), 'required');
		
		if($this->form->run())
		{
			$order_mails_id = $this->order_mails_model->add($_POST);
				
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
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $order_mails_id . '/' . $this->config->item('default_language'));
				}
			}
		}
		
		$data['drop_down'] = $this->order_mails_model->fetch_drop_down(CONTROLLER);
		$data['order_status'] = $this->order_mails_model->order_status();
		
		$this->load->view('order_mails_add_edit', $data);
	}
	
	function edit()
	{
		$this->form->set_rules('order_mails[client_fromname]', $this->lang->line('client_fromname'), 'required');
		$this->form->set_rules('order_mails[client_subject]', $this->lang->line('client_subject'), 'required');
		$this->form->set_rules('order_mails[admin_fromname]', $this->lang->line('admin_fromname'), 'required');
		$this->form->set_rules('order_mails[admin_subject]', $this->lang->line('admin_subject'), 'required');
		
		$order_mails_id = $this->url->segment(3);
		$language_id = $this->url->segment(4);
		
		if($this->form->run())
		{
			
			$this->order_mails_model->edit($_POST, $order_mails_id, $language_id);
			
			if(!$this->db->error)
			{
				// $this->alert->clear();
				$this->alert->add($this->lang->line('success'), 'success');
					
				if(isset($_POST['save_and_back']))
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
				}
				elseif(isset($_POST['upload']))
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $order_mails_id . '/' . $language_id . '/anchor_media');
				}
				else
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $order_mails_id . '/' . $language_id);
				}
			}
		}

		$data = $this->order_mails_model->fetch($order_mails_id, $language_id);
		
		$data['order_mails']['language'] = $this->languages_model->fetch($language_id);
		
		$data['languages'] = $this->languages_model->fetch_all();
		$data['drop_down'] = $this->order_mails_model->fetch_drop_down(CONTROLLER);
		$data['count_children'] = $this->order_mails_model->count_children($order_mails_id);
		$data['order_status'] = $this->order_mails_model->order_status();
		
		$this->load->view('order_mails_add_edit', $data);
	}
	
	function delete()
	{
		$id = $this->url->segment(3);
		
		$this->order_mails_model->delete($id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
		}
	}
	
}
?>