<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';

class members extends admin
{
	function __construct()
	{
		parent::__construct();
		
		$this->lang->load('members');

		$this->image_settings = array(
			array(
				'sub_folder' => 'thumb',
				'width' => MEMBERS_IMG_THUMB_W,
				'height' => MEMBERS_IMG_THUMB_H,
				'type' => 'crop'
			),
			array(
				'sub_folder' => 'normal',
				'width' => MEMBERS_IMG_NORMAL_W,
				'height' => MEMBERS_IMG_NORMAL_H,
				'type' => 'resize'
			),
			array(
				'sub_folder' => 'max',
				'width' => MEMBERS_IMG_MAX_W,
				'height' => MEMBERS_IMG_MAX_H,
				'type' => 'resize'
			)
		);
	}
	
	function index()
	{
		$this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
		
		if($this->form->run())
		{
			$this->members_model->update_overview($_POST);
			
			if(!$this->db->error)
			{
				// $this->alert->clear();
				$this->alert->add($this->lang->line('success'), 'success');
				$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
			}
		}
		
		$data = $this->members_model->fetch_all();
		$data['languages'] = $this->languages_model->fetch_all();	
		$this->load->view('members_overview', $data);
	}
	
	function add()
	{
		
		$this->form->set_rules('members[company_name]', $this->lang->line('company_name'), 'required');
		$this->form->set_message('date_birth', $this->lang->line('date_birth_length'));
		
		if($this->form->run())
		{
			$members_id = $this->members_model->add($_POST);

			$_FILES['photo'] = rearrange_files($_FILES['photo']);
			
			if(!empty($_FILES['photo']))
			{				
				foreach($_FILES['photo'] as $media)
				{
					$photo = new photo(
						$media,
						$this->image_settings,
						MEDIA_DIR,
						CONTROLLER,
						$members_id,
						0,
						$media['name']
					);
				}
			}
			echo $this->db->error;
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
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $members_id . '/' . $this->config->item('default_language'));
				}
			}
		}
		
		$data['drop_down'] = $this->members_model->fetch_drop_down(CONTROLLER);
		
		$this->load->view('members_add_edit', $data);
	}
	
	function edit()
	{
		$this->form->set_rules('members[company_name]', $this->lang->line('company_name'), 'required');
		$this->form->set_message('date_birth', $this->lang->line('date_birth_length'));
		
		$members_id = $this->url->segment(3);
		$language_id = $this->url->segment(4);
		
		
		if($this->form->run())
		{
			$_FILES['photo'] = rearrange_files($_FILES['photo']);
			
			if(!empty($_FILES['photo']))
			{				
				foreach($_FILES['photo'] as $media)
				{
					$photo = new photo(
						$media,
						$this->image_settings,
						MEDIA_DIR,
						CONTROLLER,
						$members_id,
						0,
						$media['name']
					);
				}
			}

			$this->members_model->edit($_POST, $members_id, $language_id);
			
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
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $members_id . '/' . $language_id . '/anchor_media');
				}
				else
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $members_id . '/' . $language_id);
				}
			}
		}
		
		if(isset($_POST['ajax']))
		{
			$filename = $_POST['filename'];
			
			$crop = new crop($filename, MEMBERS_DIR_CROP_REPLACE);
			$crop->resizeImage(MEMBERS_CROP_MAX_W, MEMBERS_CROP_MAX_H, $_POST['x'], $_POST['x2'], 'crop');
			$crop->saveImage($filename, 100);
		}
		
		$data = $this->members_model->fetch($members_id, $language_id);
		
		$data['members']['language'] = $this->languages_model->fetch($language_id);
		
		$data['languages'] = $this->languages_model->fetch_all();
		$data['drop_down'] = $this->members_model->fetch_drop_down(CONTROLLER);
		$data['count_children'] = $this->members_model->count_children($members_id);
		$data['members']['category_id'] = $this->members_model->fetch_all_category_selected($members_id, CONTROLLER);

		$this->load->view('members_add_edit', $data);
	}
	
	function delete()
	{
		$id = $this->url->segment(3);
		
		$this->members_model->delete($id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
		}
	}
	
	function delete_media()
	{
		$media_id 		= $this->url->segment(3);
		$page_id 		= $this->url->segment(4);
		$language_id 	= $this->url->segment(5);
		
		$this->members_model->delete_media($media_id);
		
		if(!$this->db->error)
		{	
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $page_id . '/' . $language_id . '/anchor_media');
		}
	}
	
	function order_media()
	{
		$direction 		= $this->url->segment(3);
		$table_id 		= $this->url->segment(4);
		$language_id 	= $this->url->segment(5);
		$current_order 	= $this->url->segment(6);
		
		$this->members_model->order_media($direction, $table_id, $language_id, $current_order);
		
		if(!$this->db->error)
		{
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/anchor_media');
		}
	}

}
?>