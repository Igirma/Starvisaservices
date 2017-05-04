<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';
require_once SYS_PATH . 'core/photo.php';

class references extends admin
{
  function __construct()
  {
    parent::__construct();
    
    $this->lang->load('references');

    $this->image_settings = array(
      array(
        'sub_folder' => 'thumb',
        'width' => REFERENCES_IMG_THUMB_W,
        'height' => REFERENCES_IMG_THUMB_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'normal',
        'width' => 200,
        'height' => 200,
        'type' => 'crop'
      )
    );
  }
  
  function index()
  {
    $this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
    
    if($this->form->run())
    {
      $this->references_model->update_overview($_POST);
      
      if(!$this->db->error)
      {
        // $this->alert->clear();
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }
    
    $data = $this->references_model->fetch_all();
    $data['languages'] = $this->languages_model->fetch_all();
    $this->load->view('references_overview', $data);
  }
  
  function add()
  {
    (isset($_POST['reference']['slug']) && $_POST['reference']['slug'] == '' ? $_POST['reference']['slug'] = $this->url->string_to_url($_POST['reference']['title']) : '');
    
    $this->form->set_rules('reference[title]', $this->lang->line('title'), 'required');
    $this->form->set_message('reference_date', $this->lang->line('reference_date_length'));
    
    //$this->form->set_rules('reference[slug]', $this->lang->line('slug'), 'unique_url_add');
    
    if($this->form->run())
    {
      $reference_id = $this->references_model->add($_POST);

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
            $reference_id,
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
          $this->admin_model->add_doc($doc, $reference_id);
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
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $reference_id . '/' . $this->config->item('default_language'));
        }
      }
    }
    
    $data['blog_data'] = $this->references_model->fetch_blog();
    
    $this->load->view('references_add_edit', $data);
  }
  
  function edit()
  {
    $this->form->set_rules('reference[title]', $this->lang->line('title'), 'required');
    $this->form->set_message('reference_date', $this->lang->line('reference_date_length'));
    
    (isset($_POST['reference']['slug']) && $_POST['reference']['slug'] == '' ? $_POST['reference']['slug'] = $this->url->string_to_url($_POST['reference']['title']) : '');

    $reference_id = $this->url->segment(3);
    $language_id = $this->url->segment(4);
    
    //$this->form->set_rules('reference[slug]', $this->lang->line('slug'), 'unique_url_edit[' . $reference_id . ']');

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
            $reference_id,
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
          $this->admin_model->add_doc($doc, $reference_id);
        }
      }
      
      $this->references_model->edit($_POST, $reference_id, $language_id);
      
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
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $reference_id . '/' . $language_id . '/anchor_media');
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $reference_id . '/' . $language_id);
        }
      }
    }
    
    if(isset($_POST['ajax']))
    {
      $filename = $_POST['filename'];
      
      $crop = new crop($filename, REFERENCES_DIR_CROP_REPLACE);
      $crop->resizeImage(REFERENCES_CROP_MAX_W, REFERENCES_CROP_MAX_H, $_POST['x'], $_POST['x2'], 'crop');
      $crop->saveImage($filename, 100);
    }
    
    $data = $this->references_model->fetch($reference_id, $language_id);
    
    $data['reference']['language'] = $this->languages_model->fetch($language_id);
    
    $data['languages'] = $this->languages_model->fetch_all();
    $data['blog_data'] = $this->references_model->fetch_blog();
    
    $this->load->view('references_add_edit', $data);
  }
  
  function delete()
  {
    $id = $this->url->segment(3);
    
    $this->references_model->delete($id);
    
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
    
    $this->references_model->delete_media($media_id);
    
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
    
    $this->references_model->order_media($direction, $table_id, $language_id, $current_order);
    
    if(!$this->db->error)
    {
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $table_id . '/' . $language_id . '/anchor_media');
    }
  }
    
  function order()
  {
    $direction 		= $this->url->segment(3);
    $current_order 	= $this->url->segment(4);
    $reference_id 		= $this->url->segment(5);
  
    $this->references_model->order($direction, $current_order, $reference_id);
    
    if(!$this->db->error)
    {
      // $this->alert->clear();
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
    }
  }
}
?>