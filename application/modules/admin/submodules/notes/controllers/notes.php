<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';

class notes extends admin
{
  function __construct()
  {
    parent::__construct();
    $this->lang->load('courses');
  }
  
  function index()
  {
    $this->form->set_rules('active[]', 'Show online?', 'numeric');
    
    if($this->form->run())
    {
      $this->notes_model->update_overview($_POST);
      if (!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }

    $data['notes'] = $this->notes_model->fetch_all();
    $data['languages'] = $this->languages_model->fetch_all();
    $this->load->view('notes_overview', $data);
  }

  function add()
  {
    $this->form->set_rules('note[users_notes_title]', 'Title', 'required');
    $this->form->set_rules('note[users_notes_content]', 'Content', 'required');
    if ($this->form->run()) 
    {
        $users_notes_id = $this->notes_model->add($_POST['note']);
        if (!$this->db->error) 
        {
            $this->alert->add($this->lang->line('success'), 'success');
            if (isset($_POST['save_and_back'])) {
              $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER);
            } else {
              $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER . '/edit/' . $users_notes_id . '/' . $this->config->item('default_language'));
            }
        }
    }
    //$data['countries'] = $this->notes_model->fetch_countries();
    //$data['groups'] = $this->notes_model->fetch_country_groups();

    $this->load->view('notes_add_edit');
  }

  function edit()
  {
    $this->form->set_rules('note[users_notes_title]', 'Title', 'required');
    $this->form->set_rules('note[users_notes_content]', 'Content', 'required');

    $users_notes_id = $this->url->segment(3);
    $language_id = $this->url->segment(4);

    if ($this->form->run())
    {
      $this->notes_model->edit($_POST['note'], $users_notes_id);

      if (!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');

        if (isset($_POST['save_and_back'])) {
          $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER);
        } else {
          $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER . '/edit/' . $users_notes_id . '/' . $language_id);
        }
      }
    }

    $data['note'] = $this->notes_model->fetch($users_notes_id);
    //$data['countries'] = $this->notes_model->fetch_countries();
    //$data['groups'] = $this->notes_model->fetch_country_groups();
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('notes_add_edit', $data);
  }

  function delete()
  {
    $users_notes_id = $this->url->segment(3);
    $this->notes_model->delete($users_notes_id);
    
    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER);
    }
  }

  function order()
  {
    $direction = $this->url->segment(3);
    $users_notes_order = $this->url->segment(4);
    $users_notes_id = $this->url->segment(5);
  
    $this->notes_model->order($direction, $users_notes_order, $users_notes_id);

    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
    }
  }
}

?>