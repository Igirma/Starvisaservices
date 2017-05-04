<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';
require_once SYS_PATH . 'core/twitter.php';


class videos extends admin
{
  function __construct()
  {
    parent::__construct();
    
    $this->lang->load('videos');

    $this->image_settings = array(
      array(
        'sub_folder' => 'thumb',
        'width' => VIDEOS_IMG_THUMB_W,
        'height' => VIDEOS_IMG_THUMB_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'normal',
        'width' => VIDEOS_IMG_NORMAL_W,
        'height' => VIDEOS_IMG_NORMAL_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'max',
        'width' => VIDEOS_IMG_MAX_W,
        'height' => VIDEOS_IMG_MAX_H,
        'type' => 'resize'
      )
    );
  }
  
  function index()
  {
    $this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
    
    if($this->form->run())
    {
      $this->videos_model->update_overview($_POST);
      
      if(!$this->db->error)
      {
        // $this->alert->clear();
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }
    
    $data['videos'] = $this->videos_model->fetch_all();
    $data['languages'] = $this->languages_model->fetch_all();	
    $this->load->view('videos_overview', $data);
  }
  
  function add()
  {
    (isset($_POST['video']['slug']) && $_POST['video']['slug'] == '' ? $_POST['video']['slug'] = $this->url->string_to_url($_POST['video']['title']) : '');
    
    $this->form->set_rules('video[title]', $this->lang->line('title'), 'required');
    
    if($this->form->run())
    {
      $brand_id = $this->videos_model->add($_POST);

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
            $brand_id,
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
          $this->admin_model->add_doc($doc, $brand_id);
        }
      }

      if(!$this->db->error)
      {
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
    
    $this->load->view('videos_add_edit');
  }
  
  function edit()
  {
    $this->form->set_rules('video[title]', $this->lang->line('title'), 'required');
    
    (isset($_POST['video']['slug']) && $_POST['video']['slug'] == '' ? $_POST['video']['slug'] = $this->url->string_to_url($_POST['video']['title']) : '');

    $video_id = $this->url->segment(3);
    $language_id = $this->url->segment(4);
    
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
            $video_id,
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
          $this->admin_model->add_doc($doc, $video_id);
        }
      }
      
      $this->videos_model->edit($_POST, $video_id, $language_id);

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
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $video_id . '/' . $language_id . '/anchor_media');
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $video_id . '/' . $language_id);
        }
      }
    }
    
    if(isset($_POST['ajax']))
    {
      $filename = $_POST['filename'];
      
      $crop = new crop($filename, VIDEOS_DIR_CROP_REPLACE);
      $crop->resizeImage(VIDEOS_CROP_MAX_W, VIDEOS_CROP_MAX_h, $_POST['x'], $_POST['x2'], 'crop');
      $crop->saveImage($filename, 100);
    }
    
    $data['video'] = $this->videos_model->fetch($video_id, $language_id);

    $data['video']['language'] = $this->languages_model->fetch($language_id);
    
    $data['languages'] = $this->languages_model->fetch_all();
    
    $this->load->view('videos_add_edit', $data);
  }
  
  function delete()
  {
    $id = $this->url->segment(3);
    
    $this->videos_model->delete($id);
    
    if(!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
    }
  }
  
  function delete_media()
  {
    $media_id 		= $this->url->segment(3);
    $video_id 		= $this->url->segment(4);
    $language_id 	= $this->url->segment(5);
    
    $this->videos_model->delete_media($media_id);
    
    if(!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $video_id . '/' . $language_id . '/anchor_media');
    }
  }
  
  function order_media()
  {
    $direction 		= $this->url->segment(3);
    $table_id 		= $this->url->segment(4);
    $language_id 	= $this->url->segment(5);
    $current_order 	= $this->url->segment(6);
    
    $this->videos_model->order_media($direction, $table_id, $language_id, $current_order);
    
    if(!$this->db->error)
    {
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/anchor_media');
    }
  }
  
  function order()
  {
    $direction 		= $this->url->segment(3);
    $current_order 	= $this->url->segment(4);
    $video_id 		= $this->url->segment(5);
  
    $this->videos_model->order($direction, $current_order, $video_id);
    
    if(!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
    }
  }
}
?>