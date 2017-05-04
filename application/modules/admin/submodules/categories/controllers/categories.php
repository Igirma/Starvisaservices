<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';

class categories extends admin
{
	function __construct()
	{
		parent::__construct();
		
		$this->lang->load('categories');
		
		$this->image_settings = array(
			array(
				'sub_folder' => 'thumb',
				'width' => CATEGORIES_IMG_THUMB_W,
				'height' => CATEGORIES_IMG_THUMB_H,
				'type' => 'crop'
			),
			array(
				'sub_folder' => 'page',
				'width' => CATEGORIES_IMG_PAGE_W,
				'height' => CATEGORIES_IMG_PAGE_H,
				'type' => 'resize'
			),
			array(
				'sub_folder' => 'max',
				'width' => CATEGORIES_IMG_MAX_W,
				'height' => CATEGORIES_IMG_MAX_H,
				'type' => 'resize'
			)
		);
	}
	
	function index()
	{
		$subcontroller = $this->url->segment(2);
		
		$this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
		$this->form->set_rules('main_menu[]', 'In menu tonen?', 'numeric');
		
		if($this->form->run())
		{
			$this->categories_model->update_overview($_POST);
			
			if(!$this->db->error)
			{
				$this->alert->clear();
				$this->alert->add($this->lang->line('success'), 'success');
				$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/'. $subcontroller);
			}
		}
		$data['category'] = $this->categories_model->fetch_all($subcontroller);
		
		$data['product_options'] = $this->categories_model->fetch_all_product_options($this->config->item('default_language'));
		$data['languages'] = $this->languages_model->fetch_all();
		$data['default_language'] = $this->config->item('default_language');
		
		$this->load->view('categories_overview', $data);
	}
	
	function add()
	{
		$subcontroller = $this->url->segment(3);
	
		$this->form->set_rules('category[title]', $this->lang->line('content_title'), 'required');
		
		(isset($_POST['category']['slug']) && $_POST['category']['slug'] == '' ? $_POST['category']['slug'] = $this->url->string_to_url($_POST['category']['title']) : '');
		
		//$this->form->set_rules('category[slug]', $this->lang->line('slug'), 'unique_url_add');
		
		if($this->form->run())
		{
			$category_id = $this->categories_model->add($_POST, $subcontroller);
			
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
						$category_id,
						0,
						$media['name']
					);
				}
			}
			
			if(!$this->db->error)
			{
				if(isset($_POST['save_and_back']))
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $subcontroller);
				}
				else
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $subcontroller . '/' . $category_id . '/' . $this->config->item('default_language'));
				}
			}
		}
		
		if($subcontroller == 'products')
		{
			//echo 'products';
		}
		
		$data['languages'] = $this->admin_model->fetch_languages();
		$data['drop_down'] = $this->categories_model->fetch_drop_down($subcontroller);
		$data['category']['products_options'] = $this->categories_model->fetch_category_product_options();
		$data['category']['filters'] = $this->categories_model->fetch_category_filters($subcontroller, $this->config->item('default_language'));
		$data['category']['products'] = $this->categories_model->fetch_products_categories($subcontroller, $this->config->item('default_language'));
		
		$this->load->view('categories_add_edit', $data);
	}
	
	function edit()
	{
		$subcontroller = $this->url->segment(3);
		$category_id = $this->url->segment(4);
		$language_id = $this->url->segment(5);
		
		$this->form->set_rules('category[title]', $this->lang->line('title'), 'required');
		$this->form->set_message('category_date', $this->lang->line('category_date_length'));
		
		(isset($_POST['category']['slug']) && $_POST['category']['slug'] == '' ? $_POST['category']['slug'] = $this->url->string_to_url($_POST['category']['title']) : '');
		
		//$this->form->set_rules('category[slug]', $this->lang->line('slug'), 'unique_url_edit[' . $category_id . ']');
		
		//$this->form->set_rules('category[slug]', $this->lang->line('slug'), 'unique_url_edit[' . $category_id . ']');

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
						$category_id,
						0,
						$media['name']
					);
				}
			}
			
			$this->categories_model->edit($_POST, $category_id, $language_id, $subcontroller);
			
			if(!$this->db->error)
			{
				$this->alert->add($this->lang->line('success'), 'success');
					
				if(isset($_POST['save_and_back']))
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $subcontroller);
				}
				elseif(isset($_POST['upload']))
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $subcontroller . '/' . $category_id . '/' . $language_id . '/anchor_media');
				}
				else
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $subcontroller . '/' . $category_id . '/' . $language_id);
				}
			}
		}
		
		if(isset($_POST['ajax']))
		{
			$filename = $_POST['filename'];
			
			$crop = new crop($filename, CATEGORIES_DIR_CROP_REPLACE);
			$crop->resizeImage(CATEGORIES_CROP_MAX_W, CATEGORIES_CROP_MAX_H, $_POST['x'], $_POST['x2'], 'crop');
			$crop->saveImage($filename, 100);
		}
		
		$data = $this->categories_model->fetch($category_id, $language_id);
		
		$data['category']['language'] = $this->languages_model->fetch($language_id);
		
		$data['languages'] = $this->languages_model->fetch_all();
		
		$subcontroller = $this->url->segment(3);
		$data['drop_down'] = $this->categories_model->fetch_drop_down($subcontroller);
		$data['category']['products_options'] = $this->categories_model->fetch_category_product_options($category_id, $language_id);
		$data['category']['filters'] = $this->categories_model->fetch_category_filters($subcontroller, $language_id, $category_id);
		
		$this->load->view('categories_add_edit', $data);
	}
	
	function delete()
	{
		$subcontroller 	= $this->url->segment(2);
		$category_id 	= $this->url->segment(4);
		
		$this->categories_model->delete($category_id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $subcontroller);
		}
	}
	
	function delete_media()
	{
		$subcontroller 	= $this->url->segment(4);
		$media_id 		= $this->url->segment(5);
		$category_id 	= $this->url->segment(6);
		$language_id 	= $this->url->segment(7);
		
		$this->categories_model->delete_media($media_id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' .  $subcontroller . '/' .$category_id . '/' . $language_id . '/anchor_media');
		}
	}
	
	function order_media()
	{
		$subcontroller	= $this->url->segment(3);
		$direction 		= $this->url->segment(4);
		$table_id 		= $this->url->segment(5);
		$language_id 	= $this->url->segment(6);
		$current_order 	= $this->url->segment(7);
		
		$this->projects_model->order_media($direction, $table_id, $language_id, $current_order);
		
		if(!$this->db->error)
		{
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER .  '/edit/' . $subcontroller . '/' . $table_id . '/' . $language_id . '/anchor_media');
		}
	}
	
	function order()
	{
		
		$subcontroller 	= $this->url->segment(3);
		$direction 		= $this->url->segment(4);
		$current_order 	= $this->url->segment(5);
		$parent_id 		= $this->url->segment(6);
		$category_id 	= $this->url->segment(7);
		
		$this->categories_model->order($subcontroller, $direction, $current_order, $parent_id, $category_id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $subcontroller . '/success');
		}
	}
	
	function add_product_options()
	{
		$subcontroller = $this->url->segment(3);
	
		$this->form->set_rules('product_options[title]', $this->lang->line('product_options_title'), 'required');
		
		if($this->form->run())
		{
			$category_id = $this->categories_model->add_product_options($_POST);			
			
			if(!$this->db->error)
			{
				if(isset($_POST['save_and_back']))
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $subcontroller);
				}
				else
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit_product_options/' . $subcontroller . '/' . $category_id . '/' . $this->config->item('default_language'));
				}
			}
		}
		
		$data['languages'] = $this->admin_model->fetch_languages();
		$data['drop_down'] = $this->categories_model->fetch_drop_down($subcontroller);
		$this->load->view('product_options_add_edit', $data);
	}
	
	function edit_product_options()
	{
		$product_options	 = $this->url->segment(3);
		$product_options_id = $this->url->segment(4);
		$language_id = $this->url->segment(5);
		
		$this->form->set_rules('product_options[title]', $this->lang->line('title'), 'required');
		
		if($this->form->run())
		{
			
			$this->categories_model->edit_product_options($_POST, $product_options_id, $language_id);
			
			if(!$this->db->error)
			{
				$this->alert->add($this->lang->line('success'), 'success');
					
				if(isset($_POST['save_and_back']))
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $product_options);
				}
				elseif(isset($_POST['upload']))
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit_product_options/' . $product_options . '/' . $product_options_id . '/' . $language_id . '/anchor_media');
				}
				else
				{
					$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit_product_options/' . $product_options . '/' . $product_options_id . '/' . $language_id);
				}
			}
		}
		
		$data = $this->categories_model->fetch_product_options($product_options_id, $language_id);
		
		$data['product_options']['language'] = $this->languages_model->fetch($language_id);
		
		$data['languages'] = $this->languages_model->fetch_all();
		
		$subcontroller = $this->url->segment(3);
		$data['drop_down'] = $this->categories_model->fetch_drop_down($subcontroller);
		
		$this->load->view('product_options_add_edit', $data);
	}
	
	function delete_product_options()
	{
		$subcontroller 	= $this->url->segment(2);
		$product_options_id 	= $this->url->segment(4);
		
		$this->categories_model->delete_product_options($product_options_id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $subcontroller);
		}
	}
	
	function order_product_options()
	{
		
		$subcontroller 	= $this->url->segment(3);
		$direction 		= $this->url->segment(4);
		$current_order 	= $this->url->segment(5);
		$product_options_id 		= $this->url->segment(6);
	
		$this->categories_model->order_product_options($direction, $current_order, $product_options_id);
		
		if(!$this->db->error)
		{
			// $this->alert->clear();
			$this->alert->add($this->lang->line('success'), 'success');
			$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/' . $subcontroller . '/success');
		}
	}
	
}
?>