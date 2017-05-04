<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';

class photoalbums extends admin
{
	var $image_settings = array();

	function __construct()
	{
		parent::__construct();
		
		$this->lang->load('photoalbum');
		
		$this->image_settings = array(
			array(
				'sub_folder' => 'thumb',
				'width' => PHOTOALBUMS_IMG_THUMB_W,
				'height' => PHOTOALBUMS_IMG_THUMB_H,
				'type' => 'crop'
			),
			array(
				'sub_folder' => 'normal',
				'width' => PHOTOALBUMS_IMG_NORMAL_W,
				'height' => PHOTOALBUMS_IMG_NORMAL_H,
				'type' => 'crop'
			),
			array(
				'sub_folder' => 'max',
				'width' => PHOTOALBUMS_IMG_MAX_W,
				'height' => PHOTOALBUMS_IMG_MAX_H,
				'type' => 'resize'
			)
		);
	}
	
	function index()
	{
		$this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
		
		if($this->form->run())
		{
			$this->photoalbums_model->update_overview($_POST);
			
			if(!$this->db->error)
			{
				// $this->alert->clear();
				$this->alert->add($this->lang->line('success'), 'success');
				$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
			}
		}
	
		$data['photoalbums'] = $this->photoalbums_model->fetch_all();
		$data['languages'] = $this->languages_model->fetch_all();
		$this->load->view('photoalbums_overview', $data);
	}	
	
	function add()
	{
		$this->form->set_rules('photoalbums[title]', $this->lang->line('title'), 'required');		
		(isset($_POST['photoalbums']['slug']) && $_POST['photoalbums']['slug'] == '' ? $_POST['photoalbums']['slug'] = $this->url->string_to_url($_POST['photoalbums']['title']) : '');
		
		$this->form->set_rules('photoalbums[slug]', $this->lang->line('slug'), 'unique_url_add');
		
		if($this->form->run())
		{
			$album_id = $this->photoalbums_model->add($_POST);
			
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
						0,
						$album_id,
						$media['name'],
						0
					);
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
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $album_id . '/' . $this->config->item('default_language'));
				}
			}
		}
	
		$this->load->view('photoalbums_add_edit');
	}
	
	function edit()
	{
		$album_id = $this->url->segment(3);
		$language_id = $this->url->segment(4);

		$this->form->set_rules('photoalbums[title]', $this->lang->line('title'), 'required');
		$this->form->set_rules('photoalbums[slug]', $this->lang->line('slug'), 'unique_url_edit[' . $album_id . ']');
		
		(isset($_POST['photoalbums']['slug']) && $_POST['photoalbums']['slug'] == '' ? $_POST['photoalbums']['slug'] = $this->url->string_to_url($_POST['photoalbums']['title']) : '');
		
		if($this->form->run())
		{
			$id = $this->photoalbums_model->edit($_POST, $language_id);
			
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
						0,
						$album_id,
						$media['name'],
						0
					);
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
				elseif(isset($_POST['upload']))
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $album_id . '/' . $language_id . '/anchor_media');
				}
				else
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $album_id . '/' . $language_id);
				}
			}
		}
		
		if(isset($_POST['ajax']))
		{
			$filename = $_POST['filename'];
			
			$crop = new crop($filename, PHOTOALBUMS_DIR_CROP_REPLACE);
			$crop->resizeImage(PHOTOALBUMS_CROP_MAX_W, PHOTOALBUMS_CROP_MAX_H, $_POST['x'], $_POST['x2'], 'crop');
			$crop->saveImage($filename, 100);
		}
		
		$data = $this->photoalbums_model->fetch($album_id, $language_id);
		
		$data['photoalbums']['language'] = $this->languages_model->fetch($language_id);
		
		$data['languages'] = $this->languages_model->fetch_all();
		
		$this->load->view('photoalbums_add_edit', $data);
	}
	
	function delete()
	{
		$this->photoalbums_model->delete($this->url->segment(3));
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
		}
	}
	
	function delete_media()
	{
		$media_id = $this->url->segment(3);
		$album_id = $this->url->segment(4);
		$language_id = $this->url->segment(5); 
	
		$this->photoalbums_model->delete_media($media_id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $album_id . '/' . $language_id . '/anchor_media');
		}
	}
	
	function order()
	{
		$direction 		= $this->url->segment(3);
		$current_order 	= $this->url->segment(4);
		$album_id 		= $this->url->segment(5);
	
		$this->photoalbums_model->order($direction, $current_order, $album_id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
		}
	}
	
	function order_media()
	{
		$direction 		= $this->url->segment(3);
		$album_id 		= $this->url->segment(4); 
		$language_id 	= $this->url->segment(5);
		$current_order 	= $this->url->segment(6);
		
		$this->photoalbums_model->order_media($direction, $album_id, $current_order);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $album_id . '/' . $language_id . '/anchor_media');
		}
	}

}
?>