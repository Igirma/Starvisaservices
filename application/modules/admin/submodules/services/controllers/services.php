<?php

require_once APP_PATH . 'modules/admin/controllers/admin.php';

class services extends admin
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
    $data['services'] = $this->services_model->fetch_all();
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('services_overview', $data);
  }
  
  function add()
  {
    $this->form->set_rules('service[users_services_name]', 'Name', 'required');
    
    if ($this->form->run())
    {
      $id = $this->services_model->add($_POST['service']);
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

    $this->load->view('services_add_edit');
  }

  function edit()
  {
    $this->form->set_rules('service[users_services_name]', 'Name', 'required');
    
    $id = $this->url->segment(3);
    $language_id = $this->url->segment(4);
    
    if ($this->form->run())
    {
      $this->services_model->edit($_POST['service'], $id);
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

    $data['service'] = $this->services_model->fetch($id);
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('services_add_edit', $data);
  }

  function order()
  {
    $direction = $this->url->segment(3);
    $order = $this->url->segment(4);
    $id = $this->url->segment(5);
  
    $this->services_model->order($direction, $order, $id);
    
    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
    }
  }

  function delete()
  {
    $id = $this->url->segment(3);
    $this->services_model->delete($id);

    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
    }
  }

}

?>