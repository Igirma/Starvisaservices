<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';

class country_holidays extends admin
{
  function __construct()
  {
    parent::__construct();
    
    $this->lang->load('country');

  }
  
  function index()
  {
    $data = $this->country_holidays_model->fetch_all();

    $this->load->view('country_holidays_overview', $data);
  }
  
  function add()
  {
    $this->form->set_rules('holiday[holiday_name]', 'Holiday name', 'required');

    if($this->form->run())
    {
      $holiday_id = $this->country_holidays_model->add($_POST);

      if(!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
        
        if(isset($_POST['save_and_back']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $holiday_id . '/' . $this->config->item('default_language'));
        }
      }
    }
    
    $data['languages'] = $this->languages_model->fetch_all();
    $data['countries'] = $this->country_holidays_model->get_countries();
    
    $this->load->view('country_holidays_add_edit', $data);
  }
  
  function edit()
  {
    $this->form->set_rules('holiday[holiday_name]', 'Holiday name', 'required');
    
    $holiday_id = $this->url->segment(3);
    
    if($this->form->run())
    {

      $this->country_holidays_model->edit($_POST, $holiday_id);
      
      if(!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
          
        if(isset($_POST['save_and_back']))
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
        else
        {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $holiday_id);
        }
      }
    }
    
    $data['holiday'] = $this->country_holidays_model->fetch($holiday_id);
    $data['languages'] = $this->languages_model->fetch_all();
    $data['countries'] = $this->country_holidays_model->get_countries();

    $this->load->view('country_holidays_add_edit', $data);
  }
  
  function delete()
  {
    $id = $this->url->segment(3);
    
    $this->country_holidays_model->delete($id);
    
    if(!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
    }
  }

}
?>