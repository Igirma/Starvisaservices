<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';

class courses extends admin
{
  function __construct()
  {
    parent::__construct();
    
    $this->lang->load('courses');

    $this->image_settings = array(
      array(
        'sub_folder' => 'thumb',
        'width' => COURSES_IMG_THUMB_W,
        'height' => COURSES_IMG_THUMB_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'normal',
        'width' => COURSES_IMG_NORMAL_W,
        'height' => COURSES_IMG_NORMAL_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'profile',
        'width' => COURSES_IMG_ICON_W,
        'height' => COURSES_IMG_ICON_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'max',
        'width' => COURSES_IMG_MAX_W,
        'height' => COURSES_IMG_MAX_H,
        'type' => 'resize'
      )
    );
  }
  
  function index()
  {
    $this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
    
    if($this->form->run())
    {
      $this->courses_model->update_overview($_POST);
      
      if(!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }
    
    $data['courses'] = $this->courses_model->fetch_all();
    $data['languages'] = $this->languages_model->fetch_all();	
    $this->load->view('courses_overview', $data);
  }
  
  function add()
  {
    (isset($_POST['course']['slug']) && $_POST['course']['slug'] == '' ? $_POST['course']['slug'] = $this->url->string_to_url($_POST['course']['title']) : '');
    
    $this->form->set_rules('course[title]', $this->lang->line('title'), 'required');
    
    if($this->form->run())
    {
      $course_id = $this->courses_model->add($_POST);

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
            $course_id,
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
          $this->admin_model->add_doc($doc, $course_id);
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
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $course_id . '/' . $this->config->item('default_language'));
        }
      }
    }
    
    $this->load->view('courses_add_edit');
  }
  
  function edit()
  {
    $this->form->set_rules('course[title]', $this->lang->line('title'), 'required');
    
    (isset($_POST['course']['slug']) && $_POST['course']['slug'] == '' ? $_POST['course']['slug'] = $this->url->string_to_url($_POST['course']['title']) : '');

    $course_id = $this->url->segment(3);
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
            $course_id,
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
          $this->admin_model->add_doc($doc, $course_id);
        }
      }
      
      $this->courses_model->edit($_POST, $course_id, $language_id);

      if(!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
          
        if(isset($_POST['save_and_back']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
        elseif(isset($_POST['upload']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $course_id . '/' . $language_id . '/anchor_media');
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $course_id . '/' . $language_id);
        }
      }
    }
    
    if(isset($_POST['ajax']))
    {
      $filename = $_POST['filename'];
      
      $crop = new crop($filename, COURSES_DIR_CROP_REPLACE);
      $crop->resizeImage(COURSES_CROP_MAX_W, COURSES_CROP_MAX_h, $_POST['x'], $_POST['x2'], 'crop');
      $crop->saveImage($filename, 100);
    }
    
    $data['course'] = $this->courses_model->fetch($course_id, $language_id);

    $data['course']['language'] = $this->languages_model->fetch($language_id);
    
    $data['languages'] = $this->languages_model->fetch_all();
    
    $this->load->view('courses_add_edit', $data);
  }
  
  function delete()
  {
    $id = $this->url->segment(3);
    
    $this->courses_model->delete($id);
    
    if(!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
    }
  }
  
  function delete_media()
  {
    $media_id 		= $this->url->segment(3);
    $course_id 		= $this->url->segment(4);
    $language_id 	= $this->url->segment(5);
    
    $this->courses_model->delete_media($media_id);
    
    if(!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $course_id . '/' . $language_id . '/anchor_media');
    }
  }
  
  function order_media()
  {
    $direction 		= $this->url->segment(3);
    $table_id 		= $this->url->segment(4);
    $language_id 	= $this->url->segment(5);
    $current_order 	= $this->url->segment(6);
    
    $this->courses_model->order_media($direction, $table_id, $language_id, $current_order);
    
    if(!$this->db->error)
    {
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/anchor_media');
    }
  }
  
  function order()
  {
    $direction 		= $this->url->segment(3);
    $current_order 	= $this->url->segment(4);
    $course_id 		= $this->url->segment(5);
  
    $this->courses_model->order($direction, $current_order, $course_id);
    
    if(!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
    }
  }
}
?>