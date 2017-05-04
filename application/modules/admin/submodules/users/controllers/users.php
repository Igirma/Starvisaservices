<?php
require_once('application/modules/admin/controllers/admin.php');

class users extends admin
{
	private $loaded = false;
    function __construct()
    {
		if(!$this->loaded)
			parent::__construct();
		$this->loaded = true;
        
        $this->lang->load('users');
        
    }
    
    function index()
    {
        $this->form->set_rules('active[]', $this->lang->line('active'), 'numeric');
        
        if($this->form->run()) {
            $this->users_model->update_overview($_POST);
            
            if(!$this->db->error) {
                // $this->alert->clear();
                $this->alert->add($this->lang->line('success'), 'success');
                $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
            }
        }
        
        $data = $this->users_model->fetch_all();
        
        $data['languages'] = $this->languages_model->fetch_all();
        
        $this->load->view('users_overview', $data);
    }
    
    function add()
    {
        $this->form->set_rules('username', $this->lang->line('username'), 'required');
        $this->form->set_rules('email', $this->lang->line('email'), 'required|valid_email');
        
        $this->form->set_rules('password', $this->lang->line('password'), 'required');
        $this->form->set_rules('password_check', $this->lang->line('password_check'), 'required|matches[password]');
        
        if($this->form->run()) {
            $this->users_model->add($_POST);
            
            if(!$this->db->error) {
                // $this->alert->clear();
                $this->alert->add($this->lang->line('success'), 'success');
                $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
            }
        }
        
        $data['all_rights'] = $this->users_model->fetch_rights();
        
        $this->load->view('users_add_edit', $data);
    }
    
    function edit()
    {
        $user_id = $this->url->segment(3);
        
        $this->form->set_rules('username', $this->lang->line('username'), 'required');
        $this->form->set_rules('email', $this->lang->line('email'), 'required|valid_email');
        
        if(!empty($_POST['password']) || !empty($_POST['password_check'])) {
            $this->form->set_rules('password', $this->lang->line('password'), 'required');
            $this->form->set_rules('password_check', $this->lang->line('password_check'), 'required|matches[password]');
        }
        
        $data['user'] = $this->users_model->fetch($user_id);
        
        $data['all_rights'] = $this->users_model->fetch_rights();
        
        if($this->form->run()) {
            $this->users_model->edit($_POST);
            
            if(!$this->db->error && empty($this->form->_error_messages)) {
                // $this->alert->clear();
                $this->alert->add($this->lang->line('success'), 'success');
                
                if(isset($_POST['save_and_back'])) {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
                } else {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER . '/edit/' . $user_id);
                }
            }
        }
        
        $this->load->view('users_add_edit', $data);
    }
    
    function delete()
    {
        $this->users_model->delete($this->url->segment(3));
        
        if(!$this->db->error) {
            // $this->alert->clear();
            $this->alert->add($this->lang->line('success'), 'success');
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
    }
    
    function permission()
    {
        $user_id = $this->url->segment(3);
        $language_id = $this->url->segment(4);
        $permission = $this->url->segment(5);
        
        $this->users_model->set_permission($user_id, $language_id, $permission);
        
        if(!$this->db->error) {
            // $this->alert->clear();
            $this->alert->add($this->lang->line('success'), 'success');
            $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
        }
        
    }
    
}

?>