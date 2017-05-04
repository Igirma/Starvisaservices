<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';

class country extends admin
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
      $this->country_model->update_overview($_POST);
      
      if(!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }
    //$this->country_model->resetCountries();
    
    $data['countries'] = $this->country_model->fetch_all();
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('country_overview', $data);
  }
  
  function add()
  {
    
    $this->form->set_rules('country[users_country_name]', 'Country name', 'required');
    $this->form->set_rules('country[users_country_code]', 'Country code', 'required');
    
    if ($this->form->run())
    {
      $users_country_id = $this->country_model->add($_POST['country']);

      if(!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
        if(isset($_POST['save_and_back']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $users_country_id . '/' . $this->config->item('default_language'));
        }
      }
    }
    
    $data['groups'] = $this->country_model->fetch_active_groups();
    $data['nationalities'] = $this->country_model->fetch_active_nationalities_groups();
    
    $this->load->view('country_add_edit', $data);
  }
  
  function edit()
  {
    $this->form->set_rules('country[users_country_name]', 'Country name', 'required');
    $this->form->set_rules('country[users_country_code]', 'Country code', 'required');
    
    $users_country_id = $this->url->segment(3);
    $language_id = $this->url->segment(4);
    
    if($this->form->run())
    {
      $this->country_model->edit($_POST['country'], $users_country_id);

      if(!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
          
        if (isset($_POST['save_and_back']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $users_country_id . '/' . $language_id);
        }
      }
    }

    $data['country'] = $this->country_model->fetch($users_country_id);
    $data['groups'] = $this->country_model->fetch_active_groups();
    $data['nationalities'] = $this->country_model->fetch_active_nationalities_groups();
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('country_add_edit', $data);
  }
  
  function delete()
  {
    $users_country_id = $this->url->segment(3);
    $this->country_model->delete($users_country_id);

    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
    }
  }

}
?>