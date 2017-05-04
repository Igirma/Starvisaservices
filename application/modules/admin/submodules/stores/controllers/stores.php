<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';
require_once SYS_PATH . 'core/twitter.php';


class stores extends admin
{
  function __construct()
  {
    parent::__construct();
    
    $this->lang->load('stores');

    $this->image_settings = array(
      array(
        'sub_folder' => 'thumb',
        'width' => STORES_IMG_THUMB_W,
        'height' => STORES_IMG_THUMB_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'normal',
        'width' => STORES_IMG_NORMAL_W,
        'height' => STORES_IMG_NORMAL_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'max',
        'width' => STORES_IMG_MAX_W,
        'height' => STORES_IMG_MAX_H,
        'type' => 'resize'
      )
    );
  }
  
  function index()
  {
    $this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
    
    if($this->form->run())
    {
      $this->stores_model->update_overview($_POST);
      
      if(!$this->db->error)
      {
        // $this->alert->clear();
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }
    
    $data['stores'] = $this->stores_model->fetch_all();
    $data['languages'] = $this->languages_model->fetch_all();	
    $this->load->view('stores_overview', $data);
  }
  
  function add()
  {
    (isset($_POST['store']['slug']) && $_POST['store']['slug'] == '' ? $_POST['store']['slug'] = $this->url->string_to_url($_POST['store']['title']) : '');
    
    $this->form->set_rules('store[title]', $this->lang->line('title'), 'required');
    
    //$this->form->set_rules('store[slug]', $this->lang->line('slug'), 'unique_url_add');
    
    if($this->form->run())
    {
      $store_id = $this->stores_model->add($_POST);

      $_FILES['photo'] = rearrange_files($_FILES['photo']);
      $_FILES['logo'] = rearrange_files($_FILES['logo']);
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
            $store_id,
            0,
            $media['name']
          );
        }
      }
      if(!empty($_FILES['logo']))
      {
        foreach($_FILES['logo'] as $media)
        {
          $home = new photo(
            $media,
            $this->image_settings,
            MEDIA_DIR,
            CONTROLLER,
            $store_id,
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
          $this->admin_model->add_doc($doc, $store_id);
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
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $store_id . '/' . $this->config->item('default_language'));
        }
      }
    }
    
    $this->load->view('stores_add_edit', $data);
  }
  
  function edit()
  {
    $this->form->set_rules('store[title]', $this->lang->line('title'), 'required');
    
    (isset($_POST['store']['slug']) && $_POST['store']['slug'] == '' ? $_POST['store']['slug'] = $this->url->string_to_url($_POST['store']['title']) : '');

    $store_id = $this->url->segment(3);
    $language_id = $this->url->segment(4);
    
    $this->form->set_rules('store[slug]', $this->lang->line('slug'), 'unique_url_edit[' . $store_id . ']');

    if($this->form->run())
    {
      $_FILES['photo'] = rearrange_files($_FILES['photo']);
      $_FILES['logo'] = rearrange_files($_FILES['logo']);
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
            $store_id,
            0,
            $media['name']
          );
        }
      }
      if(!empty($_FILES['logo']))
      {
        foreach($_FILES['logo'] as $media)
        {
          $home = new photo(
            $media,
            $this->image_settings,
            MEDIA_DIR,
            CONTROLLER,
            $store_id,
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
          $this->admin_model->add_doc($doc, $store_id);
        }
      }
      
      $this->stores_model->edit($_POST, $store_id, $language_id);
      
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
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $store_id . '/' . $language_id . '/anchor_media');
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $store_id . '/' . $language_id);
        }
      }
    }
    
    if(isset($_POST['ajax']))
    {
      $filename = $_POST['filename'];
      
      $crop = new crop($filename, STORES_DIR_CROP_REPLACE);
      $crop->resizeImage(STORES_CROP_MAX_W, STORES_CROP_MAX_H, $_POST['x'], $_POST['x2'], 'crop');
      $crop->saveImage($filename, 100);
    }
    
    $data['store'] = $this->stores_model->fetch($store_id, $language_id);
    
    $data['store']['language'] = $this->languages_model->fetch($language_id);
    
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('stores_add_edit', $data);
  }
  
  function delete()
  {
    $id = $this->url->segment(3);
    
    $this->stores_model->delete($id);
    
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
    
    $this->stores_model->delete_media($media_id);
    
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
    
    $this->stores_model->order_media($direction, $table_id, $language_id, $current_order);
    
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
        //$this->stores_model->updateTwitter($message);
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
        //$this->stores_model->updateFacebook($message);
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
    $store_id 		= $this->url->segment(5);
  
    $this->stores_model->order($direction, $current_order, $store_id);
    
    if(!$this->db->error)
    {
      // $this->alert->clear();
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
    }
  }
}
?>