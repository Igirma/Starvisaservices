<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';

class filters extends admin
{
	function __construct()
	{
		parent::__construct();
		
		$this->lang->load('filters');
		
	}
	
	function index()
	{
		$controller = $this->url->segment(2);
		
		$this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
		$this->form->set_rules('main_menu[]', 'In menu tonen?', 'numeric');
		
		if($this->form->run())
		{
			$this->filters_model->update_overview($_POST);
			
			if(!$this->db->error)
			{
				// $this->alert->clear();
				$this->alert->add($this->lang->line('success'), 'success');
				$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/'. $controller);
			}
		}
		$data['filter'] = $this->filters_model->fetch_all($controller);
		$data['languages'] = $this->languages_model->fetch_all();
		$data['default_language'] = $this->config->item('default_language');
		
		$this->load->view('filters_overview', $data);
	}
	
	function add()
	{
		$filter_type = $this->url->segment(3);
	
		$this->form->set_rules('filter[title]', $this->lang->line('content_title'), 'required');
		
		(isset($_POST['filter']['slug']) && $_POST['filter']['slug'] == '' ? $_POST['filter']['slug'] = $this->url->string_to_url($_POST['filter']['title']) : '');
		
		$this->form->set_rules('filter[slug]', $this->lang->line('slug'), 'unique_url_add');
		
		if($this->form->run())
		{
			$filter_id = $this->filters_model->add($_POST, $filter_type);
			
			if(!$this->db->error)
			{
				if(isset($_POST['save_and_back']))
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $filter_type);
				}
				else
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $filter_type . '/' . $filter_id . '/' . $this->config->item('default_language'));
				}
			}
		}
		$controller = $this->url->segment(3);
		
		$data['languages'] = $this->admin_model->fetch_languages();
		$this->load->view('filters_add_edit', $data);
	}
	
	function edit()
	{
		$filter	 = $this->url->segment(3);
		$filter_id = $this->url->segment(4);
		$language_id = $this->url->segment(5);
		
		$this->form->set_rules('filter[title]', $this->lang->line('title'), 'required');
		$this->form->set_message('filter_date', $this->lang->line('filter_date_length'));
		
		(isset($_POST['filter']['slug']) && $_POST['filter']['slug'] == '' ? $_POST['filter']['slug'] = $this->url->string_to_url($_POST['filter']['title']) : '');
		
		$this->form->set_rules('filter[slug]', $this->lang->line('slug'), 'unique_url_edit[' . $filter_id . ']');
		
		$this->form->set_rules('filter[slug]', $this->lang->line('slug'), 'unique_url_edit[' . $filter_id . ']');

		if($this->form->run())
		{
			
			$this->filters_model->edit($_POST, $filter_id, $language_id, $filter);
			
			if(!$this->db->error)
			{
				$this->alert->add($this->lang->line('success'), 'success');
					
				if(isset($_POST['save_and_back']))
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $filter);
				}
				elseif(isset($_POST['upload']))
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $filter . '/' . $filter_id . '/' . $language_id . '/anchor_media');
				}
				else
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $filter . '/' . $filter_id . '/' . $language_id);
				}
			}
		}
		
		$data = $this->filters_model->fetch($filter_id, $language_id);
		
		$data['filter']['language'] = $this->languages_model->fetch($language_id);
		
		$data['languages'] = $this->languages_model->fetch_all();
		
		$controller = $this->url->segment(3);
		$this->load->view('filters_add_edit', $data);
	}
	
	function delete()
	{
		$filter_type 	= $this->url->segment(2);
		$filter_id 	= $this->url->segment(4);
		
		$this->filters_model->delete($filter_id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $filter_type);
		}
	}
	
	function delete_media()
	{
		$controller 	= $this->url->segment(3);
		$media_id 		= $this->url->segment(4);
		$filter_id 	= $this->url->segment(5);
		$language_id 	= $this->url->segment(6);
		
		$this->filters_model->delete_media($media_id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $controller . '/edit/' . $filter_id . '/' . $language_id . '/anchor_media');
		}
	}
	
	function order_media()
	{
		$controller		= $this->url->segment(3);
		$direction 		= $this->url->segment(4);
		$table_id 		= $this->url->segment(5);
		$language_id 	= $this->url->segment(6);
		$current_order 	= $this->url->segment(7);
		
		$this->projects_model->order_media($direction, $table_id, $language_id, $current_order);
		
		if(!$this->db->error)
		{
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $controller . '/edit/' . $table_id . '/' . $language_id . '/anchor_media');
		}
	}
	
	function order()
	{
		
		$filter_type 	= $this->url->segment(3);
		$direction 		= $this->url->segment(4);
		$current_order 	= $this->url->segment(5);
		$controller 	= $this->url->segment(3);
		$filter_id 		= $this->url->segment(6);
	
		$this->filters_model->order($direction, $current_order, $controller, $filter_id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $filter_type . '/success');
		}
	}
	
}
?>