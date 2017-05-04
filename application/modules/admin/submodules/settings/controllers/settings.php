<?php

require_once('application/modules/admin/controllers/admin.php');

class settings extends admin
{
    function __construct()
    {
        parent::__construct();
        $this->lang->load('settings');
    }
    
    function index()
    {
        $this->form->set_rules('email', $this->lang->line('email'), 'required|valid_email');
        
        if($this->form->run()) {
            $this->settings_model->edit($_POST);
            
            if(!$this->db->error && empty($this->form->_error_messages)) {
                $this->alert->add($this->lang->line('success'), 'success');
                
                if(isset($_POST['save'])) {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/' . CONTROLLER);
                } else {
                    $this->url->redirect(SITE_URL . LANG_CODE . '/admin/');
                }
            }
        }
        $data['setting'] = $this->settings_model->fetch_settings();
        $this->load->view('settings_add_edit', $data);
    }
}

?>