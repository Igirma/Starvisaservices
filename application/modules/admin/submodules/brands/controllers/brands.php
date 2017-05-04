<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';
require_once SYS_PATH . 'core/twitter.php';


class brands extends admin
{
  function __construct()
  {
    parent::__construct();
    
    $this->lang->load('brands');

    $this->image_settings = array(
      array(
        'sub_folder' => 'thumb',
        'width' => BRANDS_IMG_THUMB_W,
        'height' => BRANDS_IMG_THUMB_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'normal',
        'width' => BRANDS_IMG_NORMAL_W,
        'height' => BRANDS_IMG_NORMAL_H,
        'type' => 'resize'
      ),
      array(
        'sub_folder' => 'max',
        'width' => BRANDS_IMG_MAX_W,
        'height' => BRANDS_IMG_MAX_H,
        'type' => 'resize'
      )
    );
  }

  function index()
  {
    $this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
    
    if($this->form->run())
    {
      $this->brands_model->update_overview($_POST);
      
      if(!$this->db->error)
      {
        // $this->alert->clear();
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }
    
    $data['brands'] = $this->brands_model->fetch_all();
    $data['languages'] = $this->languages_model->fetch_all();	
    $this->load->view('brands_overview', $data);
  }
  
  function add()
  {
    (isset($_POST['brand']['slug']) && $_POST['brand']['slug'] == '' ? $_POST['brand']['slug'] = $this->url->string_to_url($_POST['brand']['title']) : '');
    
    $this->form->set_rules('brand[title]', $this->lang->line('title'), 'required');
    
    //$this->form->set_rules('brand[slug]', $this->lang->line('slug'), 'unique_url_add');
    
    if($this->form->run())
    {
      $brand_id = $this->brands_model->add($_POST);

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
            $brand_id,
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
            $brand_id,
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
          $this->admin_model->add_doc($doc, $brand_id);
        }
      }
        
      $this->brands_model->attach_filters($_POST['brand']['gender_id'], $brand_id, 'gender');
      $this->brands_model->attach_filters($_POST['brand']['store_id'], $brand_id, 'stores');

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
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $brand_id . '/' . $this->config->item('default_language'));
        }
      }
    }
    
    $data['stores'] = $this->stores_model->fetch_all();
    $this->load->view('brands_add_edit', $data);
  }
  
  function edit()
  {
    $this->form->set_rules('brand[title]', $this->lang->line('title'), 'required');
    
    (isset($_POST['brand']['slug']) && $_POST['brand']['slug'] == '' ? $_POST['brand']['slug'] = $this->url->string_to_url($_POST['brand']['title']) : '');

    $brand_id = $this->url->segment(3);
    $language_id = $this->url->segment(4);
    
    $this->form->set_rules('brand[slug]', $this->lang->line('slug'), 'unique_url_edit[' . $brand_id . ']');

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
            $brand_id,
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
            $brand_id,
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
          $this->admin_model->add_doc($doc, $brand_id);
        }
      }
      
      $this->brands_model->edit($_POST, $brand_id, $language_id);
      
      $this->brands_model->attach_filters($_POST['brand']['gender_id'], $brand_id, 'gender');
      $this->brands_model->attach_filters($_POST['brand']['store_id'], $brand_id, 'stores');

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
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $brand_id . '/' . $language_id . '/anchor_media');
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $brand_id . '/' . $language_id);
        }
      }
    }
    
    if(isset($_POST['ajax']))
    {
      $filename = $_POST['filename'];
      
      $crop = new crop($filename, BRANDS_DIR_CROP_REPLACE);
      $crop->resizeImage(BRANDS_CROP_MAX_W, BRANDS_CROP_MAX_H, $_POST['x'], $_POST['x2'], 'crop');
      $crop->saveImage($filename, 100);
    }
    
    $data['brand'] = $this->brands_model->fetch($brand_id, $language_id);
    
    $data['brand']['language'] = $this->languages_model->fetch($language_id);
    
    $data['languages'] = $this->languages_model->fetch_all();
    
    $data['stores'] = $this->stores_model->fetch_all();
    $data['brand']['store_id'] = $this->brands_model->fetch_brands_selected($brand_id, 'stores');
    $data['brand']['gender_id'] = $this->brands_model->fetch_brands_selected($brand_id, 'gender');

    $this->load->view('brands_add_edit', $data);
  }
  
  function delete()
  {
    $id = $this->url->segment(3);
    
    $this->brands_model->delete($id);
    
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
    
    $this->brands_model->delete_media($media_id);
    
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
    
    $this->brands_model->order_media($direction, $table_id, $language_id, $current_order);
    
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
        //$this->brands_model->updateTwitter($message);
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
        //$this->brands_model->updateFacebook($message);
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
    $brand_id 		= $this->url->segment(5);
  
    $this->brands_model->order($direction, $current_order, $brand_id);
    
    if(!$this->db->error)
    {
      // $this->alert->clear();
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
    }
  }
}
?>