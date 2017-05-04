<?php 
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';

class newsletter extends admin
{
	var $image_settings = array();

	function __construct()
	{
		parent::__construct();
		$this->lang->load('newsletter');
		
	}
	
	function index()
	{	
		$this->form->set_rules('active[]', $this->lang->line('active'), 'numeric');
		
		if($this->form->run())
		{
			$counts = $this->newsletter_model->update_overview($_POST, $this->config->item('default_language'));
			
			if(!$this->db->error)
			{
				// $this->alert->clear();
				if(isset($_POST['newsletter_send']) && $_POST['newsletter_send'] > 0)
					$this->alert->add($this->lang->line('mail_sent').' ' .(($counts)?$counts:'0').' '.$this->lang->line('members'), 'success');
				else $this->alert->add($this->lang->line('success'), 'success');
				
				$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . ($this->url->segment(2) == 'archive' ? '/' . $this->url->segment(2) : ''));
			}
		}
		
		$archive = 0;
		
		if($this->url->segment(2) == 'archive')
		{
			$archive = 1;
		}
		
		$data['newsletter'] 	= $this->newsletter_model->fetch_all($archive);
		$data['languages'] 		= $this->languages_model->fetch_all();
		$data['groups'] 		= $this->newsletter_model->fetch_all_categories();

		$this->load->view('newsletter_overview', $data);
	}
	
	function add()
	{
		$this->form->set_rules('newsletter[title]', $this->lang->line('title'), 'required');
		$this->form->set_rules('newsletter[date]', $this->lang->line('date'), 'required|exact_length[10]');
		
		
		$this->form->set_message('date', $this->lang->line('date_length'));
		
		if($this->form->run())
		{
			$newsletter_id = $this->newsletter_model->add($_POST);
			
			$_FILES['docs'] = rearrange_files($_FILES['docs']);
			
			if(!empty($_FILES['docs']))
			{				
				foreach($_FILES['docs'] as $doc)
				{
					add_doc($doc);
					$this->admin_model->add_doc($doc, $newsletter_id);
				}
			}
			
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
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $newsletter_id . '/' . $this->config->item('default_language'));
				}
			}
		}
		
		$data['events'] = $this->newsletter_model->fetch_all_events();
		$data['news_items'] = $this->newsletter_model->fetch_all_news();
		
		$this->load->view('newsletter_add_edit', $data);
	}
	
	function edit()
	{
		$newsletter_id 		= $this->url->segment(3);
		$language_id 	= $this->url->segment(4);
		
		$this->form->set_rules('newsletter[title]', $this->lang->line('title'), 'required');
		$this->form->set_rules('newsletter[date]', $this->lang->line('date'), 'required|exact_length[10]');
		
		$this->form->set_message('date', $this->lang->line('date_length'));
		
		if($this->form->run())
		{
			$_FILES['docs'] = rearrange_files($_FILES['docs']);

			if(!empty($_FILES['docs']))
			{				
				foreach($_FILES['docs'] as $doc)
				{
					add_doc($doc);
					$this->admin_model->add_doc($doc, $newsletter_id);
				}
			}
			
			$this->newsletter_model->edit($_POST, $newsletter_id, $language_id);
			
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
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $newsletter_id . '/' . $language_id . '/anchor_media');
				}
				else
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $newsletter_id . '/' . $language_id);
				}
			}
		}
		
		$data = $this->newsletter_model->fetch($newsletter_id, $language_id);
		
		$data['newsletter']['language'] = $this->languages_model->fetch($language_id);
		
		$data['languages'] = $this->languages_model->fetch_all();
		$data['events'] = $this->newsletter_model->fetch_all_events();
		$data['news_items'] = $this->newsletter_model->fetch_all_news($data['newsletter']['newsletter_id']);
		$data['newsletter']['news'] = $this->newsletter_model->fetch_all_news_selected($data['newsletter']['newsletter_id']);
		
		$this->load->view('newsletter_add_edit', $data);
	}
	
	function delete()
	{
		$id = $this->url->segment(3);
		
		$this->newsletter_model->delete($id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . ($this->url->segment(2) == 'archive' ? '/' . $this->url->segment(2) : ''));
		}
	}
	
	function order_media()
	{
		$direction 		= $this->url->segment(3);
		$table_id 		= $this->url->segment(4);
		$language_id 	= $this->url->segment(5);
		$current_order 	= $this->url->segment(6);
		
		$this->newsletter_model->order_media($direction, $table_id, $language_id, $current_order);
		
		if(!$this->db->error)
		{
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/anchor_media');
		}
	}
	
	function delete_media()
	{
		$media_id 		= $this->url->segment(3);
		$table_id 		= $this->url->segment(4);
		$language_id 	= $this->url->segment(5);
		
		$this->newsletter_model->delete_media($media_id);
		
		if(!$this->db->error)
		{
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/anchor_media');
		}
	}
	
}

?>