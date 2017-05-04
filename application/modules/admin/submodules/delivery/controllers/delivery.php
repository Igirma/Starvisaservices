<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';

class delivery extends admin
{
  function __construct()
  {
    parent::__construct();
    $this->lang->load('courses');
  }
  
  function index()
  {
    $data['delivery_methods'] = $this->delivery_model->fetch_all();
    $data['languages'] = $this->languages_model->fetch_all();
    $this->load->view('delivery_overview', $data);
  }

  function add()
  {
    $this->form->set_rules('delivery[delivery_method_name]', 'Title', 'required');
    $this->form->set_rules('delivery[delivery_method_price]', 'Price', 'required');
    if ($this->form->run()) 
    {
        $delivery_method_id = $this->delivery_model->add($_POST['delivery']);
        if (!$this->db->error) 
        {
            $this->alert->add($this->lang->line('success'), 'success');
            if (isset($_POST['save_and_back'])) {
              $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER);
            } else {
              $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER . '/edit/' . $delivery_method_id . '/' . $this->config->item('default_language'));
            }
        }
    }
    $this->load->view('delivery_add_edit');
  }
  
  function edit()
  {
    $this->form->set_rules('delivery[delivery_method_name]', 'Title', 'required');
    $this->form->set_rules('delivery[delivery_method_price]', 'Price', 'required');

    $delivery_method_id = $this->url->segment(3);
    $language_id = $this->url->segment(4);

    if ($this->form->run())
    {
      $this->delivery_model->edit($_POST['delivery'], $delivery_method_id);

      if (!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');

        if (isset($_POST['save_and_back'])) {
          $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER);
        } else {
          $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER . '/edit/' . $delivery_method_id . '/' . $language_id);
        }
      }
    }

    $data['delivery'] = $this->delivery_model->fetch($delivery_method_id);
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('delivery_add_edit', $data);
  }

  function delete()
  {
    $delivery_method_id = $this->url->segment(3);
    $this->delivery_model->delete($delivery_method_id);
    
    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER);
    }
  }
}

?>