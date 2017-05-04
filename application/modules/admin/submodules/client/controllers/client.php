<?php

require_once('application/modules/admin/controllers/admin.php');
require_once SYS_PATH . 'core/photo.php';

class client extends admin
{
  function __construct()
  {
    parent::__construct();
    
    $this->lang->load('client');

    $this->image_settings = array(
      array(
        'sub_folder' => 'thumb',
        'width' => CLIENTS_IMG_THUMB_W,
        'height' => CLIENTS_IMG_THUMB_H,
        'type' => 'crop'
      ),
      array(
        'sub_folder' => 'normal',
        'width' => CLIENTS_IMG_NORMAL_W,
        'height' => CLIENTS_IMG_NORMAL_H,
        'type' => 'resize'
      ),
      array(
        'sub_folder' => 'max',
        'width' => CLIENTS_IMG_MAX_W,
        'height' => CLIENTS_IMG_MAX_H,
        'type' => 'resize'
      )
    );
  }
  
  function index()
  {
    $this->form->set_rules('active[]', $this->lang->line('active'), 'numeric');
    
    if($this->form->run())
    {
      $this->client_model->update_overview($_POST);
      
      if(!$this->db->error)
      {
        // $this->alert->clear();
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }
    
    $data = $this->client_model->fetch_all();
    
    $data['languages'] = $this->languages_model->fetch_all();
    
    $this->load->view('client_overview', $data);
  }

  function add()
  {		
    $this->form->set_rules('clientname', $this->lang->line('clientname'), 'required');
    $this->form->set_rules('email', $this->lang->line('email'), 'required|valid_email');
    
    $this->form->set_rules('password', $this->lang->line('password'), 'required');
    $this->form->set_rules('password_check', $this->lang->line('password_check'), 'required|matches[password]');

    if($this->form->run())
    {
      $id = $this->client_model->add($_POST);
      $_FILES['photo'] = rearrange_files($_FILES['photo']);

      if (!empty($_FILES['photo']))
      {
        foreach($_FILES['photo'] as $media)
        {
          $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $id, 0, $media['name']);
        }
      }
      if(!$this->db->error)
      {
        // $this->alert->clear();
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }
    
    $this->load->view('client_add_edit');
  }
  
  function edit()
  {
    $client_id = $this->url->segment(3);
    $language_id = $this->url->segment(4);

    $this->form->set_rules('clientname', $this->lang->line('clientname'), 'required');
    $this->form->set_rules('email', $this->lang->line('email'), 'required|valid_email');

    if (isset($_POST) && !empty($_POST) && !empty($_POST['old_password']) && !empty($_POST['password']))
    {
      $this->form->set_rules('old_password', $this->lang->line('old_password'), 'required|matches_password[client_id]');
      $this->form->set_rules('password', $this->lang->line('password'), 'required');
      $this->form->set_rules('password_check', $this->lang->line('password_check'), 'required|matches[password]');
    }

    $data['client'] = $this->client_model->fetch($client_id);
    
    if($this->form->run())
    {
      $this->client_model->edit($_POST);
      
      $_FILES['photo'] = rearrange_files($_FILES['photo']);

      if(!empty($_FILES['photo']))
      {
        foreach($_FILES['photo'] as $media)
        {
          $photo = new photo($media, $this->image_settings, MEDIA_DIR, CONTROLLER, $client_id, 0, $media['name']);
        }
      }

      if(!$this->db->error && empty($this->form->_error_messages))
      {
        // $this->alert->clear();
        $this->alert->add($this->lang->line('success'), 'success');
        
        if(isset($_POST['save_and_back']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $client_id);
        }
      }
    }

    $this->load->view('client_add_edit', $data);
  }
  
  function delete()
  {
    $this->client_model->delete($this->url->segment(3));
    
    if(!$this->db->error)
    {
      // $this->alert->clear();
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
    }
  }
  
  function permission()
  {
    $client_id = $this->url->segment(3);
    $language_id = $this->url->segment(4);
    $permission = $this->url->segment(5);
    
    $this->client_model->set_permission($client_id, $language_id, $permission);

    if(!$this->db->error)
    {
      // $this->alert->clear();
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
    }
    
  }

}

?>