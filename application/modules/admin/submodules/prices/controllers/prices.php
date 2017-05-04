<?php

require_once APP_PATH . 'modules/admin/controllers/admin.php';

class prices extends admin
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
    $data['prices'] = $this->prices_model->fetch_all();
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('prices_overview', $data);
  }
  
  function add()
  {
    $this->form->set_rules('price[users_price_name]', 'Name', 'required');
    
    if ($this->form->run())
    {
      $id = $this->prices_model->add($_POST['price']);
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

    $this->load->view('prices_add_edit');
  }

  function edit()
  {
    $this->form->set_rules('price[users_price_name]', 'Name', 'required');
    
    $id = $this->url->segment(3);
    $language_id = $this->url->segment(4);
    
    if ($this->form->run())
    {
      $this->prices_model->edit($_POST['price'], $id);
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

    $data['price'] = $this->prices_model->fetch($id);
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('prices_add_edit', $data);
  }

  function order()
  {
    $direction = $this->url->segment(3);
    $order = $this->url->segment(4);
    $id = $this->url->segment(5);
	
	if($this->prices_model->get_order($id) == 999) {
		$this->alert->add('This action cannot be done.', 'error');
		$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
		return false;
	}
    $this->prices_model->order($direction, $order, $id);
    
    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
    }
  }

  function delete()
  {
    $id = $this->url->segment(3);
	if($this->prices_model->get_order($id) == 999) {
		$this->alert->add('This action cannot be done.', 'error');
		$this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
		return false;
	}
	$this->prices_model->delete($id);

    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
    }
  }

}

?>