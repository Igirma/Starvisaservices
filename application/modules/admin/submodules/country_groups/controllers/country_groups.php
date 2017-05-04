<?php

require_once APP_PATH . 'modules/admin/controllers/admin.php';

class country_groups extends admin
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
      $this->country_groups_model->update_overview($_POST);
      if (!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }
    $data['groups'] = $this->country_groups_model->fetch_all();
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('country_groups_overview', $data);
  }
  
  function add()
  {
    
    $this->form->set_rules('group[user_country_group_name]', 'Group name', 'required');
    
    if ($this->form->run())
    {
      $id = $this->country_groups_model->add($_POST['group']);
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

    $data['countries'] = $this->country_groups_model->fetch_active_countries();

    $this->load->view('country_groups_add_edit', $data);
  }

  function edit()
  {
    $this->form->set_rules('group[user_country_group_name]', 'Group name', 'required');
    
    $id = $this->url->segment(3);
    $language_id = $this->url->segment(4);
    
    if ($this->form->run())
    {
      $this->country_groups_model->edit($_POST['group'], $id);
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

    $data['group'] = $this->country_groups_model->fetch($id);
    $data['countries'] = $this->country_groups_model->fetch_active_countries();
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('country_groups_add_edit', $data);
  }

  function order()
  {
    $direction = $this->url->segment(3);
    $order = $this->url->segment(4);
    $id = $this->url->segment(5);
  
    $this->country_groups_model->order($direction, $order, $id);
    
    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
    }
  }

  function delete()
  {
    $id = $this->url->segment(3);
    $this->country_groups_model->delete($id);

    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
    }
  }

}

?>