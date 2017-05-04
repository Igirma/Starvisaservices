<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';

class landingspages extends admin
{		
	function __construct()
	{
		parent::__construct();
		
		$this->lang->load('landingspages');
		
		$this->image_settings = array(
			array(
				'sub_folder' => 'thumb',
				'width' => LANDINGSPAGES_IMG_THUMB_W,
				'height' => LANDINGSPAGES_IMG_THUMB_H,
				'type' => 'crop'
			),
			array(
				'sub_folder' => 'normal',
				'width' => LANDINGSPAGES_IMG_NORMAL_W,
				'height' => LANDINGSPAGES_IMG_NORMAL_H,
				'type' => 'resize'
			),
			array(
				'sub_folder' => 'max',
				'width' => LANDINGSPAGES_IMG_MAX_W,
				'height' => LANDINGSPAGES_IMG_MAX_H,
				'type' => 'resize'
			)
		);
		
	}
	
	function index()
	{	
		$this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
		$this->form->set_rules('main_menu[]', 'In menu tonen?', 'numeric');
		
		if($this->form->run())
		{
			$this->landingspages_model->update_overview($_POST);
			
			if(!$this->db->error)
			{
				// $this->alert->clear();
				$this->alert->add($this->lang->line('success'), 'success');
				$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
			}
		}
		
		$data['landingspages'] = $this->landingspages_model->fetch_all();

		$data['languages'] = $this->languages_model->fetch_all();

		$data['default_language'] = $this->config->item('default_language');
		
		$this->load->view('landingspages_overview', $data);
	}
	
	function add()
	{
		if(isset($_POST['external']) && $_POST['external'] == 1)
		{
			$this->form->set_rules('form[landingspage_content][ex_url]', $this->lang->line('ex_url'), 'required|prep_url');
			$this->form->set_rules('form[landingsmobile_content][ex_url]', $this->lang->line('ex_url'), 'required|prep_url');
		}
		
		(isset($_POST['form']['landingspage_content']['slug']) && $_POST['form']['landingspage_content']['slug'] == '' ? $_POST['form']['landingspage_content']['slug'] = $this->url->string_to_url($_POST['form']['landingspage_content']['content_title']) : '');
		
		if(isset($_POST['external']) && $_POST['external'] == 0)
		{
			$this->form->set_rules('form[landingspage_content][content_title]', $this->lang->line('content_title'), 'required');
			$this->form->set_rules('form[landingspage_content][slug]', $this->lang->line('slug'), 'unique_url_add');
			
			if($this->config->item('mobile_website') == 1)
			{
				(isset($_POST['form']['landingsmobile_content']['slug']) && $_POST['form']['landingsmobile_content']['slug'] == '' ? $_POST['form']['landingsmobile_content']['slug'] = $this->url->string_to_url($_POST['form']['landingsmobile_content']['content_title']) : '');
				$this->form->set_rules('form[landingsmobile_content][content_title]', $this->lang->line('content_title'), 'required');
				$this->form->set_rules('form[landingsmobile_content][menu_title]', $this->lang->line('menu_title'), 'required');
				$this->form->set_rules('form[landingsmobile_content][slug]', $this->lang->line('slug'), 'unique_url_add');
			}
		}
		
		$this->form->set_rules('form[landingspage_content][menu_title]', $this->lang->line('menu_title'), 'required');
		
		if($this->form->run())
		{
			$id = $this->landingspages_model->add($_POST);
			
			$_FILES = rearrange_files($_FILES['photo']);
		
			if(!empty($_FILES))
			{
				foreach($_FILES as $media)
				{
					$photo = new photo(
						$media,
						$this->image_settings,
						MEDIA_DIR,
						CONTROLLER,
						$id,
						0,
						$media['name']
					);
				}
			}
			
			if(!$this->db->error)
			{
				if(!$this->db->error)
				{
					$this->alert->add($this->lang->line('success'), 'success');
					
					if(isset($_POST['save_and_back']))
					{
						$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
					}
					else
					{
						$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id . '/' . $this->config->item('default_language'));
					}
				}
			}
		}
		
		$data['drop_down_category']		= $this->categories_model->fetch_all(CONTROLLER);
		$data['drop_down'] 				= $this->landingspages_model->fetch_drop_down();
		$data['landingspage']['filters'] = $this->landingspages_model->getFilters('landingspages', $this->config->item('default_language'));
		
		$this->load->view('landingspages_add_edit',$data);
	}
	
	function edit()
	{
		$landingspage_id 	= $this->url->segment(3);
		$language 			= $this->url->segment(4);
		
		if($language == '')
		{
			$language = $this->config->item('default_language');
		}
		
		if(isset($_POST['external']) && $_POST['external'] == 1)
		{
			$this->form->set_rules('form[landingspage_content][ex_url]', $this->lang->line('ex_url'), 'required|prep_url');
			$this->form->set_rules('form[landingsmobile_content][ex_url]', $this->lang->line('ex_url'), 'required|prep_url');
		}
		
		(isset($_POST['form']['landingspage_content']['slug']) && $_POST['form']['landingspage_content']['slug'] == '' ? $_POST['form']['landingspage_content']['slug'] = $this->url->string_to_url($_POST['form']['landingspage_content']['content_title']) : '');
		
		if(isset($_POST['external']) && $_POST['external'] == 0)
		{
			$this->form->set_rules('form[landingspage_content][content_title]', $this->lang->line('content_title'), 'required');
			$this->form->set_rules('form[landingspage_content][slug]', $this->lang->line('slug'), 'unique_url_edit[' . $landingspage_id . ']');
			
			if($this->config->item('mobile_website') == 1)
			{
				(isset($_POST['form']['landingsmobile_content']['slug']) && $_POST['form']['landingsmobile_content']['slug'] == '' ? $_POST['form']['landingsmobile_content']['slug'] = $this->url->string_to_url($_POST['form']['landingsmobile_content']['content_title']) : '');
				$this->form->set_rules('form[landingsmobile_content][content_title]', $this->lang->line('content_title'), 'required');
				$this->form->set_rules('form[landingsmobile_content][menu_title]', $this->lang->line('menu_title'), 'required');
				$this->form->set_rules('form[landingsmobile_content][slug]', $this->lang->line('slug'), 'unique_url_edit[' . $landingspage_id . ']');
			}
		}
		
		$this->form->set_rules('form[landingspage_content][menu_title]', $this->lang->line('menu_title'), 'required');
		
		if($this->form->run())
		{
			$info = $this->landingspages_model->fetch($landingspage_id, $language);
			
			$this->landingspages_model->edit($landingspage_id, $language, $_POST);
			
			if($info['form']['landingspage_content']['slug'] != $_POST['form']['landingspage_content']['slug'])
			{
				$this->admin_model->links_crawl(SITE_URL_WOHTTP . $info['form']['landingspage_content']['slug'], SITE_URL_WOHTTP . $this->url->string_to_url($_POST['form']['landingspage_content']['slug']));
			}

			$_FILES = rearrange_files($_FILES['photo']);
			
			if(!empty($_FILES))
			{
				foreach($_FILES as $media)
				{
					$photo = new photo(
						$media,
						$this->image_settings,
						MEDIA_DIR,
						CONTROLLER,
						$landingspage_id,
						0,
						$media['name']
					);
				}
			}
			
			if(!$this->db->error)
			{
				$this->cache->clear();
				
				if(!$this->db->error && empty($this->form->_error_messages))
				{
					$this->alert->add($this->lang->line('success'), 'success');
					if(isset($_POST['save_and_back']))
					{
						$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
					}
					elseif(isset($_POST['upload']))
					{
						$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $landingspage_id . '/' . $language . '/anchor_media');
					}
					else
					{
						$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $landingspage_id . '/' . $language);
					}
				}
			}
		}
		
		if(isset($_POST['ajax']))
		{
			$filename = $_POST['filename'];
			
			$crop = new crop($filename, LANDINGSPAGES_DIR_CROP_REPLACE);
			$crop->resizeImage(LANDINGSPAGES_CROP_MAX_W, LANDINGSPAGES_CROP_MAX_H, $_POST['x'], $_POST['x2'], 'crop');
			$crop->saveImage($filename, 100);
		}
		
		$data['landingspage'] 					= $this->landingspages_model->fetch($landingspage_id, $language);
		$data['count_children'] 				= $this->landingspages_model->count_children($landingspage_id);
		$data['languages'] 						= $this->languages_model->fetch_all();
		$data['drop_down_category']				= $this->categories_model->fetch_all(CONTROLLER);
		$data['drop_down'] 						= $this->landingspages_model->fetch_drop_down();
		$data['landingspage']['category_id'] 	= $this->members_model->fetch_all_category_selected($landingspage_id, CONTROLLER);
		
		if(isset($data['landingspage']['category_id'])) 
			$data['landingspage']['filters'] = $this->landingspages_model->getFilters('landingspages', $this->config->item('default_language'), $landingspage_id, $data['landingspage']['category_id']);
		
		$this->load->view('landingspages_add_edit', $data);
	}
	
	function duplicate()
	{
		$landingspage_id 	= $this->url->segment(3);

			$this->landingspages_model->collectForDuplicate($landingspage_id, $this->image_settings);
			
			if(!$this->db->error)
			{
				if(!$this->db->error && empty($this->form->_error_messages))
				{
					$this->alert->add($this->lang->line('success'), 'success');
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
				}
			}
			else
			{
				die($this->db->error);
			}
	}
	
	function delete()
	{
		$id = $this->url->segment(3);
		
		$this->landingspages_model->delete($id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
		}
	}
	
	function order()
	{
		$direction 		= $this->url->segment(3);
		$current_order 	= $this->url->segment(4);
		$parent_id 		= $this->url->segment(5);
		$landingspage_id 		= $this->url->segment(6);
	
		$this->landingspages_model->order($direction, $current_order, $parent_id, $landingspage_id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
		}
	}
	
	function order_media()
	{
		$direction 		= $this->url->segment(3);
		$table_id 		= $this->url->segment(4);
		$language_id 	= $this->url->segment(5);
		$current_order 	= $this->url->segment(6);
		
		$this->landingspages_model->order_media($direction, $table_id, $language_id, $current_order);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/anchor_media');
		}
	}
	
	function delete_media()
	{
		$media_id 		= $this->url->segment(3);
		$landingspage_id 		= $this->url->segment(4);
		$language_id 	= $this->url->segment(5);
		
		$this->landingspages_model->delete_media($media_id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $landingspage_id . '/' . $language_id . '/anchor_media');
		}
	}
}
?>