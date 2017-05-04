<?php

require_once APP_PATH . 'modules/admin/controllers/admin.php';

class entries extends admin
{
  function __construct()
  {
    parent::__construct();
    $this->lang->load('country');
  }

  function index()
  {
    $this->form->set_rules('active[]', 'Show online?', 'numeric');
    
    if($this->form->run())
    {
      if (!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }
    $data['entries'] = $this->entries_model->fetch_all();
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('entries_overview', $data);
  }
  
  function add()
  {
    $this->form->set_rules('entry[user_entry_name]', 'Entry name', 'required');
    
    if ($this->form->run())
    {
      $id = $this->entries_model->add($_POST['entry']);
      if (!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
        if (isset($_POST['save_and_back']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id . '/' . $this->config->item('default_language'));
        }
      }
    }

    //$data['types'] = $this->entries_model->fetch_types();
    //$data['services'] = $this->entries_model->fetch_services();
    $data = array();

    $this->load->view('entries_add_edit', $data);
  }

  function edit()
  {
    $this->form->set_rules('entry[user_entry_name]', 'Entry name', 'required');
    
    $id = $this->url->segment(3);
    $language_id = $this->url->segment(4);
    
    if ($this->form->run())
    {
      $this->entries_model->edit($_POST['entry'], $id);
      if (!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
          
        if (isset($_POST['save_and_back']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $id . '/' . $language_id);
        }
      }
    }

    $data['entry'] = $this->entries_model->fetch($id);
    //$data['types'] = $this->entries_model->fetch_types();
    //$data['services'] = $this->entries_model->fetch_services();
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('entries_add_edit', $data);
  }

  function order()
  {
    $direction = $this->url->segment(3);
    $order = $this->url->segment(4);
    $id = $this->url->segment(5);
  
    $this->entries_model->order($direction, $order, $id);
    
    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
    }
  }

  function delete()
  {
    $id = $this->url->segment(3);
    $this->entries_model->delete($id);

    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
    }
  }

}

?>