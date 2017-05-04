<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';

class events extends admin
{
  function __construct()
  {
    parent::__construct();
    
    $this->lang->load('events');

  }
  
  function index()
  {
    $this->form->set_rules('active[]', 'Online plaatsen?', 'numeric');
    
    if($this->form->run())
    {
      $this->events_model->update_overview($_POST);
      
      if(!$this->db->error)
      {
        // $this->alert->clear();
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }
    
    $data = $this->events_model->fetch_all();
    $data['languages'] = $this->languages_model->fetch_all();	
    $this->load->view('events_overview', $data);
  }
  
  function add()
  {
    (isset($_POST['events']['slug']) && $_POST['events']['slug'] == '' ? $_POST['events']['slug'] = $this->url->string_to_url($_POST['events']['title']) : '');
    
    $this->form->set_rules('events[title]', $this->lang->line('title'), 'required');
    $this->form->set_message('date', $this->lang->line('date_length'));
    
    if($this->form->run())
    {
      $events_id = $this->events_model->add($_POST);

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
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $events_id . '/' . $this->config->item('default_language'));
        }
      }
    }
    
    $this->load->view('events_add_edit');
  }
  
  function edit()
  {
    (isset($_POST['events']['slug']) && $_POST['events']['slug'] == '' ? $_POST['events']['slug'] = $this->url->string_to_url($_POST['events']['title']) : '');
  
    $this->form->set_rules('events[title]', $this->lang->line('title'), 'required');
    $this->form->set_message('date', $this->lang->line('date_length'));
    
    $events_id = $this->url->segment(3);
    $language_id = $this->url->segment(4);
    
    
    if($this->form->run())
    {

      $this->events_model->edit($_POST, $events_id, $language_id);
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
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $events_id . '/' . $language_id . '/anchor_media');
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $events_id . '/' . $language_id);
        }
      }
    }
    
    $data = $this->events_model->fetch($events_id, $language_id);
    
    $data['events']['language'] = $this->languages_model->fetch($language_id);
    
    $data['languages'] = $this->languages_model->fetch_all();
    
    $this->load->view('events_add_edit', $data);
  }
  
  function delete()
  {
    $id = $this->url->segment(3);
    
    $this->events_model->delete($id);
    
    if(!$this->db->error)
    {
      // $this->alert->clear();
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
    }
  }

}
?>