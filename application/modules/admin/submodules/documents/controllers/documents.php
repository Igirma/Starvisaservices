<?php
require_once APP_PATH . 'modules/admin/controllers/admin.php';

class documents extends admin
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
      $this->documents_model->update_overview($_POST);
      if (!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
        $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
      }
    }

    $data['documents'] = $this->documents_model->fetch_all();
    $data['languages'] = $this->languages_model->fetch_all();
    $this->load->view('documents_overview', $data);
  }

  function add()
  {
    $this->form->set_rules('document[users_document_title]', 'Document title', 'required');
    $this->form->set_rules('document[users_document_content]', 'Document content', 'required');
    if ($this->form->run()) 
    {
        $users_document_id = $this->documents_model->add($_POST['document']);
        if (!$this->db->error) 
        {
            $this->alert->add($this->lang->line('success'), 'success');
            if (isset($_POST['save_and_back'])) {
              $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER);
            } else {
              $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER . '/edit/' . $users_document_id . '/' . $this->config->item('default_language'));
            }
        }
    }
    //$data['countries'] = $this->documents_model->fetch_countries();
    //$data['groups'] = $this->documents_model->fetch_country_groups();

    $this->load->view('documents_add_edit');
  }

  function edit()
  {
	$page_id = $this->url->segment(3);
	
    $this->form->set_rules('document[users_document_title]', 'Document title', 'required');
    $this->form->set_rules('document[users_document_content]', 'Document content', 'required');

    $users_document_id = $this->url->segment(3);
    $language_id = $this->url->segment(4);

    if ($this->form->run())
    {
      $this->documents_model->edit($_POST['document'], $users_document_id);

      if (!$this->db->error)
      {
        $this->alert->add($this->lang->line('success'), 'success');
		
		$_FILES['docs'] = rearrange_files($_FILES['docs']);

		if(!empty($_FILES['docs']))
		{
		  foreach($_FILES['docs'] as $doc)
		  {
			add_doc($doc);
			$this->admin_model->add_doc($doc, $page_id);
		  }
		}
		
        if (isset($_POST['save_and_back'])) {
          $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER);
		} elseif(isset($_POST['upload'])) {
          $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $page_id . '/' . $language . '/anchor_media');
        } else {
          $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER . '/edit/' . $users_document_id . '/' . $language_id);
        }
      }
    }
	
    $data['document'] = $this->documents_model->fetch($users_document_id);
    //$data['countries'] = $this->documents_model->fetch_countries();
    //$data['groups'] = $this->documents_model->fetch_country_groups();
    $data['languages'] = $this->languages_model->fetch_all();

    $this->load->view('documents_add_edit', $data);
  }

  function delete()
  {
    $users_document_id = $this->url->segment(3);
    $this->documents_model->delete($users_document_id);
    
    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . 'en/admin/' . CONTROLLER);
    }
  }
  
  function order()
  {
    $direction = $this->url->segment(3);
    $users_document_order = $this->url->segment(4);
    $users_document_id = $this->url->segment(5);
  
    $this->documents_model->order($direction, $users_document_order, $users_document_id);

    if (!$this->db->error)
    {
      $this->alert->add($this->lang->line('success'), 'success');
      $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/success');
    }
  }
  
  function delete_media()
    {
        $media_id = $this->url->segment(3);
        $page_id = $this->url->segment(4);
        $language_id = $this->url->segment(5);
        
        $this->documents_model->delete_media($media_id);
        
        if(!$this->db->error) {
            $this->alert->add($this->lang->line('success'), 'success');
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $page_id . '/' . $language_id . '/anchor_media');
        }
    }
}

?>