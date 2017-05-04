<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';
require_once SYS_PATH . 'core/twitter.php';

class products extends admin
{
  function __construct()
  {
    parent::__construct();
    
    $this->lang->load('products');

    $this->image_settings = array(
      array(
        'sub_folder' => 'thumb',
        'width' => PRODUCTS_IMG_THUMB_W,
        'height' => PRODUCTS_IMG_THUMB_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'normal',
        'width' => PRODUCTS_IMG_NORMAL_W,
        'height' => PRODUCTS_IMG_NORMAL_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'featured',
        'width' => PRODUCTS_IMG_FEATURED_W,
        'height' => PRODUCTS_IMG_FEATURED_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'max',
        'width' => PRODUCTS_IMG_MAX_W,
        'height' => PRODUCTS_IMG_MAX_H,
        'type' => 'resize'
      )
    );
  }
  
  function index()
  {
    $this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
    
    if($this->form->run())
    {
      $this->products_model->update_overview($_POST);
      
      if(!$this->db->error)
      {
        // $this->alert->clear();
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }
    
    $controller = CONTROLLER;
    //$data = $this->products_model->fetch_all($controller, 1);
    $data['products'] = $this->products_model->fetch_all_products();

    $data['out_of_stock'] = $this->products_model->fetch_all($controller, 0);
    $data['languages'] = $this->languages_model->fetch_all();
    $data['drop_down'] = $this->products_model->fetch_drop_down(CONTROLLER);
    $data['count_children'] = $this->products_model->count_children();

    $this->load->view('products_overview', $data);
  }
  
  function add()
  {
    (isset($_POST['product']['slug']) && $_POST['product']['slug'] == '' ? $_POST['product']['slug'] = $this->url->string_to_url($_POST['product']['title']) : '');
    
    $this->form->set_rules('product[title]', $this->lang->line('title'), 'required');
    
    if($this->form->run())
    {
      $product_id = $this->products_model->add($_POST);

      $_FILES['photo'] = rearrange_files($_FILES['photo']);
      $_FILES['photos'] = rearrange_files($_FILES['photos']);
      $_FILES['docs'] = rearrange_files($_FILES['docs']);
      
      if (!empty($_FILES['photo']))
      {
        foreach($_FILES['photo'] as $media)
        {
          $photo = new photo(
            $media,
            $this->image_settings,
            MEDIA_DIR,
            CONTROLLER,
            $product_id,
            0,
            $media['name']
          );
        }
      }
      
      if (!empty($_FILES['photos']))
      {
        foreach($_FILES['photos'] as $media)
        {
          $photo = new photo(
            $media,
            $this->image_settings,
            MEDIA_DIR,
            CONTROLLER,
            $product_id,
            1,
            $media['name']
          );
        }
      }

      if(!empty($_FILES['docs']))
      {
        foreach($_FILES['docs'] as $doc)
        {
          add_doc($doc);
          $this->admin_model->add_doc($doc, $product_id);
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
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $product_id . '/' . $this->config->item('default_language'));
        }
      }
    }
    $controller = CONTROLLER;
    $data['drop_down'] = $this->products_model->fetch_drop_down($controller);
    
    $data['count_children'] = $this->products_model->count_children();
    $data['product']['projects'] = $this->products_model->fetch_projects();
    $data['product']['contacts'] = $this->products_model->fetch_contacts();
    $data['product']['referenties'] = $this->products_model->fetch_referenties();
    if(isset($data['drop_down']) && count($data['drop_down']) > 0){
      $data['product']['products_options'] = $this->products_model->fetch_category_product_options($this->config->item('default_language'), 0, CONTROLLER);
      $data['product']['filters'] = $this->products_model->fetch_category_filters($this->config->item('default_language'), $controller, 0, CONTROLLER);
    }
    
    $data['categories'] = $this->products_model->product_categories();
    
    $this->load->view('products_add_edit',$data);
  }
  
  function edit()
  {

    $product_id = $this->url->segment(3);
    $language_id = $this->url->segment(4);
    
    $this->form->set_rules('product[title]', $this->lang->line('title'), 'required');
    
    if($this->url->segment(5) == 'delete'){
      $this->products_model->delete_price($this->url->segment(6));
      //$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $product_id . '/' . $language_id);
    }
    
    (isset($_POST['product']['slug']) && $_POST['product']['slug'] == '' ? $_POST['product']['slug'] = $this->url->string_to_url($_POST['product']['title']) : '');
    
    if($this->form->run())
    {
      $_FILES['photo'] = rearrange_files($_FILES['photo']);
      $_FILES['photos'] = rearrange_files($_FILES['photos']);
      $_FILES['docs'] = rearrange_files($_FILES['docs']);
      
      if (!empty($_FILES['photo']))
      {
        foreach($_FILES['photo'] as $media)
        {
          $photo = new photo(
            $media,
            $this->image_settings,
            MEDIA_DIR,
            CONTROLLER,
            $product_id,
            0,
            $media['name']
          );
        }
      }
      
      if (!empty($_FILES['photos']))
      {
        foreach($_FILES['photos'] as $media)
        {
          $photo = new photo(
            $media,
            $this->image_settings,
            MEDIA_DIR,
            CONTROLLER,
            $product_id,
            1,
            $media['name']
          );
        }
      }
      
      if(!empty($_FILES['docs']))
      {
        foreach($_FILES['docs'] as $doc)
        {
          add_doc($doc);
          $this->admin_model->add_doc($doc, $product_id);
        }
      }
      
      $this->products_model->edit($_POST, $product_id, $language_id);
      echo $this->db->error;
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
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $product_id . '/' . $language_id . '/anchor_media');
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $product_id . '/' . $language_id);
        }
      }
    }
    
    if(isset($_POST['ajax']))
    {
      $filename = $_POST['filename'];
      
      $crop = new crop($filename, PRODUCTS_DIR_CROP_REPLACE);
      $crop->resizeImage(PRODUCTS_CROP_MAX_W, PRODUCTS_CROP_MAX_H, $_POST['x'], $_POST['x2'], 'crop');
      $crop->saveImage($filename, 100);
    }
    
    $data = $this->products_model->fetch($product_id, $language_id);
    
    $data['product']['language'] = $this->languages_model->fetch($language_id);
    
    $controller = CONTROLLER;
    $data['languages'] = $this->languages_model->fetch_all();
    $data['drop_down'] = $this->products_model->fetch_drop_down($controller);
    $data['count_children'] = $this->products_model->count_children($product_id);
    $data['product']['products_options'] = $this->products_model->fetch_category_product_options($language_id, $product_id, CONTROLLER);
    $data['product']['filters'] = $this->products_model->fetch_category_filters($language_id, $controller, $product_id, CONTROLLER);
    //$data['product']['category_id'] = $this->members_model->fetch_all_category_selected($product_id, CONTROLLER);
    
    $data['categories'] = $this->products_model->product_categories();

    $this->load->view('products_add_edit', $data);
  }
  
  function delete()
  {
    $id = $this->url->segment(3);
    
    $this->products_model->delete($id);
    
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
    
    $this->products_model->delete_media($media_id);
    
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
    
    $this->products_model->order_media($direction, $table_id, $language_id, $current_order);
    
    if(!$this->db->error)
    {
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/anchor_media');
    }
  }
  
  function postTwitter()
  {
    $message = $_POST['twitter_post_text'] . ' ' . $_POST['twitter_post_link'];
    $db = checkSocialSettings();

    if(
    $db['setOAuthToken'] != '' &&
    $db['setOAuthTokenSecret'] != '' &&
    $db['setConsumerKey'] != '' &&
    $db['setConsumerSecret'] != ''
    )
    {
      if(str_replace(" ", "", $message) != '')
      {
        //$this->products_model->updateTwitter($message);
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
    $db = checkSocialSettings();
    
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
        //$this->products_model->updateFacebook($message);
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
  
  function order()
  {
    $direction 		= $this->url->segment(3);
    $current_order 	= $this->url->segment(4);
    $product_id 		= $this->url->segment(5);
  
    $this->products_model->order($direction, $current_order, $product_id);
    
    if(!$this->db->error)
    {
      // $this->alert->clear();
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
    }
  }
}
?>