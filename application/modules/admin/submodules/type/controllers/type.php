<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';

class type extends admin
{
  function __construct()
  {
    parent::__construct();
    $this->lang->load('courses');
  }
  
  function index()
  {
    $data['types'] = $this->type_model->fetch_all();
    $data['languages'] = $this->languages_model->fetch_all();
    $this->load->view('type_overview', $data);
  }

  function add()
  {
    $this->form->set_rules('type[users_type_name]', 'Title', 'required');
    if ($this->form->run()) 
    {
        $users_type_id = $this->type_model->add($_POST['type']);
        if (!$this->db->error) 
        {
            $this->alert->add($this->lang->line('success'), 'success');
            if (isset($_POST['save_and_back'])) {
              $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER);
            } else {
              $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER . '/edit/' . $users_type_id . '/' . $this->config->item('default_language'));
            }
        }
    }
    $data['entries'] = $this->type_model->fetch_entries();
    $data['countries'] = $this->type_model->fetch_countries();
    $data['groups'] = $this->type_model->fetch_country_groups();

    $this->load->view('type_add_edit', $data);
  }

  function edit()
  {
    $this->form->set_rules('type[users_type_name]', 'Title', 'required');

    $users_type_id = $this->url->segment(3);
    $language_id = $this->url->segment(4);

    if ($this->form->run())
    {
      $this->type_model->edit($_POST['type'], $users_type_id);

      if (!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');

        if (isset($_POST['save_and_back'])) {
          $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER);
        } else {
          $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER . '/edit/' . $users_type_id . '/' . $language_id);
        }
      }
    }

    $data['type'] = $this->type_model->fetch($users_type_id);
    $data['entries'] = $this->type_model->fetch_entries();
    $data['countries'] = $this->type_model->fetch_countries();
    $data['groups'] = $this->type_model->fetch_country_groups();
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('type_add_edit', $data);
  }

  function delete()
  {
    $users_type_id = $this->url->segment(3);
    $this->type_model->delete($users_type_id);
    
    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER);
    }
  }
  
  function order()
  {
    $direction = $this->url->segment(3);
    $users_type_order = $this->url->segment(4);
    $users_type_id = $this->url->segment(5);
  
    $this->type_model->order($direction, $users_type_order, $users_type_id);
    
    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
    }
  }
}

?>