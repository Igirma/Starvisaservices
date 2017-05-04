<?php 
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';

class blog extends admin
{
	var $image_settings = array();

	function __construct()
	{
		parent::__construct();
		$this->lang->load('blog');
		
		$this->image_settings = array(
			array(
				'sub_folder' => 'thumb',
				'width' => BLOG_IMG_THUMB_W,
				'height' => BLOG_IMG_THUMB_H,
				'type' => 'crop'
			),
			array(
				'sub_folder' => 'max',
				'width' => BLOG_IMG_MAX_W,
				'height' => BLOG_IMG_MAX_H,
				'type' => 'resize'
			)
		);
	}
	
	function index()
	{	
		$this->form->set_rules('active[]', $this->lang->line('active'), 'numeric');
		
		if($this->form->run())
		{
			$this->blog_model->update_overview($_POST);
			
			if(!$this->db->error)
			{
				// $this->alert->clear();
				$this->alert->add($this->lang->line('success'), 'success');
				$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . ($this->url->segment(2) == 'archive' ? '/' . $this->url->segment(2) : ''));
			}
		}
		
		$archive = 0;
		
		if($this->url->segment(2) == 'archive')
		{
			$archive = 1;
		}
		
		$data['blog'] 		= $this->blog_model->fetch_all($archive);
		$data['languages'] 	= $this->languages_model->fetch_all();
		
		$this->load->view('blog_overview', $data);
	}
	
	function add()
	{
		$this->form->set_rules('blog[title]', $this->lang->line('title'), 'required');
		$this->form->set_rules('blog[start_date]', $this->lang->line('start_date'), 'required|exact_length[10]');
		$this->form->set_rules('blog[end_date]', $this->lang->line('end_date'), 'required|exact_length[10]|blog_end_less_than_start_date');
		(isset($_POST['blog']['slug']) && $_POST['blog']['slug'] == '' ? $_POST['blog']['slug'] = $this->url->string_to_url($_POST['blog']['title']) : '');
		
		$this->form->set_rules('blog[slug]', $this->lang->line('slug'), 'unique_url_add');
		
		$this->form->set_message('start_date', $this->lang->line('start_date_length'));
		$this->form->set_message('blog_end_less_than_start_date', $this->lang->line('end_date_preceeds_start'));
		
		if($this->form->run())
		{
			$blog_id = $this->blog_model->add($_POST);
			
			$_FILES['photo'] = rearrange_files($_FILES['photo']);
			$_FILES['docs'] = rearrange_files($_FILES['docs']);
			
			if(!empty($_FILES['photo']))
			{				
				foreach($_FILES['photo'] as $media)
				{
					$photo = new photo(
						$media,
						$this->image_settings,
						MEDIA_DIR,
						CONTROLLER,
						$blog_id,
						0,
						$media['name']
					);
				}
			}
			
			if(!empty($_FILES['docs']))
			{				
				foreach($_FILES['docs'] as $doc)
				{
					add_doc($doc);
					$this->admin_model->add_doc($doc, $blog_id);
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
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $blog_id . '/' . $this->config->item('default_language'));
				}
			}
		}
		$data['blog']['filters'] = $this->blog_model->getFilters('blog', $this->config->item('default_language'));
		
		$this->load->view('blog_add_edit', $data);
	}
	
	function edit()
	{
		$blog_id 		= $this->url->segment(3);
		$language_id 	= $this->url->segment(4);
		
		if($this->url->segment(5) == 'delete'){
			$this->blog_model->delete_comment($this->url->segment(6));
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $blog_id . '/' . $language_id);
		}
		
		$this->form->set_rules('blog[title]', $this->lang->line('title'), 'required');
		$this->form->set_rules('blog[start_date]', $this->lang->line('start_date'), 'required|exact_length[10]');
		$this->form->set_rules('blog[end_date]', $this->lang->line('end_date'), 'required|exact_length[10]|blog_end_less_than_start_date');
		
		(isset($_POST['blog']['slug']) && $_POST['blog']['slug'] == '' ? $_POST['blog']['slug'] = $this->url->string_to_url($_POST['blog']['title']) : '');
		
		$this->form->set_rules('blog[slug]', $this->lang->line('slug'), 'unique_url_edit[' . $blog_id . ']');
		
		$this->form->set_message('start_date', $this->lang->line('start_date_length'));
		$this->form->set_message('blog_end_less_than_start_date', $this->lang->line('end_date_preceeds_start'));
		
		if($this->form->run())
		{
			$_FILES['photo'] = rearrange_files($_FILES['photo']);
			$_FILES['docs'] = rearrange_files($_FILES['docs']);
			
			if(!empty($_FILES['photo']))
			{				
				foreach($_FILES['photo'] as $media)
				{
					$photo = new photo(
						$media,
						$this->image_settings,
						MEDIA_DIR,
						CONTROLLER,
						$blog_id,
						0,
						$media['name']
					);
				}
			}
			
			if(!empty($_FILES['docs']))
			{				
				foreach($_FILES['docs'] as $doc)
				{
					add_doc($doc);
					$this->admin_model->add_doc($doc, $blog_id);
				}
			}
			
			$this->blog_model->edit($_POST, $blog_id, $language_id);
			
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
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $blog_id . '/' . $language_id . '/anchor_media');
				}
				else
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $blog_id . '/' . $language_id);
				}
			}
		}
		
		if(isset($_POST['ajax']))
		{
			$filename = $_POST['filename'];
			
			$crop = new crop($filename, blog_DIR_CROP_REPLACE);
			$crop->resizeImage(blog_CROP_MAX_W, blog_CROP_MAX_H, $_POST['x'], $_POST['x2'], 'crop');
			$crop->saveImage($filename, 100);
		}

		$data = $this->blog_model->fetch($blog_id, $language_id);
		
		$data['blog']['language'] = $this->languages_model->fetch($language_id);
		
		$data['languages'] = $this->languages_model->fetch_all();
		$data['blog']['filters'] = $this->blog_model->getFilters('blog', $language_id, $blog_id);
		$data['comments'] = $this->blog_model->fetch_all_comments($language_id, $blog_id);
		
		$this->load->view('blog_add_edit', $data);
	}
	
	function delete()
	{
		$id = $this->url->segment(3);
		
		$this->blog_model->delete($id);
		
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
		
		$this->blog_model->order_media($direction, $table_id, $language_id, $current_order);
		
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
		
		$this->blog_model->delete_media($media_id);
		
		if(!$this->db->error)
		{
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/anchor_media');
		}
	}
	
	function postTwitter()
	{
		$message = $_POST['twitter_post_text'] . ' ' . $_POST['twitter_post_link'];
		$db = getSettings();

		if(
			$db['setOAuthToken'] != '' &&
			$db['setOAuthTokenSecret'] != '' &&
			$db['setConsumerKey'] != '' &&
			$db['setConsumerSecret'] != ''
		)
		{
			if(str_replace(" ", "", $message) != '')
			{
				$this->blog_model->updateTwitter($message);
				// $this->alert->clear();
				$this->alert->add($this->lang->line('success'), 'success');
				$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
			}
		}
		else
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('no_social'), 'error');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
		}
	}
	
	function postFacebook()
	{		
		$message = $_POST['facebook_post_text'] . ' ' . $_POST['facebook_post_link'];
		$db = getSettings();
		
		if(
			$db['page_id'] != '' &&
			$db['appId'] != '' &&
			$db['secret'] != '' &&
			$db['url'] != '' &&
			$db['accestoken_db'] != ''
		)
		{
			if(str_replace(" ", "", $message) != '')
			{
				$this->blog_model->updateFacebook($message);
				// $this->alert->clear();
				$this->alert->add($this->lang->line('success'), 'success');
				$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
			}
		}
		else
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('no_social'), 'error');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
		}
	}
	
}

?>